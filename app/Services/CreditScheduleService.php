<?php

namespace App\Services;

use App\Models\User;

class CreditScheduleService
{
    public function rechargeUserCredits(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'REGULAR_USER') {
                $user->credits = 20;
            } elseif ($user->role === 'PREMIUM_USER') {
                $user->credits = 40;
            } elseif ($user->role === 'OWNER') {
                $user->credits = 0;
            }
            $user->save();
        }
    }
}
