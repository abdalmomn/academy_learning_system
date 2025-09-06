<?php

namespace App\Services;

use App\DTO\PaymentDto;
use App\Events\Notification\PaymentCompleted;
use App\Events\Notification\PaymentFailed;
use App\Events\UserRegistered;
use App\Models\Category;
use App\Models\Course;
use App\Models\PromoCode;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class CheckoutService
{

    public function checkout_page($request)
    {
        $course = Course::query()
            ->where('id',$request['course_id'])
            ->select('id','course_name','description', 'price','start_date','end_date','user_id','category_id')
            ->first();
        $course['teacher_name'] = User::query()
            ->where('id', $course->user_id)
            ->select(['username as teacher_name'])
            ->first();
        $category = Category::query()->find($course->category_id);
        $course['category_name'] = $category->category_name;
        $user = User::query()
            ->where('id',Auth::id())
            ->select('id','username','email')
            ->first();
        $wallet = Wallet::query()
            ->where('user_id',Auth::id())
            ->select('id','balance')
            ->first();
        unset($course['user_id']);unset($course['category_id']);
        if (!$user || !$course || !$wallet){
            Log::warning('checkout page missing data', [
                'user' => $user,
                'course' => $course, 'wallet' => $wallet]
            );
            return [
                'data' => null,
                'message' => 'there is no information'
            ];
        }
        return [
            'data' => [
                'course' => $course,
                'wallet' => $wallet,
                'user' => $user
            ],
            'message' => 'return data successfully'
        ];
    }

    public function checkout(PaymentDto $paymentDto): array
    {
        $user = Auth::user();
        $user_id = Auth::id();
        if (!$user){
            return [
                'data' => null,
                'message' => 'user not found'
            ];
        }
        $user = User::query()->where('id',$user_id)->first();
//        if ($user->email_verified_at == null){
//            return [
//                'data' => null,
//                'message' => 'verify your account first'
//            ];
//        }
        if (!$user->hasRole(['child','woman'])){
            return [
                'data' => null,
                'message' => 'children and females only'
            ];

        }
        $course = Course::query()->find($paymentDto->course_id);
        if (!$course){
            return [
                'data' => null,
                'message' => 'course not found'
            ];
        }
        if ($course->price == 0){
            if (!$course->user()->where('user_id', $user_id)->exists()) {
                $course->user()->attach($user_id, [
                    'is_completed' => false,
                    'certificate_id' => null
                ]);
            };
            return [
                'data' => null,
                'message' => 'enrolled in course successfully'
            ];
        }

            $wallet = Wallet::query()
                ->where('user_id',$user_id)
                ->first();
        $initial_price = $course->price;

        if ($paymentDto->promo_code) {
            $promo_code = PromoCode::query()
                    ->where('promo_code',$paymentDto->promo_code)
                    ->first();
            if (!$promo_code) {
                return [
                    'data' => null,
                    'message' => 'promo code not found'
                ];
            }
            if ($promo_code->expires_in < Carbon::now()){
                return [
                    'data' => null,
                    'message' => 'the promo code is expired'
                ];
            }
            $total_price = $initial_price - ($initial_price * ($promo_code->discount_percentage/100));
            if ($promo_code->usage_limit <= 0){
                return [
                    'data' => null,
                    'message' => 'the promo code has reached its limit'
                ];
            }
            $promo_code->usage_limit--;
            $promo_code->save();
        }else{
            $total_price = $initial_price;
        }

        if ($paymentDto->payment_method == 'points'){
            if ($total_price > $wallet->balance){
                Transaction::query()->create([
                            'amount' => $total_price,
                            'description' => 'there is no enough wallet points to complete this transaction',
                            'status' => 'failed',
                            'transaction_type' => 'credit',
                            'transaction_method' => $paymentDto->payment_method,
                            'user_id' => $user_id,
                    ]);

                return [
                    'data' => null,
                    'message' => 'there is no enough points to complete this process'
                ];
            }

            if ($course->user()->where('course_id',$course->id)->exists()){
                return [
                    'data' => null,
                    'message' => 'you have paid for this course already'
                ];
            }

            $wallet->balance -= $total_price;
            $wallet->save();
            $wallet->refresh();
            if (!$course->user()->where('user_id', $user_id)->exists()) {
                $course->user()->attach($user_id, [
                    'is_completed' => false,
                    'certificate_id' => null
                ]);
            }
            Transaction::query()
                ->create([
                    'amount' => $total_price,
                    'description' => 'paid for course by points',
                    'status' => 'completed',
                    'transaction_type' => 'credit',
                    'transaction_method' => $paymentDto->payment_method,
                    'user_id' => $user_id,
                ]);

            Log::info('payment success with points', [
                'user_id' => $user_id,
                'course_id' => $course->id]
            );
            return [
                'data' => [$course,$wallet],
                'message' => 'course paid successfully with points'
            ];
        }


        //stripe
        $stripe_session = 0;
        if ($paymentDto->payment_method == 'stripe'){
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $stripe_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'EUR',
                        'product_data' => [
                            'name' => $course->course_name,
                        ],
                        'unit_amount' => $total_price * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
                'metadata' => [
                    'user_id' => $user_id,
                    'course_id' => $course->id,
                ],
            ]);
            return [
                'data' => [
                    'checkout_url' => $stripe_session->url
                ],
                'message' => 'redirect to stripe payment'
            ];
        }

        Transaction::query()
            ->create([
                'amount' => $total_price,
                'description' => 'paid for course with by stripe',
                'status' => 'completed',
                'transaction_type' => 'credit',
                'transaction_method' => $paymentDto->payment_method,
                'user_id' => $user_id,
            ]);
        if (!$course->user()->where('user_id', $user_id)->exists()) {
            $course->user()->attach($user_id, [
                'is_completed' => false,
                'certificate_id' => null
            ]);
        }


        Log::info('checkout success with stripe', [
            'user_id' => $user_id,
            'course_id' => $course->id]
        );
        return [
            'data' => [
                'course' => $course,
                'wallet' => $wallet,

            ],
            'message' => 'course paid    successfully'
        ];
    }


    public function stripe_success($request): array
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->retrieve($request->get('session_id'));

        $user_id = $session->metadata->user_id;
        $course_id = $session->metadata->course_id;

        $course = Course::query()->find($course_id);

        if (!$course->user()->where('user_id', $user_id)->exists()) {
            $course->user()->attach($user_id, [
                'is_completed' => false,
                'certificate_id' => null
            ]);
        }

        $existing = Transaction::where('description', 'paid for course with stripe')
            ->where('user_id', $user_id)
            ->where('amount', $session->amount_total / 100)
            ->first();

        if ($existing) {
            return [
                'data' => null,
                'message' => 'transaction already processed'
            ];
        }

        Transaction::query()->create([
            'amount' => $session->amount_total / 100,
            'description' => 'paid for course with stripe',
            'status' => 'completed',
            'transaction_type' => 'credit',
            'transaction_method' => 'stripe',
            'user_id' => $user_id,
        ]);


        return [
            'data' => null,
            'message' => 'processing payment successfully'
        ];
    }
    public function stripe_cancel(): array
    {
        return [
          'data' => null,
          'message' => 'processing payment failure'
        ];
    }
}
