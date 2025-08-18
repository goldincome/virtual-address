<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FeatureSetting;
use App\Http\Controllers\Controller;

class FeatureSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Paginate the results, e.g., 10 items per page
        $featureSettings = FeatureSetting::latest()->paginate(10);
        return view('admin.feature-settings.index', compact('featureSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.feature-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:feature_settings,slug',
            'icon' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);
        $validatedData['slug'] = Str::slug($request->name);
        try {
            
            FeatureSetting::create($validatedData);
            return redirect()->route('admin.feature-settings.index')
                             ->with('success', 'Feature Setting created successfully.');
        } catch (\Exception $e) {
            //throw $e->getMessage(); //Log::error('Error creating feature setting: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Failed to create feature setting. Please try again.'.$e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Note: A dedicated show page is often not needed for simple settings.
     * If required, create a show.blade.php view.
     */
    public function show(FeatureSetting $featureSetting)
    {
        // If you decide you need a show page:
        // return view('admin.feature-settings.show', compact('featureSetting'));
        return redirect()->route('admin.feature-settings.edit', $featureSetting); // Or redirect to edit
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeatureSetting $featureSetting)
    {
        return view('admin.feature-settings.edit', compact('featureSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeatureSetting $featureSetting)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:feature_settings,slug,' . $featureSetting->id,
            'icon' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        try {
            $featureSetting->update($validatedData);
            return redirect()->route('admin.feature-settings.index')
                             ->with('success', 'Feature Setting updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating feature setting: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Failed to update feature setting. Please try again.')
                             ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeatureSetting $featureSetting)
    {
        try {
            $featureSetting->delete();
            return redirect()->route('admin.feature-settings.index')
                             ->with('success', 'Feature Setting deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting feature setting: ' . $e->getMessage());
            return redirect()->route('admin.feature-settings.index')
                             ->with('error', 'Failed to delete feature setting. Please try again.');
        }
    }
}
