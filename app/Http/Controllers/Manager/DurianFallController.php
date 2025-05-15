<?php

namespace App\Http\Controllers\Manager;

use App\Models\VibrationLog;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DurianFallController extends Controller
{
    public function index()
    {
        $vibrationLogs = VibrationLog::with('orchard')->paginate(10);
        
        
        // Prepare data for the line chart
        $chartData = VibrationLog::orderBy('timestamp')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->timestamp)->format('Y-m-d');
            })
            ->map(function($group) {
                return [
                    'date' => $group->first()->created_at->format('Y-m-d'),
                    'count' => $group->sum('vibration_count')
                ];
            })
            ->values();

            $orchardFallCounts = VibrationLog::select('device_id', DB::raw('SUM(vibration_count) as total_falls'))
            ->groupBy('device_id')
            ->with('orchard')
            ->get();
            
        return view('manager.durian-fall', compact('vibrationLogs', 'chartData', 'orchardFallCounts'));
    }

    public function getData()
    {
        $vibrationLogs = VibrationLog::with('orchard')
            ->orderBy('timestamp')
            ->get();
            
        return response()->json($vibrationLogs);
    }
}