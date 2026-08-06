<?php

namespace App\Http\Requests\SuperAdmin;

use App\Enums\BranchStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreBranchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->filled('code') ? Str::upper(trim($this->input('code'))) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:branches,name'],
            'owner' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20', 'unique:branches,code'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', Rule::enum(BranchStatus::class)],
        ];
    }
}
