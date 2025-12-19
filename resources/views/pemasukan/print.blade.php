<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemasukan SPP</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h3 { margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #e0e0e0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-2 { margin-top: 8px; }
    </style>
</head>
<body onload="window.print()">
    <h3>Laporan Pemasukan SPP</h3>
    <div class="mt-2">Total Transaksi: {{ $totalTransaksi }}</div>
    <div class="mt-2">Total Lunas: Rp {{ number_format($totalLunas, 0, ',', '.') }}</div>

    <table class="mt-2">
        <thead>
            <tr>
                <th class="text-center" style="width:5%">No</th>
                <th style="width:25%">Siswa</th>
                <th style="width:15%">Periode</th>
                <th style="width:15%">Tanggal Bayar</th>
                <th style="width:10%">Nominal</th>
                <th style="width:10%">Metode</th>
                <th style="width:10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemasukans as $i => $p)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $p->murid->nama_lengkap ?? 'Siswa Terhapus' }}<br><small>NISN: {{ $p->nisn }}</small></td>
                    <td class="text-center">{{ $p->bulan_spp }} {{ $p->tahun_spp }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d-m-Y') }}</td>
                    <td class="text-right">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                    <td class="text-center">{{ strtoupper($p->metode_pembayaran) }}</td>
                    <td class="text-center">{{ strtoupper($p->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
