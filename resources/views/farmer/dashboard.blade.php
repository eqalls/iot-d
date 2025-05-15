<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <!-- Add this in your <head> section -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/farmer/farmer-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            @auth
            <div class="ml-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Hi {{ ucfirst(Auth::user()->role) }}
                </p>
            </div>
            @endauth
            <!-- Main Layout -->
            <div class="grid grid-cols-2 lg:grid-cols-2 gap-6">
                <!-- Overview Box - Changed from gradient to white -->
                <div class="col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg text-gray-800 dark:text-gray-200 custom-shadow">
                    <h3 class="text-lg font-bold">Durian Production</h3>
                    <div style="max-width: 250px; margin: auto;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>

                <!-- Daily Logging Boxes -->
                <div>
                    <!-- Changed from gradient to white -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg text-gray-800 dark:text-gray-200 custom-shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold">Durian Fall Count</h3>
                            <a href="{{ route('durian') }}" class="arrow-icon">➔</a>
                        </div>
                        <div class="text-center mt-2">
                            <img style="width: 50px; display: block; margin: auto;"
                                src="{{ asset('images/durian.png') }}" alt="Tilt Sensor Icon">
                            <p>Total Durian Fall: <span id="vibration-count">Loading...</span></p>
                        </div>
                    </div>
                    
                    <!-- Notifications - Moved inside the first column like admin dashboard -->
                    <div class="notification-box bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow mt-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold">Notifications</h3>
                            <span class="arrow-icon">➔</span>
                        </div>
                        <ul class="notification-list mt-4">
                            @if ($logs->isNotEmpty())
                                @foreach ($logs as $log) 
                                    <li class="notification-item mt-2 flex items-center">
                                        <span
                                            class="status-dot {{ $log->log_type == 1 ? 'green' : 'red' }} w-3 h-3 rounded-full inline-block mr-2"></span>
                                        <div>
                                            <p class="text-sm">
                                                {{ $log->orchard ? $log->orchard->orchardName : 'Unknown Orchard' }} - 
                                                {{ $log->log_type == 1 ? 'Fall detected' : 'Animal Detected' }} - 
                                                {{ $log->created_at->format('g:i A') }}
                                            </p>
                                            <p class="timestamp text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <li class="text-sm text-gray-500">No notifications found</li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <!-- Weather Section - Moved to second column to align with lower layout -->
            </div>

            <!-- Lower Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                <!-- Total Harvest Summary Box (Previously Orchard Monitoring) -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-bold">Total Harvest Summary</h3>
                        <a href="{{ route('orchards') }}" class="arrow-icon">➔</a>
                    </div>
                    <div class="text-center mt-2">
                        <img style="width: 50px; margin-left: auto; margin-right: auto; display: block;"
                            src="{{ asset('images/orchard.png') }}" alt="Harvest Icon" class="w-8 h-8 mr-2">
                        <p>Total Harvest: {{ $totalHarvest ?? 0 }} kg</p>
                    </div>
                </div>
                <!-- Orchard Assignment Box (Previously Device Management) -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Orchard Assignment</h3>
                        <a href="{{ route('devices') }}" class="arrow-icon">➔</a>
                    </div>
                    <div class="text-center mt-2">
                        <img style="width: 50px; margin-left: auto; margin-right: auto; display: block;"
                            src="{{ asset('images/iot.png') }}" alt="Orchard Icon" class="w-8 h-8 mr-2">
                        <p>Orchards: {{ $totalOrchards }}</p>
                    </div>
                </div>
                <!-- Harvest Production Box -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-bold">Production Report</h3>
                        <a href="{{ route('production-report') }}" class="arrow-icon">➔</a>
                    </div>
                    <div class="text-center">
                        <div class="space-y-3">
                            <!-- Record Fall -->
                            <div class="flex items-center justify-between bg-blue-100 dark:bg-blue-900 p-3 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-down-long"></i>
                                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Record
                                        Fall</span>
                                </div>
                                <span
                                    class="text-sm font-bold text-blue-800 dark:text-blue-100">{{ $totalRecordFall ?? 0 }}</span>
                            </div>

                            <!-- Total Harvest -->
                            <div
                                class="flex items-center justify-between bg-green-100 dark:bg-green-900 p-3 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-tree"></i>
                                    <span class="text-sm font-medium text-green-800 dark:text-green-200">Total
                                        Harvest</span>
                                </div>
                                <span
                                    class="text-sm font-bold text-green-800 dark:text-green-100">{{ $totalHarvest ?? 0 }}</span>
                            </div>

                            <!-- Inventory -->
                            <div
                                class="flex items-center justify-between bg-yellow-100 dark:bg-yellow-900 p-3 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-warehouse"></i>
                                    <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Inventory
                                        (kg)</span>
                                </div>
                                <span
                                    class="text-sm font-bold text-yellow-800 dark:text-yellow-100">{{ $totalInventory ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg custom-shadow">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-bold">Current Weather</h3>
                        <a href="{{ route('weather') }}" class="arrow-icon">➔</a>
                    </div>
                    <div class="text-center">
                        <div id="weather-info">Loading weather...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</x-app-layout>
<script src="/js/dashboard.js?v=<?= time() ?>"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>
<script>
    var durianData = @json($durianData);
    var weatherRoute = "{{ route('weather.current') }}";
    var alertRoute = "{{ route('checkAnimalDetection') }}";
</script>
