<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Orchard
                {{ $orchard->orchardName }} Details
            </h2>
            <a href="{{ route('farmer.orchards') }}" class="btn-secondary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Orchards
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Orchard Image and Basic Info -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden shadow-md">
                                <img id="orchardImage" class="w-full h-64 object-cover" src="{{ $orchard->imageUrl ?? 'https://grocycle.com/wp-content/uploads/2020/01/What-Is-A-Permaculture-Orchard.jpg' }}" alt="{{ $orchard->orchardName }}">
                                <div class="p-4">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">{{ $orchard->orchardName }}</h3>
                                    <div class="flex items-center mb-2">
                                        <span class="badge {{ $orchard->device ? 'badge-success' : 'badge-warning' }} mr-2">
                                            {{ $orchard->device ? 'Connected' : 'No Device' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $orchard->location }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Orchard Details -->
                        <div class="lg:col-span-2">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Orchard Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div class="stat-item">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Number of Trees</div>
                                        <div class="font-semibold text-lg">{{ $orchard->numTree }}</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Orchard Size</div>
                                        <div class="font-semibold text-lg">{{ $orchard->orchardSize }} acres</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Device</div>
                                        <div class="font-semibold text-lg">{{ $orchard->device->name ?? 'No Device Assigned' }}</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Durian Type</div>
                                        <div class="font-semibold text-lg">{{ $orchard->durian->name ?? 'Not Specified' }}</div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                                    <h4 class="font-medium text-blue-700 dark:text-blue-300 mb-2">Durian Fall Monitoring</h4>
                                    <div class="flex items-center">
                                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mr-3" id="vibration-count-sensor-{{ $orchard->id }}">
                                            <div class="loading-indicator"></div> Loading...
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Total Durian Falls
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button onclick="saveVibrationCount('{{ $orchard->id }}', document.getElementById('vibration-count-sensor-{{ $orchard->id }}').innerText)" class="btn-primary text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                            </svg>
                                            Collect & Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vibration Logs -->
                    <div class="mt-8">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Recent Durian Fall Logs</h3>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vibration Count</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Device</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse ($vibrationLogs as $log)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($log->timestamp)->format('M d, Y H:i:s') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $log->vibration_count }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $log->device->name ?? 'Device '.$log->device_id }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->log_type == 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $log->getLogTypeNameAttribute() }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    No vibration logs found for this orchard.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/farmer/farmer-orchard.css') }}">
    <script>
        var orchards = @json([$orchard]);
    </script>
    <script src="{{ asset('js/orchard.js') }}"></script>
</x-app-layout>