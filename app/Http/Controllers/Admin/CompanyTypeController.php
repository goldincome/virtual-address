<?php
namespace App\Http\Controllers\Admin;

use App\Models\CompanyType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyTypeController extends Controller
{
    public function index()
    {
        $companyTypes = CompanyType::latest()->paginate(10);
        return view('admin.company-types.index', compact('companyTypes'));
    }

    public function create()
    {
        return view('admin.company-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:company_types,name',
            'description' => 'nullable|string|max:1000',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        try {
            CompanyType::create($request->only([
                'name',
                'description',
                'price_monthly',
                'price_yearly',
            ]) + ['status' => $request->boolean('status')]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create additional company price: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.company-types.index')
            ->with('success', 'Additional company type created and synced to Stripe.');
    }

    public function edit(CompanyType $companyType)
    {
        $companyType->loadCount('companySubscriptionItems');
        return view('admin.company-types.edit', compact('companyType'));
    }

    public function update(Request $request, CompanyType $companyType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:company_types,name,' . $companyType->id,
            'description' => 'nullable|string|max:1000',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        try {
            $companyType->update($request->only([
                'name',
                'description',
                'price_monthly',
                'price_yearly',
            ]) + ['status' => $request->boolean('status')]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update additional company price: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.company-types.index')
            ->with('success', 'Additional company type updated and synced to Stripe.');
    }

    public function destroy(CompanyType $companyType)
    {
        if ($companyType->companySubscriptionItems()->exists()) {
            return redirect()->route('admin.company-types.index')
                ->with('error', 'Cannot delete a company type that is billed to a subscriber.');
        }
        try {
            $companyType->delete();
        } catch (\Exception $e) {
            return redirect()->route('admin.company-types.index')
                ->with('error', 'Error deleting additional company type: ' . $e->getMessage());
        }

        return redirect()->route('admin.company-types.index')
            ->with('success', 'Additional company type deleted.');
    }
}
