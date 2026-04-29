<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <p class="no-print"><button type="button" onclick="window.print()">Cetak / simpan PDF</button></p>
    <h1>Laporan pembayaran lunas</h1>
    <p>{{ $dari->format('d/m/Y') }} — {{ $sampai->format('d/m/Y') }}</p>
    <p><strong>Total: Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Periode tagihan</th>
                <th class="right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $r->tanggal_bayar }}</td>
                    <td>{{ $r->penghuni?->nama }}</td>
                    <td>{{ $r->penghuni?->kamar?->nomor_kamar }}</td>
                    <td>{{ $r->tagihan ? $r->tagihan->labelPeriode() : '—' }}</td>
                    <td class="right">Rp {{ number_format($r->jumlah, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
