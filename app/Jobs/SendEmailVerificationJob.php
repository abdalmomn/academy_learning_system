<?php

namespace App\Jobs;

use App\Mail\VerificationEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendEmailVerificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $user;
    public $signedUrl;
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Mail::to($this->user->email)->send(new VerificationEmail($this->user,$this->signedUrl));
    }
//    public function failed(\Exception $exception)
//    {
//        Log::error('Verification email failed for user: ' . $this->user->email, [
//            'error' => $exception->getMessage()
//        ]);
//
//        // Optionally, notify an admin via email
//        Mail::raw("Email verification failed for {$this->user->email}: {$exception->getMessage()}", function ($message) {
//            $message->to('admin@example.com')
//                ->subject('Email Verification Job Failed');
//        });
//    }
}
