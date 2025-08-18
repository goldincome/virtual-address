<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PublicHoliday;
use App\Http\Controllers\Controller;
use App\Http\Requests\OffDays\StoreOffDayRequest;
use App\Services\OffDayService;

class OffDayController extends Controller
{
    protected $offDayService;

    public function __construct(OffDayService $offDayService)
    {
        $this->offDayService = $offDayService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offDays = $this->offDayService->getAllOffDays();
        return view('admin.off-days.index', compact('offDays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.off-days.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOffDayRequest $request)
    {
         if($this->offDayService->checkExistingOffDateEntry($request->date_to))
        {
            return redirect()->back()->with('error', "This date : ".Carbon::parse($request->date_to)->format('d F, Y'). ' is already existing');
        }
        $this->offDayService->createOffDay($request->validated());

        return redirect()->route('admin.off-days.index')
                         ->with('success', 'Public Holiday created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PublicHoliday $off_day)
    {
        return view('admin.off-days.show', compact('off_day'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PublicHoliday $off_day)
    {
       return view('admin.off-days.edit', compact('off_day'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreOffDayRequest $request, PublicHoliday $off_day)
    {
         $this->offDayService->updateOffDay($off_day, $request->validated());

        return redirect()->route('admin.off-days.index')
                         ->with('success', 'Public Holiday updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PublicHoliday $off_day)
    {
         $this->offDayService->deleteHolidaySchedule($off_day);

        return redirect()->route('admin.off-days.index')
                         ->with('success', 'Public Holiday deleted successfully.');
    }
}
