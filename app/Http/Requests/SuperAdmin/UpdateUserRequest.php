<?php

namespace App\Http\Requests\SuperAdmin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends StoreUserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->getKey() : null;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'contact_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'role' => ['sometimes', 'required', Rule::enum(UserRole::class)],
            'branch_id' => $this->branchRules(),
            'status' => ['sometimes', 'required', Rule::enum(UserStatus::class)],
        ];
    }
}
