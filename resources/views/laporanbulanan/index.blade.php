@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header justify-between">
            <h1>Laporan Bulanan {{ $tahun }}</h1>
            <a href="{{ route('laporanbulanan.export', ['tahun' => $tahun]) }}" class="btn btn-success">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
        </div>

        <div class="container mx-auto p-4">
            <div class="card shadow p-6 mb-6">
                <form method="GET" class="flex flex-col md:flex-row items-start md:items-center gap-2">
                    <div class="flex items-center gap-2">
                        <label for="tahun">Pilih Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select border rounded p-1">
                            @for ($i = 2025; $i <= 2030; $i++)
                                <option value="{{ $i }}" {{ $i == $tahun ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                    </button>
                </form>
            </div>

            <div class="card shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Grafik Penjualan, HPP, dan Laba</h2>
                <div style="height: 400px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [
                        {
                            label: 'Penjualan',
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            data: {!! json_encode($penjualan) !!}
                        },
                        {
                            label: 'HPP',
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            data: {!! json_encode($hpp) !!}
                        },
                        {
                            label: 'Laba',
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            data: {!! json_encode($laba) !!}
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'Rp ' + value.toLocaleString('id-ID')
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: context => 'Rp ' + Number(context.raw).toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        </script>
    </section>
@endsection
