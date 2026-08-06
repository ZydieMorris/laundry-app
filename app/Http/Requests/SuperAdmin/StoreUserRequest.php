<?php

namespace App\Http\Requests\SuperAdmin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', Rule::enum(UserRole::class), Rule::notIn([UserRole::Customer->value])],
            'branch_id' => $this->branchRules(),
            'status' => ['sometimes', Rule::enum(UserStatus::class)],
        ];
    }

    /**
     * Get the branch validation rules based on the selected role.
     *
     * @return array<int, mixed>
     */
    protected function branchRules(): array
    {
        $role = $this->input('role');

        return [
            'integer',
            'exists:branches,id',
            Rule::requiredIf(in_array($role, [UserRole::BranchAdmin->value, UserRole::Staff->value], true)),
            Rule::prohibitedIf(in_array($role, [UserRole::SuperAdmin->value, UserRole::Customer->value], true)),
        ];
    }

    /**
     * Get custom validation attributes.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'branch_id' => 'branch',
        ];
    }
}
