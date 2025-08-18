<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\MailUsage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MailUsageController extends Controller
{
    public function index(Request $request)
    {
        $usages = MailUsage::with('user')
            ->when($request->has('billed'), fn($q) =>
                $q->where('billed', $request->billed))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.mail-usages.index', compact('usages'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.mail-usages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        MailUsage::create($data);

        return redirect()->route('admin.mail-usages.index')->with('success', 'Mail usage added.');
    }

    public function show(MailUsage $usage)
    {
        return view('admin.mail-usages.show', compact('usage'));
    }

    public function edit(MailUsage $usage)
    {
        $users = User::all();
        return view('admin.mail-usages.edit', compact('usage', 'users'));
    }

    public function update(Request $request, MailUsage $usage)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billed' => 'required|boolean',
        ]);

        $usage->update($data);

        return redirect()->route('admin.mail-usages.index')->with('success', 'Mail Usage updated.');
    }

    public function destroy(MailUsage $usage)
    {
        $usage->delete();
        return back()->with('success', 'Mail Usage deleted.');
    }

    public function markBilled(MailUsage $usage)
    {
        $usage->update(['billed' => true]);
        return back()->with('success', 'Mail Usage Marked as billed.');
    }
}
