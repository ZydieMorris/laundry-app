<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Branch;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('super admin can view the users page', function () {
    $branch = Branch::factory()->create();

    $user = createUserWithRole(UserRole::BranchAdmin);
    $user->forceFill(['branch_id' => $branch->id])->save();
    $user->created_at = now()->subMinute();
    $user->save();

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->get(route('superadmin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('superadmin/Users')
            ->has('users.data', 2)
            ->where('users.data.1.id', $user->id)
            ->where('users.data.1.branch.id', $branch->id)
            ->has('branches'));
});

test('users page supports searching by name, email and contact number', function (array $filters) {
    $match = User::factory()->create([
        'name' => 'Maria Santos',
        'email' => 'maria@laundry.test',
        'contact_number' => '+1 555-0101',
    ]);
    $match->assignRole(UserRole::BranchAdmin->value);

    $other = User::factory()->create(['name' => 'Sofia Reyes']);
    $other->assignRole(UserRole::Staff->value);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->get(route('superadmin.users.index', $filters))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.id', $match->id)
            ->where('filters.search', $filters['search']));
})->with([
    'by name' => [['search' => 'Maria']],
    'by email' => [['search' => 'maria@laundry.test']],
    'by contact number' => [['search' => '+1 555-0101']],
]);

test('users page supports filtering by role, branch and status', function (string $filter, string $expectedName) {
    $branch = Branch::factory()->create(['name' => 'Downtown Laundry']);

    $match = User::factory()->create(['name' => 'Match User', 'status' => UserStatus::Active]);
    $match->assignRole(UserRole::BranchAdmin->value);
    $match->update(['branch_id' => $branch->id]);

    $other = User::factory()->inactive()->create(['name' => 'Other User']);
    $other->assignRole(UserRole::Staff->value);

    $filters = match ($filter) {
        'role' => ['role' => 'branch_admin'],
        'branch' => ['branch_id' => $branch->id],
        'status' => ['status' => 'inactive'],
    };

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->get(route('superadmin.users.index', $filters))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.name', $expectedName));
})->with([
    'by role' => ['role', 'Match User'],
    'by branch' => ['branch', 'Match User'],
    'by status' => ['status', 'Other User'],
]);

test('users page is paginated', function () {
    $superAdmin = createUserWithRole(UserRole::SuperAdmin);

    foreach (range(1, 15) as $i) {
        $user = User::factory()->create();
        $user->assignRole(UserRole::Staff->value);
    }

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 10)
            ->where('users.total', 16)
            ->where('users.current_page', 1));

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users.index', ['page' => 2]))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 6)
            ->where('users.current_page', 2));
});

test('super admin can create a branch admin user with a branch', function () {
    $branch = Branch::factory()->create();

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.users.store'), [
            'name' => 'New Branch Admin',
            'email' => 'newadmin@laundry.test',
            'contact_number' => '+1 555-0202',
            'password' => 'password',
            'role' => UserRole::BranchAdmin->value,
            'branch_id' => $branch->id,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('inertia.flash_data.toast', ['type' => 'success', 'message' => 'User created.']);

    $user = User::where('email', 'newadmin@laundry.test')->firstOrFail();

    expect($user->hasRole(UserRole::BranchAdmin))->toBeTrue();
    expect($user->branch_id)->toBe($branch->id);
    expect($user->status)->toBe(UserStatus::Active);
});

test('super admin can create a super admin user without a branch', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.users.store'), [
            'name' => 'Second Super Admin',
            'email' => 'super2@laundry.test',
            'password' => 'password',
            'role' => UserRole::SuperAdmin->value,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $user = User::where('email', 'super2@laundry.test')->firstOrFail();

    expect($user->hasRole(UserRole::SuperAdmin))->toBeTrue();
    expect($user->branch_id)->toBeNull();
});

test('branch is required for staff users', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.users.store'), [
            'name' => 'No Branch Staff',
            'email' => 'nobranch@laundry.test',
            'password' => 'password',
            'role' => UserRole::Staff->value,
        ])
        ->assertSessionHasErrors('branch_id');
});

test('branch is prohibited for super admin users', function () {
    $branch = Branch::factory()->create();

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.users.store'), [
            'name' => 'Branchless Super Admin',
            'email' => 'nosuper@laundry.test',
            'password' => 'password',
            'role' => UserRole::SuperAdmin->value,
            'branch_id' => $branch->id,
        ])
        ->assertSessionHasErrors('branch_id');
});

test('customer users cannot be created from this module', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.users.store'), [
            'name' => 'Customer Attempt',
            'email' => 'customer@laundry.test',
            'password' => 'password',
            'role' => UserRole::Customer->value,
        ])
        ->assertSessionHasErrors('role');
});

test('user name is required', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.users.store'), [
            'email' => 'noname@laundry.test',
            'password' => 'password',
            'role' => UserRole::Staff->value,
            'branch_id' => Branch::factory()->create()->id,
        ])
        ->assertSessionHasErrors('name');
});

test('user email must be unique', function () {
    User::factory()->create(['email' => 'taken@laundry.test']);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.users.store'), [
            'name' => 'Taken Email',
            'email' => 'taken@laundry.test',
            'password' => 'password',
            'role' => UserRole::Staff->value,
            'branch_id' => Branch::factory()->create()->id,
        ])
        ->assertSessionHasErrors('email');
});

test('password is required during creation', function () {
    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->post(route('superadmin.users.store'), [
            'name' => 'No Password',
            'email' => 'nopass@laundry.test',
            'role' => UserRole::Staff->value,
            'branch_id' => Branch::factory()->create()->id,
        ])
        ->assertSessionHasErrors('password');
});

