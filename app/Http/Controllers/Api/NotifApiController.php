<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class NotifApiController extends Controller
{
    // Ambil 10 notifikasi terakhir untuk divisi user yang login
    public function index(Request $request)
    {
        $user = $request->user(); 
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Log untuk debugging
        \Log::info('Ambil notifikasi', [
            'user_id' => $user->id,
            'divisi_id_divisi' => $user->divisi_id_divisi,
            'role' => $user->role ?? null,
        ]);

        // Ambil notifikasi berdasarkan divisi user
        $notifications = Notifikasi::where('id_user', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notif) {
                return [
                    'id_notifikasi' => $notif->id_notifikasi,
                    'judul' => $notif->judul,
                    'judul_document' => $notif->judul_document,
                    'id_user' => $notif->id_user,
                    'updated_at' => $notif->updated_at,
                    'dibaca' => (bool) $notif->dibaca,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Daftar 10 notifikasi terbaru',
            'data' => $notifications
        ]);
    }

    // Hitung jumlah notifikasi yang belum dibaca
    public function getUnreadCount(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'count' => 0
            ], 401);
        }

        $count = Notifikasi::where('id_user', $user->id)
            ->where('dibaca', 0)
            ->count();

        return response()->json([
            'status' => true,
            'count' => $count
        ]);
    }

    // Tandai semua notifikasi sebagai sudah dibaca
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $updated = Notifikasi::where('id_user', $user->id)
            ->where('dibaca', 0)
            ->update(['dibaca' => 1]);

        return response()->json([
            'status' => true,
            'message' => "Semua notifikasi ($updated) ditandai sudah dibaca"
        ]);
    }
}
