<?php
namespace App\Http\Controllers\Admin;

use App\Models\Company;
use App\Models\CompanyPsc;
use App\Models\PscType;
use App\Services\PscService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $companies = Company::with('user:id,name,email')
            ->withCount('companyPscs')
            ->when($search, function ($query, $search) {
                $search = trim($search);
                $query->where(fn($q) => $q
                    ->where('company_name', 'like', '%' . $search . '%')
                    ->orWhereHas('companyPscs', function ($psc) use ($search) {
                        $psc->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    })
                );
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.companies.index', compact('companies', 'search'));
    }

    public function show(Company $company)
    {
        $company->load('user:id,name,email', 'companyPscs.pscType');
        $pscTypes = PscType::where('status', true)->orderBy('id')->get();

        return view('admin.companies.show', compact('company', 'pscTypes'));
    }

    public function addPsc(Request $request, Company $company)
    {
        $request->validate([
            'psc_type_id' => 'required|exists:psc_types,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $pscType = PscType::where('status', true)->findOrFail($request->psc_type_id);

        try {
            app(PscService::class)->addPerson($company, $pscType, $request->all());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Person with Significant Control added successfully.');
    }

    public function suspend(Request $request, Company $company)
    {
        try {
            app(PscService::class)->suspendCompany($company);
        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.companies.index')->with('success', 'Company suspended and its Stripe billing cancelled.');
    }

    public function activate(Request $request, Company $company)
    {
        try {
            app(PscService::class)->activateCompany($company);
        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.companies.index')->with('success', 'Company activated.');
    }

    public function suspendPsc(Request $request, CompanyPsc $psc)
    {
        try {
            app(PscService::class)->suspendPsc($psc);
        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.companies.show', $psc->company_id)->with('success', 'Person with Significant Control suspended and its Stripe billing cancelled.');
    }

    public function activatePsc(Request $request, CompanyPsc $psc)
    {
        try {
            app(PscService::class)->activatePsc($psc);
        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.companies.show', $psc->company_id)->with('success', 'Person with Significant Control activated.');
    }
}