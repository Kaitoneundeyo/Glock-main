@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>DASHBOARD</h1>
        </div>

        <div class="container mx-auto p-4">
            <div class="card shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Grafik Penjualan 14 Hari Terakhir</h2>
                <div style="height: 400px;"> <!-- Tinggi grafik ditingkatkan -->
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Total Penjualan (Rp)',
                        data: {!! json_encode($data) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Supaya tinggi tetap berlaku
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'Rp ' + Number(context.raw).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'Rp ' + value.toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        </script>
    </section>
@endsection