test('super admin can update a user', function () {
    $branch = Branch::factory()->create();
    $user = createUserWithRole(UserRole::BranchAdmin);
    $user->update(['branch_id' => $branch->id]);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->put(route('superadmin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@laundry.test',
            'contact_number' => '+1 555-0303',
            'role' => UserRole::BranchAdmin->value,
            'branch_id' => $branch->id,
            'status' => UserStatus::Active->value,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('inertia.flash_data.toast', ['type' => 'success', 'message' => 'User updated.']);

    $user->refresh();

    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@laundry.test');
    expect($user->email_verified_at)->toBeNull();
    expect($user->contact_number)->toBe('+1 555-0303');
});

test('super admin can change a user role and branch', function () {
    $branch = Branch::factory()->create();
    $user = createUserWithRole(UserRole::BranchAdmin);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->put(route('superadmin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => UserRole::Staff->value,
            'branch_id' => $branch->id,
            'status' => UserStatus::Active->value,
        ])
        ->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->hasRole(UserRole::Staff))->toBeTrue();
    expect($user->hasRole(UserRole::BranchAdmin))->toBeFalse();
    expect($user->branch_id)->toBe($branch->id);
});

test('status can be toggled with a partial update', function () {
    $user = createUserWithRole(UserRole::Staff);
    $user->update(['branch_id' => Branch::factory()->create()->id]);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->put(route('superadmin.users.update', $user), [
            'status' => UserStatus::Inactive->value,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($user->refresh()->status)->toBe(UserStatus::Inactive);
});

test('super admin cannot deactivate their own account', function () {
    $superAdmin = createUserWithRole(UserRole::SuperAdmin);

    $this->actingAs($superAdmin)
        ->put(route('superadmin.users.update', $superAdmin), [
            'status' => UserStatus::Inactive->value,
        ])
        ->assertSessionHasErrors('status');

    expect($superAdmin->refresh()->status)->toBe(UserStatus::Active);
});

test('super admin can delete a user', function () {
    $user = createUserWithRole(UserRole::Staff);
    $user->update(['branch_id' => Branch::factory()->create()->id]);

    $this->actingAs(createUserWithRole(UserRole::SuperAdmin))
        ->delete(route('superadmin.users.destroy', $user))
        ->assertRedirect()
        ->assertSessionHasNoErrors()
        ->assertSessionHas('inertia.flash_data.toast', ['type' => 'success', 'message' => 'User deleted.']);

    expect(User::find($user->id))->toBeNull();
});

test('super admin cannot delete their own account', function () {
    $superAdmin = createUserWithRole(UserRole::SuperAdmin);

    $this->actingAs($superAdmin)
        ->delete(route('superadmin.users.destroy', $superAdmin))
        ->assertSessionHasErrors('user');

    expect(User::find($superAdmin->id))->not->toBeNull();
});

test('inactive users can sign in but are restricted to the dashboard', function () {
    $user = createUserWithRole(UserRole::Staff);
    $user->update([
        'branch_id' => Branch::factory()->create()->id,
        'status' => UserStatus::Inactive,
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('staff.dashboard'));

    $this->assertAuthenticatedAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('staff.dashboard'));
    $this->get(route('staff.dashboard'))->assertOk();

    $this->get(route('profile.edit'))->assertRedirect(route('dashboard'));
    $this->patch(route('profile.update'))->assertRedirect(route('dashboard'))->assertStatus(303);
    $this->put(route('user-password.update'))->assertRedirect(route('dashboard'))->assertStatus(303);
});

test('inactive super admins are restricted to the dashboard', function () {
    $superAdmin = createUserWithRole(UserRole::SuperAdmin);
    $superAdmin->update(['status' => UserStatus::Inactive]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.users.index'))
        ->assertRedirect(route('dashboard'));

    $this->actingAs($superAdmin)
        ->post(route('superadmin.users.store'), [
            'name' => 'X',
            'email' => 'x@laundry.test',
            'password' => 'password',
            'role' => UserRole::Staff->value,
            'branch_id' => Branch::factory()->create()->id,
        ])
        ->assertRedirect(route('dashboard'))
        ->assertStatus(303);

    expect(User::where('email', 'x@laundry.test')->exists())->toBeFalse();
});

test('non super admin roles cannot manage users', function (UserRole $role) {
    $branch = Branch::factory()->create();
    $staff = createUserWithRole(UserRole::Staff);
    $staff->update(['branch_id' => $branch->id]);

    $this->actingAs(createUserWithRole($role))
        ->get(route('superadmin.users.index'))
        ->assertForbidden();

    $this->actingAs(createUserWithRole($role))
        ->post(route('superadmin.users.store'), [
            'name' => 'X',
            'email' => 'x@laundry.test',
            'password' => 'password',
            'role' => UserRole::Staff->value,
            'branch_id' => $branch->id,
        ])
        ->assertForbidden();

    $this->actingAs(createUserWithRole($role))
        ->put(route('superadmin.users.update', $staff), [
            'name' => 'X',
            'status' => UserStatus::Active->value,
        ])
        ->assertForbidden();

    $this->actingAs(createUserWithRole($role))
        ->delete(route('superadmin.users.destroy', $staff))
        ->assertForbidden();
})->with([
    'branch admin' => [UserRole::BranchAdmin],
    'staff' => [UserRole::Staff],
    'customer' => [UserRole::Customer],
]);

test('guests are redirected to login when visiting users', function () {
    $this->get(route('superadmin.users.index'))->assertRedirect(route('login'));
});
