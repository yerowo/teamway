<?php

namespace App\Controller;

use App\Models\DB;

class WorkerManager
{
    public static function addWorker($firstName, $lastName, $roleId = 1): array
    {
        try {
            $workerID = "WRK" . date('Ym') . sprintf("%03d", mt_rand(1, 999));;
            (new DB('workers'))->setColumnsAndValues([
                "worker_id" => $workerID,
                "first_name" => $firstName,
                "last_name" => $lastName,
                "role_id" => $roleId,
                "date" => time()
            ])->insert();

            return ['status' => 'success', 'message' => 'Worker added successfully', 'worker_id' => $workerID];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getAllWorkers(): array
    {
        return (new DB('workers'))->makeQuery([
            'query' => 'SELECT * FROM workers ORDER BY date DESC',
        ]);
    }

    public function getAllShifts(): array
    {
        return (new DB('shifts'))->makeQuery([
            'query' => 'SELECT * FROM shifts ORDER BY id DESC',
        ]);
    }
}
