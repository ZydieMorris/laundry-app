<?php

use App\Enums\UserRole;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users are redirected to their role dashboard', function (UserRole $role, string $routeName) {
    $this->actingAs(createUserWithRole($role));

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route($routeName));
})->with([
    'super admin' => [UserRole::SuperAdmin, 'superadmin.dashboard'],
    'branch admin' => [UserRole::BranchAdmin, 'branch_admin.dashboard'],
    'staff' => [UserRole::Staff, 'staff.dashboard'],
    'customer' => [UserRole::Customer, 'customer.dashboard'],
]);
