<?php

use App\Enums\UserRole;
use Inertia\Testing\AssertableInertia as Assert;

test('each role can visit its own dashboard', function (UserRole $role, string $routeName, string $component) {
    $this->actingAs(createUserWithRole($role));

    $this->get(route($routeName))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    'super admin' => [UserRole::SuperAdmin, 'superadmin.dashboard', 'superadmin/Dashboard'],
    'admin' => [UserRole::Admin, 'admin.dashboard', 'admin/Dashboard'],
    'staff' => [UserRole::Staff, 'staff.dashboard', 'staff/Dashboard'],
    'customer' => [UserRole::Customer, 'customer.dashboard', 'customer/Dashboard'],
]);

test('roles cannot access other role dashboards', function (UserRole $userRole, string $routeName) {
    $this->actingAs(createUserWithRole($userRole));

    $this->get(route($routeName))->assertForbidden();
})->with([
    'super admin cannot access admin' => [UserRole::SuperAdmin, 'admin.dashboard'],
    'super admin cannot access staff' => [UserRole::SuperAdmin, 'staff.dashboard'],
    'super admin cannot access customer' => [UserRole::SuperAdmin, 'customer.dashboard'],
    'admin cannot access super admin' => [UserRole::Admin, 'superadmin.dashboard'],
    'admin cannot access staff' => [UserRole::Admin, 'staff.dashboard'],
    'admin cannot access customer' => [UserRole::Admin, 'customer.dashboard'],
    'staff cannot access super admin' => [UserRole::Staff, 'superadmin.dashboard'],
    'staff cannot access admin' => [UserRole::Staff, 'admin.dashboard'],
    'staff cannot access customer' => [UserRole::Staff, 'customer.dashboard'],
    'customer cannot access super admin' => [UserRole::Customer, 'superadmin.dashboard'],
    'customer cannot access admin' => [UserRole::Customer, 'admin.dashboard'],
    'customer cannot access staff' => [UserRole::Customer, 'staff.dashboard'],
]);

test('guests are redirected to login when visiting role dashboards', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with([
    'superadmin' => 'superadmin.dashboard',
    'admin' => 'admin.dashboard',
    'staff' => 'staff.dashboard',
    'customer' => 'customer.dashboard',
]);
