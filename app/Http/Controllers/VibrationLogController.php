<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VibrationLog;
use Illuminate\Support\Facades\Log;

class VibrationLogController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'deviceID' => 'required|string|max:50',
            'vibrationCount' => 'required|integer|min:1',
            'logType' => 'required|integer',
            'orchard_id' => 'required|integer',
        ]);

        // Create a new VibrationLog instance
        $vibrationLog = new VibrationLog();
        $vibrationLog->device_id = $validatedData['deviceID'];
        $vibrationLog->vibration_count = $validatedData['vibrationCount'];
        $vibrationLog->log_type = $validatedData['logType'];
        $vibrationLog->orchard_id = $validatedData['orchard_id'];
        $vibrationLog->created_at = now(); // Set the timestamp explicitly
        $vibrationLog->save();

Log::info('Received data:', $request->all());
        return response()->json(['message' => 'Data received'], 200);
    }
}
