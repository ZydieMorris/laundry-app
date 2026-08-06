<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $search = trim((string) $request->query('search', ''));
        $role = trim((string) $request->query('role', ''));
        $branchId = (int) $request->query('branch_id', 0) ?: null;
        $status = trim((string) $request->query('status', ''));

        $users = User::query()
            ->with(['branch:id,name', 'roles:id,name'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->when($role !== '' && UserRole::tryFrom($role) !== null, function ($query) use ($role) {
                $query->whereHas('roles', fn ($query) => $query->where('name', $role));
            })
            ->when($branchId !== null, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($status !== '' && UserStatus::tryFrom($status) !== null, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('superadmin/Users', [
            'users' => $users,
            'branches' => Branch::query()->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'search' => $search,
                'role' => $role,
                'branch_id' => $branchId,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $role = $data['role'];
        unset($data['role']);

        if (in_array($role, [UserRole::SuperAdmin->value, UserRole::Customer->value], true)) {
            $data['branch_id'] = null;
        }

        $data['status'] ??= UserStatus::Active->value;

        User::create($data)->syncRoles($role);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User created.')]);

        return back();
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($user->is($request->user()) && $request->input('status') === UserStatus::Inactive->value) {
            throw ValidationException::withMessages([
                'status' => __('You cannot deactivate your own account.'),
            ]);
        }

        $data = $request->validated();

        if (isset($data['role'])) {
            $role = $data['role'];
            unset($data['role']);

            if (in_array($role, [UserRole::SuperAdmin->value, UserRole::Customer->value], true)) {
                $data['branch_id'] = null;
            }

            $user->syncRoles($role);
        }

        if (isset($data['email']) && $user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

        $user->fill($data)->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User updated.')]);

        return back();
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->is($request->user())) {
            throw ValidationException::withMessages([
                'user' => __('You cannot delete your own account.'),
            ]);
        }

        $user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User deleted.')]);

        return back();
    }
}
