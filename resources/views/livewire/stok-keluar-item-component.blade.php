<div class="overflow-x-auto bg-white shadow-md rounded p-4">
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 shadow-sm">
        <h2 class="text-2xl font-semibold text-gray-800">Detail Stok Keluar</h2>
        <div class="mt-3 text-base text-gray-700 space-y-1">
            <p><strong>No Keluar:</strong> {{ $stokKeluar->no_keluar }}</p>
            <p><strong>Tanggal Keluar:</strong>
                {{ \Carbon\Carbon::parse($stokKeluar->tanggal_keluar)->timezone('Asia/Makassar')->format('d M Y, H:i') }}
            </p>
                <p><strong>Jenis:</strong> {{ ucfirst($stokKeluar->jenis) }}</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-2 bg-green-200 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif
</div>
@foreach ($items as $item)
    <tr>
        <td>{{ $item->produk->nama_produk ?? '-' }}</td>
        <td>{{ $item->jumlah }}</td>
        <td>{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
    </tr>
@endforeach
</div>
</div>

