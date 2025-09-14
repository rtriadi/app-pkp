<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perjanjian Kinerja Disetujui</title>
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
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #28a745;
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

        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
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
            <h1>🎉 Perjanjian Kinerja Disetujui</h1>
            <p>Sistem Manajemen Kinerja Pegawai</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $employee->name }}</strong>,</p>

            <div class="success-message">
                ✅ Perjanjian Kinerja Anda untuk tahun {{ $agreement->year }} telah <strong>DISETUJUI</strong>
            </div>

            <p>Dengan ini kami informasikan bahwa perjanjian kinerja Anda telah disetujui oleh pejabat yang berwenang.
                Anda dapat melanjutkan dengan pengisian penilaian kinerja bulanan sesuai dengan target yang telah
                disepakati.</p>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #495057;">Detail Perjanjian Kinerja:</h3>
                <div class="info-item">
                    <span class="info-label">Tahun:</span>
                    <span class="info-value">{{ $agreement->year }}</span>
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
                    <span class="info-label">Unit Kerja:</span>
                    <span class="info-value">{{ $employee->work_unit?->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Persetujuan:</span>
                    <span
                        class="info-value">{{ \Carbon\Carbon::parse($agreement->approved_at)->format('d F Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Disetujui Oleh:</span>
                    <span class="info-value">{{ $approver?->name ?? 'Administrator' }}</span>
                </div>
            </div>

            <h3 style="color: #495057;">Langkah Selanjutnya:</h3>
            <ol>
                <li>Akses sistem APP PKP untuk melihat detail perjanjian kinerja Anda</li>
                <li>Mulai mengisi penilaian kinerja bulanan sesuai jadwal yang telah ditentukan</li>
                <li>Upload dokumen pendukung jika diperlukan</li>
                <li>Monitor progress pencapaian target kinerja Anda</li>
            </ol>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/dashboard') }}" class="action-button">
                    🔗 Akses Sistem APP PKP
                </a>
                <a href="{{ route('reports.performance-agreement.pdf', $agreement->id) }}" class="action-button"
                    style="background-color: #28a745;">
                    📄 Download PDF Perjanjian
                </a>
            </div>

            <p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, silakan hubungi administrator sistem atau pejabat
                penilai Anda.</p>

            <p>Terima kasih atas komitmen dan dedikasi Anda dalam meningkatkan kinerja organisasi.</p>
        </div>

        <div class="footer">
            <p><strong>APP PKP - Sistem Manajemen Kinerja Pegawai</strong></p>
            <p>Pengadilan Agama | Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Pengadilan Agama. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
