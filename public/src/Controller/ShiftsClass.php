<?php

namespace App\Controller;

use App\Models\DB;
use App\Controller\Duration;
use DateTime;

class ShiftsClass
{
    /** @var string The ID of the worker. */
    public string $worker_id;

    /**
     * Initializes a new instance of the Worker class.
     *
     * @param string $worker_id The ID of the worker.
     */
    public function __construct($worker_id)
    {
        $this->worker_id = $worker_id;
    }

    /**
     * Adds a new work shift for the worker.
     *
     * @param string $date The date of the work shift, formatted as Y-m-d.
     * @param string $startTime The start time of the work shift, formatted as H:i.
     *
     * @return array An array containing a status message and additional data, such as the ID of the new work shift.
     */
    public function addShift(string $date, string $startTime): array
    {
        // Check that the date is valid.
        if (!Duration::workDateIsValid($date)) {
            return ['status' => 'error', 'message' => 'Work shift date must be a valid date Y-m-d'];
        }

        // Check that the date is either the current date or a future date.
        if (date('Y-m-d') > $date) {
            return ['status' => 'error', 'message' => 'Please choose current date or a future date'];
        }

        // Check that the worker does not already have a shift scheduled for the given date.
        if ($this->isShiftConflict($date)) {
            return ['status' => 'error', 'message' => 'Worker already has a shift on selected date'];
        }

        // Generate a unique ID for the new work shift.
        $workShiftID = uniqid();

        // Calculate the start and end times of the work shift.
        $startTimeUnix = DateTime::createFromFormat("Y-m-d H:i", $date . ' ' . $startTime)->format('U');
        $endTimeUnix = strtotime('+' . Duration::WORK_SHIFT_DURATION . ' hours', $startTimeUnix);

        // Insert the new work shift into the database.
        try {
            (new DB('shifts'))->setColumnsAndValues([
                "shift_id" => $workShiftID,
                "worker_id" => $this->worker_id,
                "start_time" => $startTimeUnix,
                'duration' => Duration::WORK_SHIFT_DURATION,
                "end_time" => $endTimeUnix,
                "date" => time()
            ])->insert();
            return ['status' => 'success', 'message' => 'shift created successfully', 'worker_id' => $this->worker_id, 'data' => $workShiftID];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Define a function called isShiftConflict that takes a date parameter and returns a boolean value
    public function isShiftConflict($date): bool
    {
        $shift = (new DB('shifts'))->makeQuery([
            'query' => "SELECT id FROM shifts WHERE worker_id = ? AND DATE(FROM_UNIXTIME(date)) = ?",
            'values' => [$this->worker_id, $date],
            'singleRecord' => true
        ]);

        // Check if the query returned a non-empty result and return true if it did, otherwise return false
        if (!empty($shift)) return true;
        return false;
    }


    /**
     * This function checks if the current worker ID exists in the 'workers' table of the database.
     * It creates a new DB object and calls its makeQuery method with a SQL query and parameters.
     * The 'singleRecord' parameter is set to true, indicating that only one record is expected to be returned.
     * If the query returns a non-empty result, indicating that the worker ID exists in the table, the function returns true.
     * Otherwise, it returns false.
     * @return bool Returns true if the worker ID exists in the 'workers' table, otherwise returns false.
     */
    public function exists(): bool
    {
        $worker = (new DB('workers'))->makeQuery([
            'query' => "SELECT worker_id FROM workers WHERE worker_id = ?",
            'values' => [$this->worker_id],
            'singleRecord' => true
        ]);
        if (!empty($worker)) return true;
        return false;
    }
}
