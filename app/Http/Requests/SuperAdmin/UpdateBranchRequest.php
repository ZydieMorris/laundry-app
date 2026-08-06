<?php

namespace App\Http\Requests\SuperAdmin;

use App\Enums\BranchStatus;
use App\Models\Branch;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends StoreBranchRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $branch = $this->route('branch');
        $branchId = $branch instanceof Branch ? $branch->getKey() : null;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('branches', 'name')->ignore($branchId)],
            'owner' => ['sometimes', 'nullable', 'string', 'max:255'],
            'code' => ['sometimes', 'nullable', 'string', 'max:20', Rule::unique('branches', 'code')->ignore($branchId)],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'required', Rule::enum(BranchStatus::class)],
        ];
    }
}
