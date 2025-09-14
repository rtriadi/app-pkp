<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Rekapitulasi Penilaian Capaian Kinerja</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .header h2 {
            font-size: 14px;
            margin: 5px 0;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            border: 1px solid #000;
        }

        .info-table .label {
            width: 30%;
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .recap-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .recap-table th,
        .recap-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .recap-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .summary-section {
            margin-top: 30px;
            text-align: center;
        }

        .summary-table {
            width: 70%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        .summary-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .summary-table .label {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .signature-section {
            margin-top: 50px;
            text-align: center;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            margin-top: 60px;
        }

        .signature-box .title {
            margin-bottom: 60px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PENGADILAN AGAMA</h1>
        <h2>FORMULIR REKAPITULASI PENILAIAN CAPAIAN KINERJA</h2>
        <h2>TAHUN {{ $year }}</h2>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">Nama Pegawai</td>
                <td>: {{ $employee->name }}</td>
            </tr>
            <tr>
                <td class="label">NIP</td>
                <td>: {{ $employee->nip }}</td>
            </tr>
            <tr>
                <td class="label">Pangkat/Golongan</td>
                <td>: {{ $employee->rank_grade }}</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td>: {{ $employee->position }}</td>
            </tr>
            <tr>
                <td class="label">Unit Kerja</td>
                <td>: {{ $employee->workUnit->name ?? '' }}</td>
            </tr>
        </table>
    </div>

    <table class="recap-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No</th>
                <th rowspan="2" style="width: 15%;">Bulan</th>
                <th colspan="2">Nilai Capaian Kinerja</th>
                <th rowspan="2" style="width: 15%;">Grade</th>
                <th rowspan="2" style="width: 25%;">Pejabat Penilai</th>
                <th rowspan="2" style="width: 25%;">Atasan Pejabat Penilai</th>
            </tr>
            <tr>
                <th>Angka</th>
                <th>Huruf</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalScore = 0;
                $assessmentCount = 0;
            @endphp
            @foreach ($assessments as $index => $assessment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper(date('F', mktime(0, 0, 0, $assessment->month, 1))) }}</td>
                    <td>{{ number_format($assessment->total_score, 2) }}</td>
                    <td>{{ $assessment->grade }}</td>
                    <td>{{ $assessment->grade }}</td>
                    <td>{{ $assessment->assessor->name ?? '' }}</td>
                    <td>{{ $assessment->supervisor->name ?? '' }}</td>
                </tr>
                @php
                    $totalScore += $assessment->total_score;
                    $assessmentCount++;
                @endphp
            @endforeach
            @if ($assessmentCount > 0)
                <tr style="font-weight: bold;">
                    <td colspan="2">RATA-RATA</td>
                    <td>{{ number_format($totalScore / $assessmentCount, 2) }}</td>
                    <td colspan="4">-</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="summary-section">
        <h3>RINGKASAN PENILAIAN TAHUNAN</h3>
        <table class="summary-table">
            <tr>
                <td class="label">Jumlah Penilaian</td>
                <td>{{ $assessmentCount }}</td>
            </tr>
            <tr>
                <td class="label">Total Nilai</td>
                <td>{{ number_format($totalScore, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Rata-rata Nilai</td>
                <td>{{ $assessmentCount > 0 ? number_format($totalScore / $assessmentCount, 2) : 0 }}</td>
            </tr>
            <tr>
                <td class="label">Grade Keseluruhan</td>
                <td>
                    @php
                        $avgScore = $assessmentCount > 0 ? $totalScore / $assessmentCount : 0;
                        if ($avgScore >= 91) {
                            echo 'Sangat Baik';
                        } elseif ($avgScore >= 76) {
                            echo 'Baik';
                        } elseif ($avgScore >= 61) {
                            echo 'Cukup';
                        } elseif ($avgScore >= 51) {
                            echo 'Kurang';
                        } else {
                            echo 'Sangat Kurang';
                        }
                    @endphp
                </td>
            </tr>
        </table>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="title">Pejabat yang Berwenang</div>
        </div>
    </div>
</body>

</html>
