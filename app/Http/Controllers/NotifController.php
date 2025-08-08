<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class NotifController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $notifications = Notifikasi::where('id_user', $user->id)
            ->orderBy('updated_at', 'desc')
            // ->limit(10)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    // Ambil jumlah notifikasi yang belum dibaca
    public function getUnreadCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $count = Notifikasi::where('id_user', $user->id)
            ->where('dibaca', 0)
            ->count();

        return response()->json(['count' => $count]);
    }

    // Tandai semua notifikasi sebagai sudah dibaca
    public function markAllAsRead()
    {
        $user = Auth::user();
        if ($user) {
            Notifikasi::where('id_user', $user->id)
                ->where('dibaca', 0)
                ->update(['dibaca' => 1]);
        }
        return response()->json(['success' => true]);
    }

}
