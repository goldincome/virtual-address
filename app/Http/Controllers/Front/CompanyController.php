<?php
namespace App\Http\Controllers\Front;

use App\Models\Company;
use App\Services\PscService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = auth()->user()->companies()
            ->with('companyPscs.pscType')
            ->latest()
            ->paginate(10);

        $pscAllowance = $this->pscAllowanceData();

        return view('front.companies.index', compact('companies', 'pscAllowance'));
    }

    public function create()
    {
        $this->ensureEligible();

        return view('front.companies.create');
    }

    public function store(Request $request)
    {
        $this->ensureEligible();

        $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        $company = auth()->user()->companies()->create($request->only([
            'company_name',
            'address',
            'phone',
            'email',
            'description',
        ]));

        return redirect()->route('companies.show', $company)
            ->with('success', 'Company added successfully. You can now add Persons with Significant Control.');
    }

    public function show(Company $company)
    {
        $this->authorizeCompany($company);

        $company->load('companyPscs.pscType');
        $pscTypes = \App\Models\PscType::where('status', true)->orderBy('id')->get();
        $pscAllowance = $this->pscAllowanceData();

        return view('front.companies.show', compact('company', 'pscTypes', 'pscAllowance'));
    }

    protected function ensureEligible(): void
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            abort(403, 'You need an active Virtual Address plan to add company details.');
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            abort(403, 'Your current plan does not include Company Person with Significant Control.');
        }
    }

    protected function authorizeCompany(Company $company): void
    {
        abort_unless((int) $company->user_id === (int) auth()->id(), 403);
    }

    /**
     * PSC allowance (paid vs used) per active PSC type for the current user,
     * or null when the user's plan does not include Company PSC.
     *
     * @return array|null
     */
    protected function pscAllowanceData(): ?array
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            return null;
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            return null;
        }

        $service = app(PscService::class);
        $interval = $service->intervalForUser($user);

        return \App\Models\PscType::where('status', true)->orderBy('id')->get()
            ->map(function ($type) use ($user, $service, $interval) {
                $billed = $service->billedQuantity($user, $type);
                $active = $service->activePersonCount($user, $type);
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'price' => $type->priceForInterval($interval),
                    'interval' => $interval,
                    'billed' => $billed,
                    'active' => $active,
                    'remaining' => max(0, $billed - $active),
                ];
            })
            ->all();
    }
}