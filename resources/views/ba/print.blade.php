<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Berita Acara - {{ $ba->ba_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #525659;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .a4-container {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            box-sizing: border-box;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 14px;
            margin: 5px 0 0 0;
        }

        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            text-decoration: underline;
            margin: 30px 0 10px 0;
            text-transform: uppercase;
        }

        .document-number {
            text-align: center;
            margin-bottom: 30px;
        }

        .content {
            font-size: 14px;
            line-height: 1.6;
            text-align: justify;
        }

        .table-info {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .table-info td {
            vertical-align: top;
            padding: 4px 0;
        }

        .table-info td:first-child {
            width: 30%;
            font-weight: bold;
        }

        .table-info td:nth-child(2) {
            width: 5%;
        }

        .chronology {
            margin-bottom: 40px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 80px;
        }

        /* Tampilan hanya saat diprint */
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            .a4-container {
                box-shadow: none;
                width: 100%;
                min-height: 100%;
                padding: 0;
            }
            @page {
                size: A4;
                margin: 20mm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="a4-container">
        
        <div class="header">
            <h1>GANDARIA CITY</h1>
            <p>Jl. Sultan Iskandar Muda, RT.10/RW.6, Kebayoran Lama Utara, Jakarta Selatan</p>
        </div>

        <div class="document-title">
            BERITA ACARA {{ strtoupper($ba->ba_type) }}
        </div>
        <div class="document-number">
            No: {{ $ba->ba_number }}
        </div>

        <div class="content">
            <p>Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($ba->incident_date)->translatedFormat('d F Y') }}</strong>, telah dibuat laporan resmi mengenai insiden yang terjadi di area properti Gandaria City, dengan keterangan sebagai berikut:</p>
            
            <table class="table-info">
                <tr>
                    <td>Kategori Kejadian</td>
                    <td>:</td>
                    <td>{{ $ba->ba_type }}</td>
                </tr>
                <tr>
                    <td>Pihak/Pelanggan Terkait</td>
                    <td>:</td>
                    <td>{{ $ba->customer_name }}</td>
                </tr>
                <tr>
                    <td>Plat Nomor Kendaraan</td>
                    <td>:</td>
                    <td>{{ $ba->license_plate ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Status Dokumen</td>
                    <td>:</td>
                    <td>{{ $ba->status }}</td>
                </tr>
                <tr>
                    <td>Waktu Persetujuan CPM</td>
                    <td>:</td>
                    <td>{{ $ba->approved_at ? \Carbon\Carbon::parse($ba->approved_at)->translatedFormat('d F Y, H:i') : 'Belum Diverifikasi' }}</td>
                </tr>
            </table>

            <p style="font-weight: bold; text-decoration: underline; margin-bottom: 5px;">Kronologi Kejadian:</p>
            <div class="chronology">
                {!! nl2br(e($ba->chronology)) !!}
            </div>

            <p>Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <div>Dibuat / Dilaporkan Oleh:</div>
                <div class="signature-name">{{ $ba->pic ? $ba->pic->name : '_________________' }}</div>
                <div>({{ $ba->pic ? $ba->pic->role : 'PIC' }})</div>
            </div>

            <div class="signature-box">
                <div>Mengetahui / Menyetujui:</div>
                <div class="signature-name">Manajemen CPM</div>
                <div>GANDARIA CITY</div>
            </div>
        </div>

    </div>

</body>
</html>
