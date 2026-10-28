<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $subStatus = $request->input('subscription_status');
        $planId = $request->input('plan_id');
        $mailType = $request->input('mail_type');

        $query = User::with(['subscriptions', 'orders', 'companies', 'pscSubscriptionItems'])
            ->where(function($q) {
                $q->where('user_type', 'customer')->orWhereNull('user_type');
            })
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('customer_number', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($subStatus) {
            switch ($subStatus) {
                case 'active':
                    $query->whereHas('subscriptions', function ($subQuery) {
                        $subQuery->where(function ($q) {
                            $q->where('stripe_status', 'active')
                              ->orWhere('stripe_status', 'trialing');
                        })->where(function ($q) {
                            $q->whereNull('ends_at')
                              ->orWhere('ends_at', '>', now());
                        });
                    });
                    break;
                case 'cancelled':
                    $query->whereHas('subscriptions', function ($subQuery) {
                        $subQuery->where(function ($q) {
                            $q->whereNotNull('ends_at')
                              ->where('ends_at', '>', now())
                              ->orWhere('stripe_status', 'canceled');
                        });
                    });
                    break;
                case 'expired':
                    $query->whereHas('subscriptions', function ($subQuery) {
                        $subQuery->whereNotNull('ends_at')
                                 ->where('ends_at', '<=', now());
                    });
                    break;
                case 'none':
                    $query->whereDoesntHave('subscriptions');
                    break;
            }
        }

        if ($planId) {
            $plan = Plan::find($planId);
            if ($plan) {
                $query->where(function($q) use ($plan, $planId) {
                    $q->whereHas('subscriptions', function ($subQuery) use ($plan) {
                        $subQuery->where('stripe_price', $plan->stripe_price_id_monthly)
                                 ->orWhere('stripe_price', $plan->stripe_price_id_yearly)
                                 ->orWhereHas('items', function ($itemQuery) use ($plan) {
                                     $itemQuery->where('stripe_price', $plan->stripe_price_id_monthly)
                                               ->orWhere('stripe_price', $plan->stripe_price_id_yearly);
                                 });
                    })
                    ->orWhereHas('orderDetails', function ($detailQuery) use ($planId) {
                        $detailQuery->where('product_type', 'virtual_address')
                                    ->whereJsonContains('plan->id', (int) $planId);
                    });
                });
            }
        }

        $customers = $query->paginate(15)->withQueryString();
        $plans = Plan::where('is_active', true)->get();

        return view('admin.customers.index', compact('customers', 'plans', 'search', 'subStatus', 'planId', 'mailType'));
    }

    /**
     * Display the specified customer.
     */
    public function show(User $customer)
    {
        $customer->load([
            'subscriptions',
            'orders.orderDetails',
            'companies.companyPscs.pscType',
            'pscSubscriptionItems',
            'mailUsages',
            'mails',
        ]);

        $latestVaOrder = $customer->orders()
            ->whereHas('orderDetails', function($q) {
                $q->where('product_type', 'virtual_address');
            })
            ->latest()
            ->first();

        $mailType = '—';
        if ($latestVaOrder) {
            $detail = $latestVaOrder->orderDetails->where('product_type', 'virtual_address')->first();
            if ($detail && $detail->plan) {
                $planData = json_decode($detail->plan, true);
                if (isset($planData['mail_type'])) {
                    $mailType = ucfirst($planData['mail_type']);
                }
            }
        }

        return view('admin.customers.show', compact('customer', 'mailType', 'latestVaOrder'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(User $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, User $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:50',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'status' => 'required|in:active,suspended',
        ]);

        $customer->update($request->only([
            'name',
            'email',
            'phone',
            'first_name',
            'last_name',
            'status',
        ]));

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Suspend customer locally.
     */
    public function suspend(Request $request, User $customer)
    {
        $customer->update(['status' => 'suspended']);

        return redirect()->back()->with('success', 'Customer suspended successfully.');
    }

    /**
     * Activate customer.
     */
    public function activate(Request $request, User $customer)
    {
        $customer->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Customer activated successfully.');
    }

    /**
     * Export customers search results to CSV / Excel.
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $subStatus = $request->input('subscription_status');
        $planId = $request->input('plan_id');
        $mailType = $request->input('mail_type');

        $query = User::with(['subscriptions', 'orders', 'companies', 'pscSubscriptionItems', 'mails'])
            ->where(function($q) {
                $q->where('user_type', 'customer')->orWhereNull('user_type');
            })
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('customer_number', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($subStatus) {
            switch ($subStatus) {
                case 'active':
                    $query->whereHas('subscriptions', function ($subQuery) {
                        $subQuery->where(function ($q) {
                            $q->where('stripe_status', 'active')
                              ->orWhere('stripe_status', 'trialing');
                        })->where(function ($q) {
                            $q->whereNull('ends_at')
                              ->orWhere('ends_at', '>', now());
                        });
                    });
                    break;
                case 'cancelled':
                    $query->whereHas('subscriptions', function ($subQuery) {
                        $subQuery->where(function ($q) {
                            $q->whereNotNull('ends_at')
                              ->where('ends_at', '>', now())
                              ->orWhere('stripe_status', 'canceled');
                        });
                    });
                    break;
                case 'expired':
                    $query->whereHas('subscriptions', function ($subQuery) {
                        $subQuery->whereNotNull('ends_at')
                                 ->where('ends_at', '<=', now());
                    });
                    break;
                case 'none':
                    $query->whereDoesntHave('subscriptions');
                    break;
            }
        }

        if ($planId) {
            $plan = Plan::find($planId);
            if ($plan) {
                $query->where(function($q) use ($plan, $planId) {
                    $q->whereHas('subscriptions', function ($subQuery) use ($plan) {
                        $subQuery->where('stripe_price', $plan->stripe_price_id_monthly)
                                 ->orWhere('stripe_price', $plan->stripe_price_id_yearly)
                                 ->orWhereHas('items', function ($itemQuery) use ($plan) {
                                     $itemQuery->where('stripe_price', $plan->stripe_price_id_monthly)
                                               ->orWhere('stripe_price', $plan->stripe_price_id_yearly);
                                 });
                    })
                    ->orWhereHas('orderDetails', function ($detailQuery) use ($planId) {
                        $detailQuery->where('product_type', 'virtual_address')
                                    ->whereJsonContains('plan->id', (int) $planId);
                    });
                });
            }
        }

        $customers = $query->get();

        $filename = 'customers-export-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Customer Number',
                'Name',
                'Email',
                'Phone',
                'Subscription Plan',
                'Subscription Status',
                'Subscription Expiry Date',
                'Total Mails Scanned',
                'Total Mails Forwarded',
                'Companies Count',
                'PSCs Count',
                'Account Status',
                'Registered Date'
            ]);

            foreach ($customers as $customer) {
                $activeSub = $customer->subscriptions->first(fn($s) => $s->active() || $s->onGracePeriod());
                $subPlan = $activeSub ? optional($activeSub->plan)->name : ($customer->subscriptions->first() ? optional($customer->subscriptions->first()->plan)->name : 'None');
                $subStatus = 'None';
                $expiryDate = 'None';
                if ($activeSub) {
                    if ($activeSub->active()) $subStatus = 'Active';
                    elseif ($activeSub->onGracePeriod()) $subStatus = 'Grace Period';

                    if ($activeSub->ends_at) {
                        $expiryDate = $activeSub->ends_at->format('Y-m-d H:i:s');
                    } else {
                        try {
                            $stripeSub = $activeSub->asStripeSubscription();
                            if (isset($stripeSub->current_period_end)) {
                                $expiryDate = \Carbon\Carbon::createFromTimestamp($stripeSub->current_period_end)->format('Y-m-d H:i:s');
                            } else {
                                $expiryDate = 'Active (Auto-renewing)';
                            }
                        } catch (\Exception $e) {
                            $expiryDate = 'Active (Auto-renewing)';
                        }
                    }
                } elseif ($customer->subscriptions->count() > 0) {
                    $subStatus = 'Cancelled / Expired';
                    $latestSub = $customer->subscriptions->first();
                    $expiryDate = $latestSub->ends_at ? $latestSub->ends_at->format('Y-m-d H:i:s') : 'Expired';
                }

                $scannedCount = $customer->mails->where('mail_type', \App\Enums\MailTypeEnum::Scanned)->count();
                $forwardedCount = $customer->mails->where('mail_type', \App\Enums\MailTypeEnum::Forwarded)->count();

                fputcsv($file, [
                    $customer->customer_number,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $subPlan,
                    $subStatus,
                    $expiryDate,
                    $scannedCount,
                    $forwardedCount,
                    $customer->companies->count(),
                    $customer->pscSubscriptionItems->count(),
                    ucfirst($customer->status),
                    $customer->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
