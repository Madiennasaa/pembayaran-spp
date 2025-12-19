<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kwitansi - {{ $p->id }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            color: #333;
        }

        .receipt-card {
            background: #fff;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .content-table td {
            padding: 10px 0;
            font-size: 16px;
            border-bottom: 1px solid #fafafa;
        }

        /* Area Tanda Tangan */
        .footer-sign {
            margin-top: 40px;
            text-align: right;
        }

        .sign-container {
            display: inline-block;
            text-align: center;
            width: 220px;
        }

        .signature-wrapper {
            height: 100px;
            margin-top: 5px;

            /* JANGAN FLEX */
            text-align: center;

            /* FORCE render di modal */
            position: relative;
            z-index: 9999;
        }

        .signature-img {
            max-height: 85px;
            max-width: 100%;
            display: inline-block;

            /* FIX bug Chrome modal */
            transform: none !important;
            filter: none !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .sign-name {
            font-weight: bold;
            text-decoration: underline;
            display: block;
            margin-top: 5px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-card {
                box-shadow: none;
                border: 1px solid #eee;
                width: 100%;
                max-width: 100%;
            }

            .no-print {
                display: none;
            }

            img {
                display: block !important;
            }
        }
    </style>
</head>

<body>

    <div class="receipt-card">
        <div class="header">
            <h2>KWITANSI PEMBAYARAN SPP</h2>
            <p>No: KW-{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

        <table class="content-table">
            <tr>
                <td width="150">Telah Terima Dari</td>
                <td width="20">:</td>
                <td><strong>{{ $p->murid->nama_lengkap ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Untuk Pembayaran</td>
                <td>:</td>
                <td>SPP Bulan {{ $p->bulan_spp }} {{ $p->tahun_spp }}</td>
            </tr>
            <tr>
                <td>Sejumlah</td>
                <td>:</td>
                <td style="font-size: 20px; font-weight: bold;">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Tanggal Bayar</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        <div class="footer-sign">
            <div class="sign-container">
                <span>Bendahara,</span>
                <div style="height: 95px; display: flex; align-items: center; justify-content: center;">
                    @if ($signatureUrl)
                        <img src="{{ $signatureUrl }}" class="signature-img">
                    @else
                        <div style="color:red; font-size: 11px;">(File TTD Tidak Terbaca)</div>
                    @endif
                </div>
                <span class="sign-name">{{ $bendaharaName }}</span>
            </div>
        </div>

        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()"
                style="padding: 10px 25px; cursor: pointer; background: #000; color: #fff; border: none; border-radius: 5px;">Cetak
                Kwitansi</button>
        </div>
    </div>

</body>

<script>
    window.onload = function() {
        const img = document.querySelector('.signature-img');

        if (!img) {
            window.print();
            return;
        }

        // Jika gambar sudah siap
        if (img.complete) {
            setTimeout(() => window.print(), 300);
        } else {
            img.onload = function() {
                setTimeout(() => window.print(), 300);
            };
            img.onerror = function() {
                window.print();
            };
        }
    };
</script>

</html>
