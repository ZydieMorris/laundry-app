<?php

use App\Enums\BranchStatus;
use App\Enums\UserRole;
use App\Models\Branch;
use Inertia\Testing\AssertableInertia as Assert;

test('super admin can view the branches page', function () {
    $branch = Branch::factory()->create();

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->get(route('superadmin.branches.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('superadmin/Branches')
            ->has('branches.data', 1)
            ->where('branches.data.0.id', $branch->id));
});

test('branches page supports searching by name, code and email', function (array $filters) {
    $match = Branch::factory()->create([
        'name' => 'Downtown Laundry',
        'code' => 'BR-DOWNTOWN',
        'email' => 'downtown@laundry.test',
    ]);

    Branch::factory()->create([
        'name' => 'Uptown Fresh',
        'code' => 'BR-UPTOWN',
        'email' => 'uptown@laundry.test',
    ]);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->get(route('superadmin.branches.index', $filters))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('branches.data', 1)
            ->where('branches.data.0.id', $match->id)
            ->where('filters.search', $filters['search']));
})->with([
    'by name' => [['search' => 'Downtown']],
    'by code' => [['search' => 'BR-DOWNTOWN']],
    'by email' => [['search' => 'downtown@laundry.test']],
]);

test('branches page is paginated', function () {
    Branch::factory()->count(15)->create();

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->get(route('superadmin.branches.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('branches.data', 10)
            ->where('branches.total', 15)
            ->where('branches.current_page', 1));

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->get(route('superadmin.branches.index', ['page' => 2]))
        ->assertInertia(fn (Assert $page) => $page
            ->has('branches.data', 5)
            ->where('branches.current_page', 2));
});

test('super admin can create a branch', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.branches.store'), [
            'name' => 'Downtown Laundry',
            'code' => 'BR-DOWNTOWN',
            'contact_number' => '+1 555-0101',
            'email' => 'downtown@laundry.test',
            'address' => '123 Main Street',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('inertia.flash_data.toast', ['type' => 'success', 'message' => 'Branch created.']);

    $this->assertDatabaseHas('branches', [
        'name' => 'Downtown Laundry',
        'code' => 'BR-DOWNTOWN',
        'status' => 'active',
    ]);
});

test('branch code is auto-generated when not provided', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.branches.store'), ['name' => 'Auto Code Branch'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $branch = Branch::where('name', 'Auto Code Branch')->firstOrFail();

    expect($branch->code)->toMatch('/^BR-[A-Z0-9]{6}$/');
});

test('branch code is normalized to uppercase', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.branches.store'), [
            'name' => 'Lowercase Code Branch',
            'code' => 'br-lower',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('branches', ['code' => 'BR-LOWER']);
});

test('branch name is required', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.branches.store'), [])
        ->assertSessionHasErrors('name');
});

test('branch name must be unique', function () {
    Branch::factory()->create(['name' => 'Downtown Laundry']);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.branches.store'), ['name' => 'Downtown Laundry'])
        ->assertSessionHasErrors('name');
});

test('branch code must be unique when provided', function () {
    Branch::factory()->create(['code' => 'BR-DUPLICATE']);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.branches.store'), [
            'name' => 'Other Laundry',
            'code' => 'BR-DUPLICATE',
        ])
        ->assertSessionHasErrors('code');
});

test('super admin can update a branch', function () {
    $branch = Branch::factory()->create(['name' => 'Old Name']);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->put(route('superadmin.branches.update', $branch), [
            'name' => 'New Name',
            'code' => $branch->code,
            'status' => 'inactive',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('inertia.flash_data.toast', ['type' => 'success', 'message' => 'Branch updated.']);

    $branch->refresh();

    expect($branch->name)->toBe('New Name');
    expect($branch->status)->toBe(BranchStatus::Inactive);
});

test('branch can keep its own name and code during update', function () {
    $branch = Branch::factory()->create(['name' => 'Same Name', 'code' => 'BR-SAME']);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->put(route('superadmin.branches.update', $branch), [
            'name' => 'Same Name',
            'code' => 'BR-SAME',
            'status' => 'active',
        ])
        ->assertSessionHasNoErrors();
});

test('non super admin roles cannot manage branches', function (UserRole $role) {
    $branch = Branch::factory()->create();

    $this->actingAs(createUserWithRole($role))
        ->get(route('superadmin.branches.index'))
        ->assertForbidden();

    $this->actingAs(createUserWithRole($role))
        ->post(route('superadmin.branches.store'), ['name' => 'X'])
        ->assertForbidden();

    $this->actingAs(createUserWithRole($role))
        ->put(route('superadmin.branches.update', $branch), [
            'name' => 'X',
            'code' => 'BR-X',
            'status' => 'active',
        ])
        ->assertForbidden();
})->with([
    'branch admin' => [UserRole::BranchAdmin],
    'staff' => [UserRole::Staff],
    'customer' => [UserRole::Customer],
]);

test('guests are redirected to login when visiting branches', function () {
    $this->get(route('superadmin.branches.index'))->assertRedirect(route('login'));
});
