<?php
namespace App\Http\Controllers\Front;

use App\Models\Company;
use App\Models\PscType;
use App\Services\CartService;
use App\Services\PscService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyPscController extends Controller
{
    public function store(Request $request, Company $company)
    {
        abort_unless((int) $company->user_id === (int) auth()->id(), 403);

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

        return redirect()->route('companies.show', $company)
            ->with('success', 'Person with Significant Control added successfully.');
    }

    /**
     * Add the requested PSC allowance (top-up) quantities to the cart so the
     * user can pay for more slots through the normal checkout flow.
     */
    public function topUp(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            return redirect()->back()->with('error', 'You need an active Virtual Address plan to purchase PSC slots.');
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            return redirect()->back()->with('error', 'Your current plan does not include Company Person with Significant Control.');
        }

        $request->validate([
            'psc' => 'required|array',
            'psc.*' => 'nullable|integer|min:0|max:100',
        ]);

        $quantities = array_filter($request->input('psc', []), fn ($q) => (int) $q > 0);
        if (empty($quantities)) {
            return redirect()->back()->with('error', 'Please choose how many PSC slots you want to purchase.');
        }

        try {
            app(CartService::class)->addPscTopUpToCart($user, $quantities);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('cart.index')
            ->with('success', 'PSC top-up added to your cart. Complete checkout to increase your allowance.');
    }
}