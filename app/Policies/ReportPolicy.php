<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    public function create(User $user): Response
    {
        return $user->hasPermission('report.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create report');
    }
}
