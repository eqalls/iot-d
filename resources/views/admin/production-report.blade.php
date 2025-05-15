<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Production Report') }}
        </h2>
    </x-slot>

    <!-- Add CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-production.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            showModal: false,
            selectedReport: null,
            harvestId: null,
            harvestDate: null,
            orchard: null,
            durianType: null,
            totalHarvest: null
        }">
            <div class="mb-4">
                <!-- Record Fall Filter -->
                <form method="GET" action="{{ route('production-report') }}"
                    class="flex flex-wrap gap-4 items-center mb-4" id="recordFallFilter">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" placeholder="Search..." class="w-full border rounded p-2"
                            value="{{ request('search') }}">
                    </div>
                    <select name="device_id" class="border rounded p-2">
                        <option value="">All Devices</option>
                        @foreach ($vibrationLogs->unique('device_id') as $log)
                            <option value="{{ $log->device_id }}"
                                {{ request('device_id') == $log->device_id ? 'selected' : '' }}>
                                Device {{ $log->device_id }}
                            </option>
                        @endforeach
                    </select>
                    <select name="log_type" class="border rounded p-2">
                        <option value="">All Log Types</option>
                        <option value="1" {{ request('log_type') == '1' ? 'selected' : '' }}>Fall</option>
                        <option value="0" {{ request('log_type') == '0' ? 'selected' : '' }}>Other</option>
                    </select>
                    <input type="date" name="date" class="border rounded p-2" value="{{ request('date') }}">
                    <button type="submit" class="bg-blue-500 text-black rounded p-2">Filter</button>
                </form>

                <!-- Harvest Report Filter -->
                <form method="GET" action="{{ route('production-report') }}"
                    class="flex flex-wrap gap-4 items-center mb-4 hidden" id="harvestReportFilter">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" placeholder="Search..." class="w-full border rounded p-2"
                            value="{{ request('search') }}">
                    </div>
                    <select name="orchard" class="border rounded p-2">
                        <option value="">All Orchards</option>
                        @foreach ($orchards as $orchard)
                            <option value="{{ $orchard }}"
                                {{ request('orchard') == $orchard ? 'selected' : '' }}>
                                {{ $orchard }}
                            </option>
                        @endforeach
                    </select>
                    <select name="durian_type" class="border rounded p-2">
                        <option value="">All Durian Types</option>
                        @foreach ($durianTypes as $type)
                            <option value="{{ $type }}"
                                {{ request('durian_type') == $type ? 'selected' : '' }}>{{ $type }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="date" class="border rounded p-2" value="{{ request('date') }}">
                    <button type="submit" class="bg-blue-500 text-black rounded p-2">Filter</button>
                </form>
                <!-- Inventory Report Filter -->
                <form method="GET" action="{{ route('production-report') }}"
                    class="flex flex-wrap gap-4 items-center mb-4 hidden" id="inventoryReportFilter">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="inventory_search" placeholder="Search..."
                            class="w-full border rounded p-2" value="{{ request('inventory_search') }}">
                    </div>
                    <select name="storage_location" class="border rounded p-2">
                        <option value="">All Storage Locations</option>
                        @foreach ($storageReports->unique('storage_location') as $report)
                            <option value="{{ $report->storage_location }}"
                                {{ request('storage_location') == $report->storage_location ? 'selected' : '' }}>
                                Storage {{ $report->storage_location }}
                            </option>
                        @endforeach
                    </select>
                    <select name="quantity_range" class="border rounded p-2">
                        <option value="">All Quantities</option>
                        <option value="0-100" {{ request('quantity_range') == '0-100' ? 'selected' : '' }}>0-100
                            kg</option>
                        <option value="101-500" {{ request('quantity_range') == '101-500' ? 'selected' : '' }}>
                            101-500 kg</option>
                        <option value="501+" {{ request('quantity_range') == '501+' ? 'selected' : '' }}>>500 kg
                        </option>
                    </select>
                    <button type="submit" class="bg-blue-500 text-black rounded p-2">Filter</button>
                </form>
            </div>

            <div class="mb-6">
                <button onclick="showTable('recordFall')" class="bg-blue-500 rounded-md">
                    Record Fall
                </button>
                <button onclick="showTable('harvestReport')" class="bg-green-500 rounded-md">
                    Harvest Report
                </button>
                <button onclick="showTable('inventoryReport')" class="bg-yellow-500 rounded-md">
                    Inventory Report
                </button>
            </div>

            <!-- Record Fall Section with Chart -->
            <div id="recordFall" class="table-container">
                <h3 class="text-lg font-bold mb-4">
                    </i> Record Fall
                </h3>
                <!-- Add Chart Canvas -->
                <div class="chart-container" style="overflow-x: auto; position: relative;">
                    <canvas id="fallChart" style="min-width: 700px; width: 100%; height: 400px;"></canvas>
                </div>
                <table
                    class="w-full mb-8 min-w-full divide-y divide-gray-200 dark:divide-gray-700 overflow-hidden shadow-sm rounded-lg">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Device ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Durian Fall</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Log Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($vibrationLogs as $index => $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $log->device_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $log->vibration_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $log->log_type == 1 ? 'text-green-600 bg-green-100 border border-green-300' : 'text-red-600 bg-red-100 border border-red-300' }}">
                                        <span
                                            class="w-2 h-2 mr-2 rounded-full {{ $log->log_type == 1 ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $log->log_type == 1 ? 'Fall' : 'Other' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No Vibration
                                    Data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Pagination for Record Fall -->
                <div class="mt-4">
                    {{ $vibrationLogs->appends(request()->except('page'))->links() }}
                </div>
            </div>

            <!-- Harvest Report Section -->
            <div id="harvestReport" class="table-container hidden">
                <h3 class="text-lg font-bold mb-4">Harvest Report</h3>
                <div class="chart-container" style="overflow-x: auto; position: relative; max-height: 300px;">
                    <canvas id="harvestChart" style="min-width: 700px; width: 100%; max-height: 300px;"></canvas>
                </div>
                </br>
                <table
                    class="w-full mb-8 min-w-full divide-y divide-gray-200 dark:divide-gray-700 overflow-hidden shadow-sm rounded-lg">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Farmer</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Orchard</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Durian Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Harvest Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Total Harvested</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($harvestReports as $index => $report)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->farmer->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->orchard->orchardName }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->durian->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->harvest_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->total_harvested }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $report->status == 'Completed' ? 'text-green-600 bg-green-100 border border-green-300' : 'text-orange-600 bg-orange-100 border border-orange-300' }}">
                                        <span
                                            class="w-2 h-2 mr-2 rounded-full {{ $report->status == 'Completed' ? 'bg-green-500' : 'bg-orange-500' }}"></span>
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <!-- Action Dropdown -->
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <button @click="open = !open"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                            Action
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                            class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                            <div class="py-1">
                                                <!-- Inside the action dropdown -->
                                                <button type="button"
                                                    @click="selectedReport = {{ json_encode($report) }}; showModal = true; open = false"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    View Details
                                                </button>
                                                <button type="button" @click="printReport(); open = false"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    Print
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No Harvest
                                    Report Data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Pagination for Harvest Report -->
                <div class="mt-4">
                    {{ $harvestReports->appends(request()->except('page'))->links() }}
                </div>
                <div x-show="showModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                    <div class="bg-white p-6 rounded-lg shadow-lg text-sm w-full max-w-2xl mx-auto relative">
                        <button @click="showModal = false"
                            class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl">&times;</button>
                        <form @submit.prevent="saveHarvestDetails()" id="harvestDetailsForm" x-data="{ isSubmitting: false }">
                            <div id="printable">
                                <!-- Basic Info -->
                                <h2 class="text-2xl font-bold mb-4 text-center">Durian Harvest Checklist Report</h2>
                                <div class="mb-4">
                                    <input type="hidden" x-model="selectedReport.id" name="harvest_id">
                                    <p><strong>Harvest ID:</strong> <span x-text="selectedReport.id"></span></p>
                                    <p><strong>Date:</strong> <span x-text="selectedReport.harvest_date"></span></p>
                                    <p><strong>Orchard Location:</strong> <span x-text="selectedReport.orchard"></span>
                                    </p>
                                    <p><strong>Durian Type:</strong> <span x-text="selectedReport.durian_type"></span>
                                    </p>
                                    <p><strong>Total Harvested:</strong> <span
                                            x-text="selectedReport.total_harvested"></span> fruits</p>
                                    <p><strong>Status:</strong> <span x-text="selectedReport.status"></span></p>
                                </div>

                                <!-- Durian Details -->
                                <hr class="my-4">
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold mb-2">Durian Details</h3>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="estimated_weight"
                                                    class="block text-sm font-medium mb-1">Estimated Weight
                                                    (kg)</label>
                                                <input type="number" id="estimated_weight" name="estimated_weight"
                                                    class="w-full border rounded p-2" step="0.01" required
                                                    x-bind:value="selectedReport.estimated_weight">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-2">Grade</label>
                                                <div class="grid grid-cols-3 gap-4">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" id="grade_a" name="grade[]"
                                                            value="A" class="form-checkbox h-4 w-4 text-blue-600"
                                                            x-bind:checked="selectedReport.grade && selectedReport.grade.includes('A')">
                                                        <label for="grade_a" class="ml-2">Grade A</label>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input type="checkbox" id="grade_b" name="grade[]"
                                                            value="B" class="form-checkbox h-4 w-4 text-blue-600"
                                                            x-bind:checked="selectedReport.grade && selectedReport.grade.includes('B')">
                                                        <label for="grade_b" class="ml-2">Grade B</label>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input type="checkbox" id="grade_c" name="grade[]"
                                                            value="C" class="form-checkbox h-4 w-4 text-blue-600"
                                                            x-bind:checked="selectedReport.grade && selectedReport.grade.includes('C')">
                                                        <label for="grade_c" class="ml-2">Grade C</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="condition"
                                                class="block text-sm font-medium mb-1">Condition</label>
                                            <div class="grid grid-cols-3 gap-4">
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="condition_excellent"
                                                        name="condition[]" value="excellent"
                                                        class="form-checkbox h-4 w-4 text-blue-600"
                                                        x-bind:checked="selectedReport.condition && selectedReport.condition.includes(
                                                            'excellent')">
                                                    <label for="condition_excellent" class="ml-2">Excellent</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="condition_good" name="condition[]"
                                                        value="good" class="form-checkbox h-4 w-4 text-blue-600"
                                                        x-bind:checked="selectedReport.condition && selectedReport.condition.includes(
                                                            'good')">
                                                    <label for="condition_good" class="ml-2">Good</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="condition_damaged" name="condition[]"
                                                        value="damaged" class="form-checkbox h-4 w-4 text-blue-600"
                                                        x-bind:checked="selectedReport.condition && selectedReport.condition.includes(
                                                            'damaged')">
                                                    <label for="condition_damaged" class="ml-2">Damaged</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Post-Harvest Handling -->
                                <hr class="my-4">
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold mb-2">Post-Harvest Handling</h3>
                                    <div class="space-y-3">
                                        <div class="space-y-2">
                                            <p class="font-medium">Storage Location:</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="storage_a" name="storage_location[]"
                                                        value="A" class="form-checkbox h-4 w-4 text-blue-600"
                                                        x-bind:checked="selectedReport.storage_location && selectedReport
                                                            .storage_location.includes('A')">
                                                    <label for="storage_a" class="ml-2">Storage A</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="storage_b" name="storage_location[]"
                                                        value="B" class="form-checkbox h-4 w-4 text-blue-600"
                                                        x-bind:checked="selectedReport.storage_location && selectedReport
                                                            .storage_location.includes('B')">
                                                    <label for="storage_b" class="ml-2">Storage B</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="storage_c" name="storage_location[]"
                                                        value="C" class="form-checkbox h-4 w-4 text-blue-600"
                                                        x-bind:checked="selectedReport.storage_location && selectedReport
                                                            .storage_location.includes('C')">
                                                    <label for="storage_c" class="ml-2">Storage C</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="storage_d" name="storage_location[]"
                                                        value="D" class="form-checkbox h-4 w-4 text-blue-600"
                                                        x-bind:checked="selectedReport.storage_location && selectedReport
                                                            .storage_location.includes('D')">
                                                    <label for="storage_d" class="ml-2">Storage D</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="remarks"
                                                class="block text-sm font-medium mb-1">Remarks</label>
                                            <textarea id="remarks" name="remarks" rows="3" class="w-full border rounded p-2"
                                                placeholder="Enter any additional notes or observations..." x-text="selectedReport.remarks"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Signature Area -->
                                <div class="mt-6 flex justify-between">
                                    <div class="w-1/2">
                                        <p class="mb-1 font-semibold">Harvested By:</p>
                                        <p>_______________________</p>
                                    </div>
                                    <div class="w-1/2 text-right">
                                        <p class="mb-1 font-semibold">Verified By:</p>
                                        <p>_______________________</p>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" :disabled="isSubmitting"
                                class="bg-green-600 text-black px-4 py-2 rounded hover:bg-green-700 mt-4">
                                Save Details
                            </button>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Inventory Report Section -->
            <div id="inventoryReport" class="table-container hidden">
                <h3 class="text-lg font-bold mb-4">Inventory Report</h3>
                <!-- Add Chart Canvas for Inventory -->
                <div class="chart-container mb-6" style="height: 400px;">
                    <canvas id="inventoryChart"></canvas>
                </div>
                <table
                    class="w-full mb-8 min-w-full divide-y divide-gray-200 dark:divide-gray-700 overflow-hidden shadow-sm rounded-lg">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Storage Location</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Total Quantity</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($storageReports as $report)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    Storage {{ $report->storage_location }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->total_quantity }} kg</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $report->updated_at ? $report->updated_at->format('Y-m-d H:i:s') : 'Not Available' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3"
                                    class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No Storage
                                    Data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Pagination for Inventory Report -->
                <div class="mt-4">
                    {{ $storageReports->appends(request()->except('page'))->links() }}
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/moment"></script>
            <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script>
            <script>
                var chartData = @json($chartData);
                var harvestChartData = @json($harvestChartData);
                var inventoryData = @json($inventoryChartData);
                s
            </script>
            <script src="{{ asset('js/production.js') }}"></script>
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!-- Add this at the bottom of the file before closing x-app-layout -->
            <div class="fixed bottom-4 right-4 space-y-2" id="toast-container">
                <!-- Template for toast notification -->
                <div class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg transition-all duration-300"
                    x-data="{ show: false }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    x-transition:enter="transform ease-out duration-300 transition"
                    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium" id="toast-message"></span>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const activeTab = sessionStorage.getItem('activeTab') || 'recordFall';
                    const paginationLinks = document.querySelectorAll('.pagination a');

                    paginationLinks.forEach(link => {
                        const url = new URL(link.href);
                        url.searchParams.set('activeTab', activeTab);
                        link.href = url.toString();
                    });
                });
            </script>
</x-app-layout>
