<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        // Define check methods
        $methods = ['Visual check', 'Operation check', 'Hearing/visual'];
        foreach ($methods as $method) {
            DB::table('check_methods')->updateOrInsert(['name' => $method]);
        }

        // Define areas and their check items
        $areas = [
            'COIL CAR' => [
                ['Craddle roll surface', 'Visual check'],
                ['Moving rail condition', 'Visual check'],
                ['Limit switch', 'Visual check'],
                ['Smooth moving operation', 'Operation check'],
            ],
            'COIL HOLDER' => [
                ['Outer board condition', 'Visual check'],
                ['Attachment condition', 'Visual check'],
                ['Expension & Retraction operation', 'Operation check'],
            ],
            'ENTRY PINCHROLL' => [
                ['No air/oil leak', 'Hearing/visual'],
                ['Sensor', 'Visual check'],
                ['Smooth lifting operation', 'Operation check'],
            ],
            'SHEAR/BEND' => [
                ['No air/oil leak', 'Hearing/visual'],
                ['Sensor', 'Visual check'],
                ['Smooth lifting operation', 'Operation check'],
                ['Cutter condition', 'Visual check'],
            ],
            'SPACE TABLE' => [
                ['No air/oil leak', 'Hearing/visual'],
                ['Side guide roller condition', 'Visual check'],
                ['Sensor', 'Visual check'],
            ],
            'MIDDLE PINCHROLL' => [
                ['No air/oil leak', 'Hearing/visual'],
                ['Sensor', 'Visual check'],
                ['Smooth lifting operation', 'Operation check'],
            ],
            'LEVELLER' => [
                ['No abnormal noise', 'Hearing/visual'],
                ['Sensor', 'Visual check'],
                ['Oil waste in container', 'Visual check'],
            ],
            'EXIT PINCHROLL' => [
                ['No air/oil leak', 'Hearing/visual'],
                ['Smooth lifting operation', 'Operation check'],
                ['Sensor', 'Visual check'],
            ],
        ];

        // Insert areas and check items
        foreach ($areas as $areaName => $items) {
            $areaId = DB::table('areas')->insertGetId(['name' => $areaName]);

            foreach ($items as [$itemName, $methodName]) {
                $methodId = DB::table('check_methods')->where('name', $methodName)->value('id');

                $checkItemId = DB::table('check_items')->insertGetId([
                    'area_id' => $areaId,
                    'name' => $itemName,
                    'method_id' => $methodId,
                ]);

                // Insert daily checks from April 2025 to March 2030
                $start = Carbon::create(2025, 4, 1);
                $end = Carbon::create(2030, 3, 31);

                while ($start <= $end) {
                    DB::table('daily_checks')->insert([
                        'check_item_id' => $checkItemId,
                        'check_date' => $start->toDateString(),
                        'status' => null,
                        'remarks' => null,
                    ]);
                    $start->addDay();
                }
            }
        }
    }
}


