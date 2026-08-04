<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the super admin dashboard.
     */
    public function index(): Response
    {
        return Inertia::render('superadmin/Dashboard');
    }
}