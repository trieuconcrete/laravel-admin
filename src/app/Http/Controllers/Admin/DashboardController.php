<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Repositories\Interface\UserRepositoryInterface;

class DashboardController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $now = Carbon::now();

        $users = $this->userRepository->getUsers();

        $totalUsers = $users->count();

        $activeUsers = $users->where('status', 1)->count();

        $inactiveUsers = $users->where('status', 0)->count();

        $usersThisMonth = $users->filter(function ($user) use ($now) {
            return $user->created_at->month === $now->month
                && $user->created_at->year === $now->year;
        })->count();

        $usersToday = $users->filter(function ($user) use ($now) {
            return $user->created_at->isToday();
        })->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'usersThisMonth',
            'usersToday'
        ));
    }
}
