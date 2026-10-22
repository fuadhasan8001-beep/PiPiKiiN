<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Show different dashboards based on role
        if ($user->role === 'admin') {
            return view('adventure.dashboard.admin');
        }

        if ($user->role === 'teacher') {
            return view('adventure.dashboard.teacher');
        }

        // Guest/Player dashboard
        return view('adventure.dashboard.guest');
    }
}