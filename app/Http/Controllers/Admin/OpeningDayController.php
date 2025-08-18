<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\OpeningDay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OpeningDayRequest;

class OpeningDayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $openingDurations = OpeningDay::all();

        return view('admin.opening-days.edit', compact('openingDurations'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(OpeningDayRequest $request)
    {
        $validated = $request->validated();

        foreach ($validated['duration_ids'] as $index => $id) {
            $day = OpeningDay::find($id);
            if ($day) {
                $startTime = Carbon::parse($validated['open_time'][$index]);
                $endTime = Carbon::parse($validated['close_time'][$index]);
                $totalMinutes = $endTime->diffInMinutes($startTime);

                $day->update([
                    'open_time' => $validated['open_time'][$index],
                    'close_time' => $validated['close_time'][$index],
                    'status' => in_array($id, $validated['day_name'] ?? []),
                    'total_mins' => $totalMinutes,
                ]);

            }
        }
        $inActiveOpeningDays = OpeningDay::whereNotIn('id',$request->day_name )->update(['status' => false]);
        return redirect()->route('admin.opening-days.index')
                         ->with('success', 'Work hours updated successfully.');
    }
    

}