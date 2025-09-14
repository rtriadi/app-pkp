<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Bulanan Disetujui</title>
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
            <h1>✅ Penilaian Bulanan Disetujui</h1>
        </div>

        <div class="success-message">
            🎉 Penilaian kinerja bulanan Anda untuk {{ $monthName }} {{ $assessment->year }} telah
            <strong>DISETUJUI</strong>
        </div>

        <p>Yth. <strong>{{ $employee->name }}</strong>,</p>
        <p>Penilaian kinerja bulanan Anda telah ditinjau dan disetujui oleh atasan pejabat penilai.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/dashboard') }}" class="action-button">🔗 Akses Sistem APP PKP</a>
        </div>

        <div class="footer">
            <p><strong>APP PKP - Sistem Manajemen Kinerja Pegawai</strong></p>
            <p>&copy; {{ date('Y') }} Pengadilan Agama. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
