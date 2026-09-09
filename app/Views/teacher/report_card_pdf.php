<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cauayan South Central School - Student Report Card</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 10px 30px;
            color: #000;
            line-height: 1.6;
            font-size: 14px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        
        .school-name {
            font-size: 22px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }
        
        .student-info {
            display: table;
            width: 100%;
            margin: 25px 0;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label,
        .info-value {
            display: table-cell;
            padding: 5px 8px;
            border: 1px solid #000;
            font-size: 14px;
        }
        
        .info-label {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 20%;
        }
        
        .info-value {
            width: 30%;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        
        .grades-table th,
        .grades-table td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
            font-size: 12px;
        }
        
        .grades-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .grades-table .subject-name {
            text-align: left;
            font-weight: bold;
        }
        

        

        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .signature-section {
            margin-top: 15px;
            display: table;
            width: 100%;
        }
        
        .signature-row {
            display: table-row;
        }
        
        .signature-cell {
            display: table-cell;
            width: 50%;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 25px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <?php $logoB64 = (($logoBase64 ?? '') !== '') ? $logoBase64 : school_logo_base64(); ?>
            <?php if ($logoB64 !== ''): ?>
                <img src="data:image/png;base64,<?= esc($logoB64, 'attr') ?>" alt="CSCS Logo" style="width: 80px; height: 80px; margin: 0 auto; display: block;">
            <?php else: ?>
                <svg width="80" height="80" viewBox="0 0 80 80" style="margin: 0 auto; display: block;">
                    <circle cx="40" cy="40" r="38" fill="#f0f0f0" stroke="#000" stroke-width="2"/>
                    <text x="40" y="45" font-size="18" font-weight="bold" text-anchor="middle" fill="#000">CSCS</text>
                </svg>
            <?php endif; ?>
        </div>
        <div class="school-name">CAUAYAN SOUTH CENTRAL SCHOOL</div>
        <div class="report-title">Student Report Card</div>
        <div style="font-size: 16px; margin: 10px 0;">School Year: <?= $schoolYear ?></div>
        <div style="font-size: 16px; margin: 10px 0;">Report Generated: <?= $reportDate ?></div>
    </div>

    <div class="student-info">
        <div class="info-row">
            <div class="info-label">Student Name:</div>
            <div class="info-value"><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></div>
            <div class="info-label">LRN:</div>
            <div class="info-value"><?= esc($student['lrn']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Grade & Section:</div>
            <div class="info-value"><?= esc(grade_level_label((int) ($student['grade_level'] ?? 0))) ?> - <?= esc($student['section_name'] ?? 'Not Assigned') ?></div>
            <div class="info-label">Adviser:</div>
            <div class="info-value"><?= esc($student['adviser_name'] ?? 'Not Assigned') ?></div>
        </div>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th rowspan="2">Subject</th>
                <th colspan="3">Term Grades</th>
                <th rowspan="2">Final Grade</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>Term 1</th>
                <th>Term 2</th>
                <th>Term 3</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $subject): ?>
            <tr>
                <td class="subject-name"><?= esc($subject['subject_name']) ?></td>
                <?php for ($term = 1; $term <= 3; $term++): ?>
                    <td>
                    <?php
                    $grade = $grades[$term][$subject['id']] ?? null;
                    if ($grade !== null) {
                        if (is_numeric($grade)) {
                            echo number_format((float)$grade, 0);
                        } else {
                            echo esc($grade);
                        }
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                <?php endfor; ?>
                <td>
                    <?php
                    $subjectGrades = [];
                    for ($t = 1; $t <= 3; $t++) {
                        if (isset($grades[$t][$subject['id']]) && $grades[$t][$subject['id']] !== null) {
                            $subjectGrades[] = $grades[$t][$subject['id']];
                        }
                    }
                    // Only average numeric grades, ignore symbols
                    $numericGrades = array_filter($subjectGrades, function($g) { return is_numeric($g); });
                    $subjectFinal = !empty($numericGrades) ? array_sum($numericGrades) / count($numericGrades) : 0;
                    echo $subjectFinal > 0 ? number_format($subjectFinal, 0) : '-';
                    ?>
                </td>
                <td>
                    <?php
                    if ($subjectFinal >= 75) {
                        echo 'PASSED';
                    } elseif ($subjectFinal > 0) {
                        echo 'FAILED';
                    } else {
                        echo 'NO GRADE';
                    }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td>TOTAL</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td><?= $finalAverage > 0 ? number_format($finalAverage, 1) : '-' ?></td>
                <td><?= $finalAverage >= 75 ? 'PASSED' : ($finalAverage > 0 ? 'FAILED' : 'NO GRADE') ?></td>
            </tr>
        </tfoot>
    </table>





    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-cell">
                <div class="signature-line"></div>
                <div><strong>Class Adviser</strong></div>
                <div><?= esc($student['adviser_name'] ?? 'Not Assigned') ?></div>
            </div>
            <div class="signature-cell">
                <div class="signature-line"></div>
                <div><strong>Principal</strong></div>
                <div>Cauayan South Central School</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Cauayan South Central School - Student Report Card | Generated on <?= date('F j, Y \\a\\t g:i A') ?></p>
        <p>This document contains confidential student information. Handle with care and maintain privacy.</p>
    </div>
</body>
</html>
