<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Bulanan Diajukan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            margin: 20px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #17a2b8;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #17a2b8;
            margin: 0;
            font-size: 24px;
        }

        .header p {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .content {
            margin-bottom: 30px;
        }

        .info-message {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 140px;
        }

        .info-value {
            color: #212529;
        }

        .action-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: bold;
        }

        .action-button:hover {
            background-color: #0056b3;
        }

        .footer {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <h2 style="color: #495057; margin: 0;">PENGADILAN AGAMA</h2>
            </div>
            <h1>📋 Penilaian Bulanan Diajukan</h1>
            <p>Sistem Manajemen Kinerja Pegawai</p>
        </div>

        <div class="content">
            <p>Yth. Atasan Pejabat Penilai,</p>

            <div class="info-message">
                📤 Penilaian kinerja bulanan telah diajukan untuk ditinjau
            </div>

            <p>Dengan ini kami informasikan bahwa terdapat penilaian kinerja bulanan yang telah diajukan oleh pegawai
                dan memerlukan persetujuan dari Anda.</p>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #495057;">Detail Penilaian:</h3>
                <div class="info-item">
                    <span class="info-label">Pegawai:</span>
                    <span class="info-value">{{ $employee->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">NIP:</span>
                    <span class="info-value">{{ $employee->nip }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jabatan:</span>
                    <span class="info-value">{{ $employee->position }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bulan/Tahun:</span>
                    <span class="info-value">{{ $monthName }} {{ $assessment->year }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nilai Capaian:</span>
                    <span class="info-value">{{ number_format($assessment->total_score, 2) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Grade:</span>
                    <span class="info-value">{{ $assessment->grade }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Pengajuan:</span>
                    <span
                        class="info-value">{{ \Carbon\Carbon::parse($assessment->updated_at)->format('d F Y H:i') }}</span>
                </div>
            </div>

            <h3 style="color: #495057;">Tindakan yang Diperlukan:</h3>
            <ol>
                <li>Akses sistem APP PKP untuk meninjau detail penilaian</li>
                <li>Verifikasi data penilaian yang telah diajukan</li>
                <li>Berikan persetujuan atau revisi jika diperlukan</li>
                <li>Berikan feedback kepada pegawai terkait</li>
            </ol>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/dashboard') }}" class="action-button">
                    🔗 Akses Sistem APP PKP
                </a>
                <a href="{{ route('reports.monthly-assessment.pdf', $assessment->id) }}" class="action-button"
                    style="background-color: #28a745;">
                    📄 Lihat PDF Penilaian
                </a>
            </div>

            <p>Penilaian kinerja yang telah disetujui akan menjadi bagian dari rekam jejak kinerja pegawai dan akan
                mempengaruhi penilaian akhir tahun.</p>

            <p>Terima kasih atas perhatian dan waktu Anda dalam meninjau penilaian kinerja pegawai.</p>
        </div>

        <div class="footer">
            <p><strong>APP PKP - Sistem Manajemen Kinerja Pegawai</strong></p>
            <p>Pengadilan Agama | Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Pengadilan Agama. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
