<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orchard;
use App\Models\Device;
use App\Models\Durian;
use App\Models\VibrationLog;

class OrchardController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        $durians = Durian::all();
        
        $orchards = auth()->user()->role === 'farmer'
            ? auth()->user()->farmer->orchards()->with(['durian', 'device'])->get()
            : Orchard::with(['durian', 'device'])->get();
        
        // Fetch vibration logs for the orchards
        $orchardDeviceIds = $orchards->pluck('device_id')->filter()->toArray();
        
        // Filter vibration logs based on user role
        if (auth()->user()->role === 'farmer') {
            // For farmers, only show logs from their assigned orchards
            $vibrationLogs = VibrationLog::whereIn('device_id', $orchardDeviceIds)
                ->orderBy('timestamp', 'desc')
                ->take(10)
                ->get();
        } else {
            // For admins, show all logs
            $vibrationLogs = VibrationLog::orderBy('timestamp', 'desc')
                ->take(10)
                ->get();
        }
    
        $view = auth()->user()->role === 'farmer' 
            ? 'farmer.orchards' 
            : 'admin.orchards';
    
        return view($view, compact('orchards', 'devices', 'durians', 'vibrationLogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orchardName' => 'required|string|max:255',
            'numTree' => 'required|integer|min:1',
            'orchardSize' => 'required|numeric|min:0.1',
            'location' => 'required|string|max:255',
            'device_id' => 'nullable|exists:devices,id',
            'durian_id' => 'nullable|exists:durians,id',
        ]);
    
        // Create orchard without user_id
        $orchard = Orchard::create($validated);
        
        // Attach to farmer if farmer is creating (using a pivot table instead of user_id column)
        if (auth()->user()->role === 'farmer') {
            auth()->user()->farmer->orchards()->attach($orchard->id);
        }
    
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Orchard added successfully!',
                'orchard' => $orchard
            ]);
        }
        
        return redirect()->route('orchards')->with('success', 'Orchard added successfully!');
    }

    public function updateTotalDurianFall(Request $request, $orchardId)
    {
        $validated = $request->validate([
            'total_durian_fall' => 'required|integer',
        ]);

        $orchard = Orchard::find($orchardId);

        if (!$orchard) {
            return response()->json(['status' => 'error', 'message' => 'Orchard not found'], 400);
        }

        // Assuming you have a 'total_durian_fall' column in your orchards table
        $orchard->total_durian_fall = $validated['total_durian_fall'];
        $orchard->save();

        return response()->json(['status' => 'success', 'message' => 'Durian fall count updated successfully']);
    }

    public function destroy($id)
    {
        $orchard = Orchard::findOrFail($id);
        $orchardName = $orchard->orchardName;
        $orchard->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Orchard '{$orchardName}' deleted successfully!"
            ]);
        }
        
        return redirect()->route('orchards')->with('success', 'Orchard deleted successfully!');
    }

    public function show($id)
    {
        $orchard = Orchard::with(['durian', 'device'])->findOrFail($id);
        
        // Get vibration logs for this orchard with device relationship
        $vibrationLogs = VibrationLog::where('device_id', $orchard->device_id)
            ->with('device')  // Eager load the device relationship
            ->orderBy('timestamp', 'desc')
            ->take(20)
            ->get();
        
        // Check if the user is a farmer and use the appropriate view
        if (auth()->user()->role === 'farmer') {
            return view('farmer.orchard-details', compact('orchard', 'vibrationLogs'));
        }
            
        return view('orchards.show', compact('orchard', 'vibrationLogs'));
    }
    
    public function getTotalFalls()
    {
        $orchards = Orchard::with(['device'])->get();
        $totalFalls = 0;
        
        foreach ($orchards as $orchard) {
            if ($orchard->device) {
                $totalFalls += VibrationLog::where('device_id', $orchard->device_id)->sum('vibration_count');
            }
        }
        
        return response()->json(['total_falls' => $totalFalls]);
    }
    
    public function create()
    {
        $devices = Device::all();
        $durians = Durian::all();
        
        return view('admin.orchards.create', compact('devices', 'durians'));
    }
    
    public function edit($id)
    {
        $orchard = Orchard::findOrFail($id);
        $devices = Device::all();
        $durians = Durian::all();
        
        return view('orchards.edit', compact('orchard', 'devices', 'durians'));
    }
    
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'orchardName' => 'required|string|max:255',
            'numTree' => 'required|integer|min:1',
            'orchardSize' => 'required|numeric|min:0.1',
            'location' => 'required|string|max:255',
            'device_id' => 'nullable|exists:devices,id',
            'durian_id' => 'nullable|exists:durians,id',
        ]);
        
        $orchard = Orchard::findOrFail($id);
        $orchard->update($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Orchard updated successfully!',
                'orchard' => $orchard
            ]);
        }
        
        return redirect()->route('orchards')->with('success', 'Orchard updated successfully!');
    }
}
