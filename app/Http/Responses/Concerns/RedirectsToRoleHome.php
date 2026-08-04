<?php

namespace App\Http\Responses\Concerns;

use Illuminate\Http\Request;

trait RedirectsToRoleHome
{
    /**
     * Resolve the URL to redirect the authenticated user to based on their role.
     */
    protected function roleHome(Request $request): string
    {
        $role = $request->user()?->role();

        return $role ? route($role->homeRoute()) : route('dashboard');
    }
}