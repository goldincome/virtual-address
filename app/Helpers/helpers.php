<?php

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


if (! function_exists('currencyFormatter')) {
    function currencyFormatter($number)
    {
        return '£' . $number;
    }
}

if (! function_exists('getTimeSlots')) {
    /**
     * Generates available 1-hour time slots for a given date, excluding
     * public holidays and already booked times.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function getTimeSlots(Request $request)
    {
        $selectedDate = Carbon::parse($request->booking_date);
        $dayName = $selectedDate->format('l'); // e.g., Monday

        // Fetch store opening hours for this day
        $storeHours = \DB::table('opening_days')
            ->where('day_name', $dayName)->where('status', true)
            ->first();

        if (!$storeHours) {
            return response()->json(['slots' => []]);
        }

        // Construct opening and closing times for the selected date
        $openTime = Carbon::parse($selectedDate->toDateString() . ' ' . $storeHours->open_time);
        $closeTime = Carbon::parse($selectedDate->toDateString() . ' ' . $storeHours->close_time);

        // Fetch conflicting public holiday ranges
        $holidayConflicts = \DB::table('public_holidays')
            ->where(function ($query) use ($selectedDate) {
                $query->whereDate('date_from', '<=', $selectedDate)
                    ->whereDate('date_to', '>=', $selectedDate);
            })
            ->get();

        $conflictingHolidayRanges = collect($holidayConflicts)->map(function ($item) use ($selectedDate) {
            return [
                'start' => Carbon::parse($selectedDate->toDateString() . ' ' . Carbon::parse($item->date_from)->format('H:i:s')),
                'end' => Carbon::parse($selectedDate->toDateString() . ' ' . Carbon::parse($item->date_to)->format('H:i:s')),
            ];
        });

        // --- NEW: Fetch all booked time slots for the selected date ---
        $bookedTimesJson = \DB::table('order_details')
            ->whereDate('booked_date', $selectedDate->toDateString())
            ->where('product_id', $request->product_id) // Ensure we only get bookings for the specific product
            ->whereNotNull('all_booked_time') // Ensure the JSON column is not null
            ->pluck('all_booked_time'); // Get an array of JSON strings

        // Flatten all booked time ranges from the JSON data into a single collection
        $bookedRanges = $bookedTimesJson->flatMap(function ($json) {
            $bookings = json_decode($json, true);

            // Return an empty array if JSON is invalid or empty
            if (json_last_error() !== JSON_ERROR_NONE || empty($bookings)) {
                return [];
            }

            // Map each booking object to a start/end Carbon instance
            return collect($bookings)->map(function ($booking) {
                return [
                    'start' => Carbon::parse($booking['startDate']),
                    'end' => Carbon::parse($booking['endDate']),
                ];
            });
        });


        // Generate 1-hour slots
        $slots = [];
        $current = $openTime->copy();

        while ($current->lt($closeTime)) {
            $end = $current->copy()->addHour();
            if ($end->gt($closeTime)) break;

            // Check if the current slot overlaps with a public holiday
            $overlapsHoliday = $conflictingHolidayRanges->contains(function ($range) use ($current, $end) {
                // Check for any overlap: (StartA < EndB) and (EndA > StartB)
                return $current->lt($range['end']) && $end->gt($range['start']);
            });

            // --- NEW: Check if the current slot overlaps with an existing booking ---
            $overlapsBooking = $bookedRanges->contains(function ($range) use ($current, $end) {
                // Check for any overlap: (StartA < EndB) and (EndA > StartB)
                return $current->lt($range['end']) && $end->gt($range['start']);
            });

            // Only add the slot if it is not blocked by a holiday OR an existing booking
            if (!$overlapsHoliday && !$overlapsBooking) {
                $slots[] = $current->format('g:i A') . ' - ' . $end->format('g:i A');
            }

            $current->addHour();
        }

        return response()->json(['slots' => $slots]);
    }
}

if (!function_exists("generateOrderNumber")) {
    function generateOrderNumber()
    {
        $number = mt_rand(1000000000, 9999999999); // better than rand()

        // call the same function if the barcode exists already
        if (orderNumberExists($number)) {
            return generateOrderNumber();
        }

        // otherwise, it's valid and can be used
        return $number;
    }

    function orderNumberExists($number)
    {
        return Order::where('order_no', $number)->exists();
    }
}

 function ConvertStringToDateTime($date, $time)
 {
    $datetime = Carbon::parse($date)
        ->setTime(substr($time, 0, 2), substr($time, 2));

    return $datetime->toDateTimeString(); // Output: 2025-05-29 14:00:00
 }

 function getAllBookingTimeJson($bookingDate, array $times)
 {
    $dates = [];
    foreach($times as $time){
            // Extracting start and end times like '1000' and '1100'
            $startTime = substr(preg_replace('/[^0-9]/', '', $time), 0, 4);
            $endTime = substr(preg_replace('/[^0-9]/', '', $time), -4);

            $startDate = ConvertStringToDateTime($bookingDate, $startTime);
            $endDate = ConvertStringToDateTime($bookingDate, $endTime);
            $dates[] = ['startDate' => $startDate , 'endDate' => $endDate];
    }
    return json_encode($dates);
 }
