<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the staff dashboard.
     */
    public function index(): Response
    {
        return Inertia::render('staff/Dashboard');
    }
}