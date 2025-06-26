@extends('layouts.app')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>DASHBOARD</h1>
        </div>

        <div class="container mx-auto p-4">
            <h2 class="text-xl font-bold mb-4">Grafik Penjualan 7 Hari Terakhir</h2>
            <canvas id="salesChart" height="100"></canvas>
        </div>

        <script>
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line', // atau 'bar', 'pie', dll
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Total Penjualan',
                        data: {!! json_encode($data) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            ticks: {
                                callback: function (value) {
                                    return 'Rp' + value.toLocaleString();
                                }
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
@endsection
