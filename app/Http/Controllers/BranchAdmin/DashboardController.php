<?php

namespace App\Http\Controllers\BranchAdmin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the branch admin dashboard.
     */
    public function index(): Response
    {
        return Inertia::render('branch_admin/Dashboard');
    }
}
