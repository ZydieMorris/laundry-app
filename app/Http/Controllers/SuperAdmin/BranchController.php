<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreBranchRequest;
use App\Http\Requests\SuperAdmin\UpdateBranchRequest;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    /**
     * Display a listing of the branches.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Branch::class);

        $search = trim((string) $request->query('search', ''));

        $branches = Branch::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('superadmin/Branches', [
            'branches' => $branches,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $this->authorize('create', Branch::class);

        $data = $request->validated();
        $data['code'] ??= $this->generateUniqueCode();
        $data['status'] ??= 'active';

        Branch::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Branch created.')]);

        return back();
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $this->authorize('update', $branch);

        $data = $request->validated();
        $data['code'] ??= $this->generateUniqueCode();

        $branch->fill($data)->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Branch updated.')]);

        return back();
    }

    /**
     * Generate a unique branch code in the format BR-XXXXXX.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = 'BR-'.Str::upper(Str::random(6));
        } while (Branch::where('code', $code)->exists());

        return $code;
    }
}
