<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Penilaian Capaian Kinerja Bulanan</title>
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

        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .assessment-table th,
        .assessment-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .assessment-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .assessment-table .left {
            text-align: left;
        }

        .summary-section {
            margin-top: 30px;
            text-align: center;
        }

        .summary-table {
            width: 50%;
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
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-box .title {
            margin-bottom: 60px;
            font-weight: bold;
        }

        .notes-section {
            margin-top: 30px;
        }

        .notes-box {
            border: 1px solid #000;
            padding: 10px;
            min-height: 60px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PENGADILAN AGAMA</h1>
        <h2>FORMULIR PENILAIAN CAPAIAN KINERJA BULANAN</h2>
        <h2>BULAN {{ strtoupper(date('F', mktime(0, 0, 0, $assessment->month, 1))) }} TAHUN {{ $assessment->year }}</h2>
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

    <table class="assessment-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No</th>
                <th rowspan="2" style="width: 25%;">Sasaran Kegiatan</th>
                <th rowspan="2" style="width: 15%;">Indikator Kinerja</th>
                <th colspan="3">Target</th>
                <th colspan="3">Realisasi</th>
                <th rowspan="2" style="width: 8%;">Nilai Capaian Kinerja</th>
            </tr>
            <tr>
                <th>Kuantitas</th>
                <th>Satuan</th>
                <th>Mutu</th>
                <th>Kuantitas</th>
                <th>Satuan</th>
                <th>Mutu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="left">{{ $detail->performanceTarget->activity_goal }}</td>
                    <td class="left">{{ $detail->performanceTarget->performance_indicator }}</td>
                    <td>{{ $detail->performanceTarget->quantity_target }}</td>
                    <td>{{ $detail->performanceTarget->unit }}</td>
                    <td>{{ $detail->performanceTarget->quality_target }}%</td>
                    <td>{{ $detail->quantity_realization }}</td>
                    <td>{{ $detail->performanceTarget->unit }}</td>
                    <td>{{ $detail->quality_realization }}%</td>
                    <td>{{ number_format($detail->score, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="label">Total Nilai</td>
                <td>{{ number_format($assessment->total_score, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Grade</td>
                <td>{{ $assessment->grade }}</td>
            </tr>
        </table>
    </div>

    <div class="notes-section">
        <strong>Catatan:</strong>
        <div class="notes-box">
            {{ $assessment->notes ?? '-' }}
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="title">Pejabat Penilai</div>
            <div>{{ $assessor->name ?? '' }}</div>
        </div>
        <div class="signature-box">
            <div class="title">Atasan Pejabat Penilai</div>
            <div>{{ $supervisor->name ?? '' }}</div>
        </div>
    </div>
</body>

</html>
