<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HarvestLog;
use App\Models\Durian;
use Illuminate\Support\Facades\DB;

class HarvestController extends Controller
{
    public function store(Request $request)
    {
        // Support both JSON and form-data
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        $request->replace($data);

        $request->validate([
            'farmer_id' => 'nullable|integer',
            'orchard_id' => 'nullable|integer',
            'durian_id' => 'nullable|integer',
            'harvest_date' => 'nullable|date',
            'total_harvested' => 'required|integer|min:1',
            'status' => 'required|string|max:50',
            'grade' => 'nullable|string',
            'condition' => 'nullable|string',
            'storage_location' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            $harvestLog = HarvestLog::create([
                'farmer_id' => $request->farmer_id,
                'orchard_id' => $request->orchard_id,
                'durian_id' => $request->durian_id,
                'harvest_date' => $request->harvest_date ?? now(),
                'total_harvested' => $request->total_harvested,
                'status' => $request->status,
                'grade' => $request->grade,
                'condition' => $request->condition,
                'storage_location' => $request->storage_location,
            ]);

            // If durian_id is valid, increment its total
            if ($request->durian_id) {
                $durian = Durian::lockForUpdate()->find($request->durian_id);
                if ($durian) {
                    $durian->increment('total', $request->total_harvested);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Harvest log recorded successfully',
                'data' => $harvestLog,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // You can delete this method if not used in frontend
    public function save(Request $request)
    {
        HarvestLog::create([
            'harvest_date' => $request->harvest_date,
            'orchard_id' => $request->orchard_id,
            'durian_id' => $request->durian_id,
            'total_harvested' => $request->total_harvested,
            'status' => 'pending',
            'grade' => null,
            'condition' => null,
            'storage_location' => null,
            'farmer_id' => $request->farmer_id,
        ]);

        return redirect()->back()->with('success', 'Harvest recorded successfully!');
    }

    private function getOrchardId($orchardName)
    {
        $orchardMapping = [
            'A' => 1,
            'B' => 2,
            'C' => 3,
        ];

        return $orchardMapping[$orchardName] ?? null;
    }
}
