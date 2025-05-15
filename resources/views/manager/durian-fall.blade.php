<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Durian Fall Monitoring') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="{{ asset('css/manager/manager-durian.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-chart-line mr-2"></i> Durian Fall Analytics Dashboard
                </h1>
            </div>
            
            <!-- Orchard Summary Cards -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        <i class="fas fa-tree mr-2"></i> Durian Falls by Orchard
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($orchardFallCounts as $fallCount)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow summary-card">
                                <h4 class="summary-card-title">
                                    {{ $fallCount->orchard->orchardName ?? 'Unknown Orchard' }}
                                </h4>
                                <div class="mt-3 flex items-center">
                                    <span class="summary-card-value">
                                        {{ $fallCount->total_falls }}
                                    </span>
                                    <span class="ml-2 summary-card-label">
                                        Durian Falls
                                    </span>
                                </div>
                                <div class="mt-2 summary-card-meta">
                                    <i class="fas fa-microchip mr-1"></i> Device ID: {{ $fallCount->device_id }}
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full empty-state">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>No durian fall data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Line Chart -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        <i class="fas fa-chart-line mr-2"></i> Durian Falls Over Time
                    </h3>
                    <div class="chart-customization">
                        <canvas id="durianFallChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Records Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        <i class="fas fa-table mr-2"></i> Detailed Fall Records
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 data-table">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-microchip mr-1"></i> Device ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-tree mr-1"></i> Orchard
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-sort-numeric-down mr-1"></i> Fall Count
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-clock mr-1"></i> Timestamp
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($vibrationLogs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $log->device_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $log->orchard->orchardName ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full">
                                                {{ $log->vibration_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No Vibration Data
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

    <div class="pagination-container mt-4">
        {{ $vibrationLogs->appends(request()->except('page'))->links() }}
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);
            
            const ctx = document.getElementById('durianFallChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'Number of Durian Falls',
                        data: chartData.map(item => item.count),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12,
                                    family: "'Inter', 'Helvetica', 'Arial', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            cornerRadius: 6,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Falls',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        }
                    }
                }
            });
            
            // Handle dark mode toggle
            const darkModeObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        const isDarkMode = document.documentElement.classList.contains('dark');
                        
                        chart.options.scales.x.grid.color = isDarkMode ? 'rgba(156, 163, 175, 0.1)' : 'rgba(229, 231, 235, 0.5)';
                        chart.options.scales.y.grid.color = isDarkMode ? 'rgba(156, 163, 175, 0.1)' : 'rgba(229, 231, 235, 0.5)';
                        chart.options.scales.x.ticks.color = isDarkMode ? '#9ca3af' : '#6b7280';
                        chart.options.scales.y.ticks.color = isDarkMode ? '#9ca3af' : '#6b7280';
                        chart.update();
                    }
                });
            });
            
            darkModeObserver.observe(document.documentElement, { attributes: true });
        });
    </script>
</x-app-layout>
