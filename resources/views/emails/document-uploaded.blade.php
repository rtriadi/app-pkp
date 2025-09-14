<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Baru Diupload</title>
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
            border-bottom: 3px solid #6f42c1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #6f42c1;
            margin: 0;
            font-size: 24px;
        }

        .info-message {
            background-color: #e7e3ff;
            border: 1px solid #d4c9ff;
            color: #4c2889;
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

        .footer {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #495057; margin: 0;">PENGADILAN AGAMA</h2>
            <h1>📎 Dokumen Baru Diupload</h1>
        </div>

        <div class="info-message">
            📤 Dokumen pendukung baru telah diupload untuk penilaian kinerja
        </div>

        <p>Yth. Atasan Pejabat Penilai,</p>
        <p>Dokumen pendukung baru telah diupload oleh pegawai untuk penilaian kinerja bulanan.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/dashboard') }}" class="action-button">🔗 Tinjau Dokumen</a>
        </div>

        <div class="footer">
            <p><strong>APP PKP - Sistem Manajemen Kinerja Pegawai</strong></p>
            <p>&copy; {{ date('Y') }} Pengadilan Agama. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
