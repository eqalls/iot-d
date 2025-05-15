<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Orchard;
use App\Models\VibrationLog; // Add VibrationLog model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;

class DashboardController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function checkAnimalDetection()
    {
        $latestLog = VibrationLog::where('log_type', 2)->latest('created_at')->first();
    
        return response()->json([
            'alert' => $latestLog ? true : false,
            'log_id' => $latestLog ? $latestLog->id : null,
        ]);
    }    

    public function index()
    {
        // Retrieve data from Firebase
        $weather = $this->firebaseService->getWeatherData();
        $totalDurian = $this->firebaseService->getDurianCount();
        $totalDevice = Device::count();
        $totalOrchards = Orchard::count();
        $durianData = DB::table('durians')->select('name', DB::raw('SUM(total) as total'))->groupBy('name')->get();

        // Retrieve Vibration Logs with orchard information (Latest 4 logs)
        $logs = VibrationLog::with('orchard')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
        
        // Get production statistics
        $totalRecordFall = VibrationLog::where('log_type', 1)->count();
        $totalHarvest = DB::table('harvest_logs')->sum('total_harvested');
        $totalInventory = DB::table('inventory_transactions')->sum('quantity');

        // Pass all variables to the dashboard view
        return view(
            'farmer.dashboard',
            compact(
                'weather',
                'totalDurian',
                'totalDevice',
                'totalOrchards',
                'durianData',
                'logs',
                'totalRecordFall',
                'totalHarvest',
                'totalInventory'
            ),
        );
    }
}
