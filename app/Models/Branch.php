<?php

namespace App\Models;

use App\Enums\BranchStatus;
use Database\Factories\BranchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $owner
 * @property string $code
 * @property string|null $address
 * @property BranchStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'owner', 'code', 'address', 'status'])]
class Branch extends Model
{
    /** @use HasFactory<BranchFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => BranchStatus::class,
        ];
    }
}
