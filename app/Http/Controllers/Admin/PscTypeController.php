<?php
namespace App\Http\Controllers\Admin;

use App\Models\PscType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PscTypeController extends Controller
{
    public function index()
    {
        $pscTypes = PscType::latest()->paginate(10);
        return view('admin.psc-types.index', compact('pscTypes'));
    }

    public function create()
    {
        return view('admin.psc-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:psc_types,name',
            'description' => 'nullable|string|max:1000',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        try {
            PscType::create($request->only([
                'name',
                'description',
                'price_monthly',
                'price_yearly',
            ]) + ['status' => $request->boolean('status')]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create Person with Significant Control price: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.psc-types.index')
            ->with('success', 'Person with Significant Control type created and synced to Stripe.');
    }

    public function edit(PscType $pscType)
    {
        $pscType->loadCount('companyPscs');
        return view('admin.psc-types.edit', compact('pscType'));
    }

    public function update(Request $request, PscType $pscType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:psc_types,name,' . $pscType->id,
            'description' => 'nullable|string|max:1000',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        try {
            $pscType->update($request->only([
                'name',
                'description',
                'price_monthly',
                'price_yearly',
            ]) + ['status' => $request->boolean('status')]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update Person with Significant Control price: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.psc-types.index')
            ->with('success', 'Person with Significant Control type updated and synced to Stripe.');
    }

    public function destroy(PscType $pscType)
    {
        if ($pscType->companyPscs()->exists()) {
            return redirect()->route('admin.psc-types.index')
                ->with('error', 'Cannot delete a Person with Significant Control type that is assigned to a person.');
        }
        try {
            $pscType->delete();
        } catch (\Exception $e) {
            return redirect()->route('admin.psc-types.index')
                ->with('error', 'Error deleting Person with Significant Control type: ' . $e->getMessage());
        }

        return redirect()->route('admin.psc-types.index')
            ->with('success', 'Person with Significant Control type deleted.');
    }
}