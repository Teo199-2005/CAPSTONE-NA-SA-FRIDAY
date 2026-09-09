<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CSCS Student Nutrition Report</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 16px;
            color: #000;
            font-size: 8px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
        }
        .logo { width: 64px; height: 64px; margin: 0 auto 8px; }
        .school-name { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .report-title { font-size: 12px; font-weight: bold; margin: 8px 0 4px; }
        .report-info { font-size: 9px; margin: 2px 0; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; font-weight: bold; }
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 7px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <?php
            $logoB64 = school_logo_base64();
            if ($logoB64 !== '') {
                echo '<img src="data:image/png;base64,' . esc($logoB64, 'attr') . '" alt="Logo" style="width:64px;height:64px;display:block;margin:0 auto;">';
            } else {
                echo '<div style="width:64px;height:64px;border:2px solid #000;border-radius:50%;margin:0 auto;line-height:60px;text-align:center;font-weight:bold;">CSCS</div>';
            }
            ?>
        </div>
        <div class="school-name">Cauayan South Central School</div>
        <div class="report-title">Student nutrition / BMI report</div>
        <div class="report-info">School year: <?= esc($schoolYear) ?></div>
        <div class="report-info">Generated: <?= esc($reportDate) ?></div>
        <div class="report-info">Filters: <?= esc($filtersSummary) ?></div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>LRN</th>
                <th>Gr</th>
                <th>Section</th>
                <th>Age</th>
                <th>Sex</th>
                <th>H cm</th>
                <th>W kg</th>
                <th>BMI</th>
                <th>Ethnicity</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php helper('nutrition'); ?>
            <?php foreach ($students as $s): ?>
                <?php
                $ageY = '—';
                if (! empty($s['date_of_birth'])) {
                    try {
                        $dob = new \DateTimeImmutable($s['date_of_birth']);
                        $ageY = (string) $dob->diff(new \DateTimeImmutable('today'))->y;
                    } catch (\Throwable) {
                        $ageY = '—';
                    }
                }
                $name = trim($s['first_name'] . ' ' . ($s['middle_name'] ? $s['middle_name'] . ' ' : '') . $s['last_name']);
                $stLabel = ! empty($s['nutrition_status'])
                    ? \App\Libraries\StudentNutritionClassifier::statusLabel($s['nutrition_status'])
                    : '—';
                ?>
                <tr>
                    <td><?= esc($name) ?></td>
                    <td><?= esc((string) ($s['lrn'] ?? '')) ?></td>
                    <td><?= esc((string) ($s['grade_level'] ?? '')) ?></td>
                    <td><?= esc((string) ($s['section_name'] ?? '')) ?></td>
                    <td><?= esc($ageY) ?></td>
                    <td><?= esc(substr((string) ($s['gender'] ?? ''), 0, 1)) ?></td>
                    <td><?= esc((string) ($s['height_cm'] ?? '')) ?></td>
                    <td><?= esc((string) ($s['weight_kg'] ?? '')) ?></td>
                    <td><?= esc((string) ($s['bmi'] ?? '')) ?></td>
                    <td><?= esc(ethnicity_option_label($s['ethnicity'] ?? null)) ?></td>
                    <td><?= esc($stLabel) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>For school health screening and planning only — not a medical diagnosis. Confidential.</p>
        <p>Cauayan South Central School | <?= esc(date('F j, Y \a\t g:i A')) ?></p>
    </div>
</body>
</html>
