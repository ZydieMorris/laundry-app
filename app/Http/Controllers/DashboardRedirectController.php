<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    /**
     * Redirect the authenticated user to their role's dashboard.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $role = $request->user()?->role();

        return redirect($role ? route($role->homeRoute()) : route('home'));
    }
}