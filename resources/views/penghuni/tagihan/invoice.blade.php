<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $tagihan->labelPeriode() }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; color: #1a56db; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        
        .info-section { margin-bottom: 30px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 5px 0; vertical-align: top; }
        .label { font-weight: bold; width: 150px; color: #555; }
        
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .details-table th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 12px 10px; text-align: left; font-size: 13px; }
        .details-table td { border-bottom: 1px solid #e2e8f0; padding: 12px 10px; font-size: 13px; }
        
        .total-section { float: right; width: 250px; }
        .total-table { width: 100%; border-collapse: collapse; }
        .total-table td { padding: 5px 10px; }
        .total-label { font-weight: bold; text-align: right; }
        .total-amount { font-weight: bold; color: #1a56db; font-size: 18px; text-align: right; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .status-badge { display: inline-block; padding: 5px 15px; background: #def7ec; color: #03543f; font-weight: bold; border-radius: 5px; text-transform: uppercase; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KUITANSI PEMBAYARAN</h1>
        <p>E-PayKos - Sistem Pengelolaan Rumah Kos</p>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">ID Transaksi</td>
                <td>: #INV-{{ $tagihan->id }}-{{ date('Ymd') }}</td>
                <td style="text-align: right;">
                    <div class="status-badge">LUNAS</div>
                </td>
            </tr>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td>: {{ date('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Nama Penghuni</td>
                <td>: {{ $tagihan->penghuni->nama }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Kamar</td>
                <td>: Kamar {{ $tagihan->kamar->nomor_kamar }}</td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>DESKRIPSI</th>
                <th>PERIODE</th>
                <th style="text-align: right;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sewa Kamar Kos</td>
                <td>{{ $tagihan->labelPeriode() }}</td>
                <td style="text-align: right;">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <table class="total-table">
            <tr>
                <td class="total-label">TOTAL BAYAR</td>
                <td class="total-amount">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both; margin-top: 50px;">
        <p style="font-size: 12px;">Terima kasih atas pembayaran Anda. Dokumen ini adalah bukti pembayaran yang sah yang dihasilkan secara elektronik.</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} E-PayKos. All rights reserved.
    </div>
</body>
</html>
