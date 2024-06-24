<?php

namespace App\test;

use App\Controller\ShiftsClass;
use App\Controller\WorkerManager;
use App\Models\DB;

use PHPUnit\Framework\TestCase;

class WorkerShiftTest extends TestCase
{
    public string $worker_id = 'WRK202304081';

    public function testClassConstructor()
    {
        $worker = new ShiftsClass($this->worker_id);
        $this->assertSame($this->worker_id, $worker->worker_id);
    }

    public function testAddWorker()
    {
        $result = WorkerManager::addWorker('John', 'Doe');

        // Check if the worker ID is a string
        $this->assertIsString($result['worker_id']);

        // Check if the worker ID starts with the prefix "WRK"
        $this->assertStringStartsWith('WRK', $result['worker_id']);

        // Check if the response array has the expected keys
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('worker_id', $result);

        // Check if the response status is "success"
        $this->assertEquals('success', $result['status']);

        // Check if the response message is "Worker added successfully"
        $this->assertEquals('Worker added successfully', $result['message']);
    }

    public function testAddShift()
    {
        // Create a new shift instance.
        $shift = new ShiftsClass($this->worker_id);

        // Try to add a new shift for the worker.
        $date = date('Y-m-d', strtotime('+1 day'));
        $startTime = '09:00';
        $shiftResult = $shift->addShift($date, $startTime);

        // // Check that the shift was added successfully.
        $this->assertEquals(['status' => 'success', 'message' => 'shift created successfully', 'worker_id' => $shift->worker_id, 'data' => $shiftResult['data']], $shiftResult);
    }
}
