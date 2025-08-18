<?php
namespace App\Services;

use App\Models\PublicHoliday;

class OffDayService
{

     /**
     * Get all public holidays with pagination.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllOffDays()
    {
        return PublicHoliday::latest()->paginate(10);
    }

    /**
     * Create a new public holiday.
     *
     * @param array $data
     * @return \App\Models\PublicHoliday
     */
    public function createOffDay(array $data): PublicHoliday
    {
        $data['status'] = isset($data['status']);
        return PublicHoliday::create($data);
    }

    /**
     * Update an existing public holiday.
     *
     * @param \App\Models\PublicHoliday $PublicHoliday
     * @param array $data
     * @return \App\Models\PublicHoliday
     */
    public function updateOffDay(PublicHoliday $offDay, array $data): PublicHoliday
    {
        $data['status'] = isset($data['status']) ?? false;
        $offDay->update($data);
        return $offDay;
    }

    /**
     * Delete a public holiday.
     *
     * @param \App\Models\PublicHoliday $offDay
     * @return void
     */
    public function deleteHolidaySchedule(PublicHoliday $offDay): void
    {
        $offDay->delete();
    }

    public function checkExistingOffDateEntry($date) : bool
    {
        $date = date('Y-m-d', strtotime($date));
        try{
            $offDate = PublicHoliday::WhereDate('date_to', '=', $date)
                ->where('status', true)->latest()->exists();
            return $offDate;
         }catch(\Exception $e){
            return false;
         } 
    }
}