<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cauayan South Central School - Progress Report Card</title>
    <style>
        @page { margin: 10mm 10mm 12mm 10mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; color: #000; line-height: 1.2; margin: 0; padding: 5px 10px; }
        .header { text-align: center; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 2px solid #000; }
        .logo { width: 50px; height: 50px; margin: 0 auto 4px; display: block; }
        .school-name { font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 2px 0; }
        .report-title { font-size: 9pt; font-weight: bold; margin: 2px 0; }
        .student-info { width: 100%; border-collapse: collapse; margin: 5px 0; font-size: 7pt; }
        .student-info td { padding: 2px 5px; border: 1px solid #333; }
        .student-info .label { background: #f0f0f0; font-weight: bold; width: 15%; }
        .student-info .value { width: 35%; }
        .legend { border: 1px solid #999; padding: 2px 5px; margin: 4px 0; font-size: 7pt; }
        .category-section { margin: 2px 0; }
        .category-header { background: #e8e8e8; font-weight: bold; padding: 2px 5px; font-size: 7.5pt; border: 1px solid #999; }
        .grades-table { width: 100%; border-collapse: collapse; font-size: 6.5pt; margin-bottom: 1px; }
        .grades-table th { background: #f0f0f0; border: 1px solid #999; padding: 1px 2px; text-align: center; font-size: 6.5pt; font-weight: bold; }
        .grades-table td { border: 1px solid #999; padding: 1px 2px; }
        .grades-table .indicator { text-align: left; padding-left: 3px; }
        .grades-table .grade-cell { text-align: center; font-weight: bold; }
        .signature-section { margin-top: 10px; }
        .signature-table { width: 100%; border-collapse: collapse; font-size: 7pt; }
        .signature-table td { text-align: center; padding: 3px; width: 25%; }
        .signature-line { border-bottom: 1px solid #000; height: 18px; margin-bottom: 2px; }
        .footer { text-align: center; font-size: 6pt; color: #666; border-top: 1px solid #999; padding-top: 3px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <?php if (isset($logoBase64) && $logoBase64): ?>
            <img src="data:image/png;base64,<?= $logoBase64 ?>" alt="CSCS Logo" class="logo">
        <?php endif; ?>
        <div class="school-name">CAUAYAN SOUTH CENTRAL SCHOOL</div>
        <div class="report-title">Progress Report Card (Non-Numerical)</div>
        <div style="font-size: 6.5pt;">School Year: <strong><?= $schoolYear ?></strong> | Report Date: <strong><?= $reportDate ?></strong></div>
    </div>
    
    <table class="student-info">
        <tr>
            <td class="label">Student Name:</td>
            <td class="value"><strong><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></strong></td>
            <td class="label">LRN:</td>
            <td class="value"><?= esc($student['student_id'] ?? ($student['lrn'] ?? 'N/A')) ?></td>
        </tr>
        <tr>
            <td class="label">Grade/Section:</td>
            <td class="value"><?= esc($section['section_name'] ?? 'N/A') ?></td>
            <td class="label">Gender:</td>
            <td class="value"><?= esc($student['gender'] ?? 'N/A') ?></td>
        </tr>
    </table>
    
    <div class="legend">
        <strong>Legend:</strong>&nbsp;
        <?php foreach ($gradeSymbols as $gs): ?>
            <?= esc($gs['symbol']) ?>=<?= esc($gs['label']) ?>;
        <?php endforeach; ?>
    </div>
    
    <?php foreach ($categories as $category): ?>
    <div class="category-section">
        <div class="category-header"><?= esc($category['name']) ?></div>
        <?php if (!empty($category['fields'])): ?>
        <table class="grades-table">
            <thead>
                <tr>
                    <th style="width: 15px;">#</th>
                    <th style="text-align: left;">Performance indicators</th>
                    <?php foreach ($quarters as $q): ?>
                    <th style="width: 25px;"><?= $q ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($category['fields'] as $index => $field): ?>
                <tr>
                    <td style="text-align: center;"><?= $index + 1 ?></td>
                    <td class="indicator"><?= esc($field['field_name']) ?></td>
                    <?php foreach ($quarters as $q): ?>
                    <td class="grade-cell">
                        <?php
                        $symbol = $allGrades[$field['id']][$q]['grade_symbol'] ?? '';
                        echo $symbol ? esc($symbol) : '-';
                        ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <strong>Class Adviser</strong><br>____________________
                </td>
                <td>
                    <div class="signature-line"></div>
                    <strong>School Principal</strong><br>Cauayan South Central School
                </td>
                <td>
                    <div class="signature-line"></div>
                    <strong>Parent/Guardian</strong>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <strong>Student</strong>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        Cauayan South Central School - Progress Report Card | Generated on <?= $reportDate ?><br>
        This document contains confidential student information.
    </div>
</body>
</html>