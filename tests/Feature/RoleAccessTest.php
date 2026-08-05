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
    'branch admin' => [UserRole::BranchAdmin, 'branch_admin.dashboard', 'branch_admin/Dashboard'],
    'staff' => [UserRole::Staff, 'staff.dashboard', 'staff/Dashboard'],
    'customer' => [UserRole::Customer, 'customer.dashboard', 'customer/Dashboard'],
]);

test('roles cannot access other role dashboards', function (UserRole $userRole, string $routeName) {
    $this->actingAs(createUserWithRole($userRole));

    $this->get(route($routeName))->assertForbidden();
})->with([
    'super admin cannot access branch admin' => [UserRole::SuperAdmin, 'branch_admin.dashboard'],
    'super admin cannot access staff' => [UserRole::SuperAdmin, 'staff.dashboard'],
    'super admin cannot access customer' => [UserRole::SuperAdmin, 'customer.dashboard'],
    'branch admin cannot access super admin' => [UserRole::BranchAdmin, 'superadmin.dashboard'],
    'branch admin cannot access staff' => [UserRole::BranchAdmin, 'staff.dashboard'],
    'branch admin cannot access customer' => [UserRole::BranchAdmin, 'customer.dashboard'],
    'staff cannot access super admin' => [UserRole::Staff, 'superadmin.dashboard'],
    'staff cannot access branch admin' => [UserRole::Staff, 'branch_admin.dashboard'],
    'staff cannot access customer' => [UserRole::Staff, 'customer.dashboard'],
    'customer cannot access super admin' => [UserRole::Customer, 'superadmin.dashboard'],
    'customer cannot access branch admin' => [UserRole::Customer, 'branch_admin.dashboard'],
    'customer cannot access staff' => [UserRole::Customer, 'staff.dashboard'],
]);

test('guests are redirected to login when visiting role dashboards', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with([
    'superadmin' => 'superadmin.dashboard',
    'branch admin' => 'branch_admin.dashboard',
    'staff' => 'staff.dashboard',
    'customer' => 'customer.dashboard',
]);
