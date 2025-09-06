<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $items = Notification::where('user_id', $user->id)->get();
        return response()->json(['notifications' => $items]);
    }

    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        $notif = Notification::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $notif->is_read = true;
        $notif->save();
        return response()->json(['message' => 'marked']);
    }
}
