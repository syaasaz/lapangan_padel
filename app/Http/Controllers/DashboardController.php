<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $query = Reservation::query()->with('user');

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return view('dashboard', [
            'isAdmin' => $user->isAdmin(),
            'totalReservations' => (clone $query)->count(),
            'pendingReservations' => (clone $query)->where('status', 'Pending')->count(),
            'confirmedReservations' => (clone $query)->where('status', 'Dikonfirmasi')->count(),
            'latestReservations' => (clone $query)->latest()->take(5)->get(),
        ]);
    }
}
