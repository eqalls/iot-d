<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Orchard Monitoring') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <!--orchards list-->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($orchards as $orchard)
                <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-transform transform hover:scale-105">
                    <h2 class="text-lg font-bold text-gray-800">Orchard {{ $orchard->orchardName }}</h2>
                    <p class="text-gray-600">
                        Device Name:
                        <span class="font-bold">{{ $orchard->device->name ?? 'No Device Assigned' }}</span>
                    </p>
                    <p class="text-gray-600">
                        Total Durian Fall:
                        <span id="vibration-count-sensor-{{ $orchard->id }}" class="font-bold">Loading...</span>
                    </p>
                    <div class="flex space-x-4 mt-4">
                        <button onclick="saveVibrationCount('{{ $orchard->id }}', document.getElementById('vibration-count-sensor-{{ $orchard->id }}').innerText)"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Collect & Reset
                        </button>
                        <a href="{{ route('farmer.orchards.show', $orchard->id) }}" 
                            class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Vibration Log Notifications -->
        <div class="mt-8 bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Vibration Notifications</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Device</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Detected Count</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vibrationLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b border-gray-200">
                                    @php
                                        $orchard = $orchards->first(function($o) use ($log) {
                                            return $o->device_id == $log->device_id;
                                        });
                                        $deviceName = $orchard ? ($orchard->device->name ?? 'Device '.$log->device_id) : 'Device '.$log->device_id;
                                    @endphp
                                    {{ $deviceName }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200">{{ $log->vibration_count }}</td>
                                <td class="py-2 px-4 border-b border-gray-200">
                                    @if($log->log_type == 1)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Fall</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Other</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 px-4 border-b border-gray-200 text-center text-gray-500">No vibration logs found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
<link rel="stylesheet" href="{{ asset('css/farmer/farmer-orchard.css') }}">
<script src="{{ asset('js/orchard.js') }}"></script>
<script>
    var orchards = @json($orchards);
</script>

