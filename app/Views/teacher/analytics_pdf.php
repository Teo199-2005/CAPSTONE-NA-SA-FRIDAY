<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CSCS Teacher Analytics Report</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0 5px 0;
        }
        
        .report-info {
            font-size: 12px;
            margin: 5px 0;
        }
        
        .section {
            margin: 25px 0;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .metric-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        
        .metric-row {
            display: table-row;
        }
        
        .metric-label,
        .metric-value {
            display: table-cell;
            padding: 5px 10px;
            border: 1px solid #000;
        }
        
        .metric-label {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 60%;
        }
        
        .metric-value {
            text-align: right;
            width: 40%;
        }
        
        .summary-box {
            border: 2px solid #000;
            padding: 15px;
            margin: 20px 0;
            background-color: #f9f9f9;
        }
        
        .footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 8px;
            background: white;
            z-index: 1000;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <?php
            $logoB64 = school_logo_base64();
            if ($logoB64 !== '') {
                echo '<img src="data:image/png;base64,' . esc($logoB64, 'attr') . '" alt="School Logo" style="width: 80px; height: 80px; margin: 0 auto; display: block;">';
            } else {
                echo '<div style="width: 80px; height: 80px; border: 3px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; background: #f0f0f0;"><strong style="font-size: 18px;">CSCS</strong></div>';
            }
            ?>
        </div>
        <div class="school-name">Cauayan South Central School</div>
        <div class="report-title">Teacher Class Analytics Report</div>
        <div class="report-info">Teacher: <?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></div>
        <div class="report-info">School Year: <?= $schoolYear ?> | Term: <?= $currentTerm ?></div>
        <div class="report-info">Report Generated: <?= $reportDate ?> at <?= $reportTime ?? date('g:i A') ?></div>
    </div>

    <div class="section">
        <div class="section-title">Class Overview</div>
        <div class="summary-box">
            <p><strong>Total Students:</strong> <?= $analytics['totalStudents'] ?></p>
            <p><strong>Total Subjects:</strong> <?= $analytics['totalSubjects'] ?></p>
            <p><strong>Class Average:</strong> <?= number_format($analytics['classAverage'], 1) ?>%</p>
            <p><strong>Attendance Rate:</strong> <?= number_format($analytics['attendanceRate'], 1) ?>%</p>
            <p><strong>Improvement Rate:</strong> +<?= number_format($analytics['improvementRate'], 1) ?>%</p>
        </div>
    </div>

    <?php
    $gradedForDist = (int) ($analytics['studentsGradedForDistribution'] ?? 0);
    $distDenom = max(1, $gradedForDist);
    ?>
    <div class="section">
        <div class="section-title">Grade Distribution</div>
        <p style="font-size: 11px; margin: 0 0 8px 0;">Percentages are of students with at least one grade this term in the subjects included (<?= $gradedForDist ?> students).</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Grade Range</th>
                    <th>Count (students)</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Excellent (90-100)</td>
                    <td><?= $analytics['gradeDistribution']['excellent'] ?></td>
                    <td><?= round(($analytics['gradeDistribution']['excellent'] / $distDenom) * 100, 1) ?>%</td>
                </tr>
                <tr>
                    <td>Very Good (85-89)</td>
                    <td><?= $analytics['gradeDistribution']['very_good'] ?></td>
                    <td><?= round(($analytics['gradeDistribution']['very_good'] / $distDenom) * 100, 1) ?>%</td>
                </tr>
                <tr>
                    <td>Good (80-84)</td>
                    <td><?= $analytics['gradeDistribution']['good'] ?></td>
                    <td><?= round(($analytics['gradeDistribution']['good'] / $distDenom) * 100, 1) ?>%</td>
                </tr>
                <tr>
                    <td>Fair (75-79)</td>
                    <td><?= $analytics['gradeDistribution']['fair'] ?></td>
                    <td><?= round(($analytics['gradeDistribution']['fair'] / $distDenom) * 100, 1) ?>%</td>
                </tr>
                <tr>
                    <td>Passing (70-74)</td>
                    <td><?= $analytics['gradeDistribution']['passing'] ?></td>
                    <td><?= round(($analytics['gradeDistribution']['passing'] / $distDenom) * 100, 1) ?>%</td>
                </tr>
                <tr>
                    <td>Failing (<70)</td>
                    <td><?= $analytics['gradeDistribution']['failing'] ?></td>
                    <td><?= round(($analytics['gradeDistribution']['failing'] / $distDenom) * 100, 1) ?>%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if (!empty($analytics['subjectAverages'])): ?>
    <div class="section" style="margin-bottom: 80px;">
        <div class="section-title">Subject Performance</div>
        <table class="data-table" style="margin-bottom: 30px;">
            <thead>
                <tr>
                    <th style="width: 60%;">Subject</th>
                    <th style="width: 20%;">Average Grade</th>
                    <th style="width: 20%;">Students Graded</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($analytics['subjectAverages'] as $subject): ?>
                <tr>
                    <td style="padding: 10px 8px;"><?= esc($subject['subject']) ?></td>
                    <td style="padding: 10px 8px; text-align: center;"><?= number_format($subject['average'], 1) ?>%</td>
                    <td style="padding: 10px 8px; text-align: center;"><?= $subject['count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="section">
        <div class="section-title">Attendance Summary</div>
        <div class="summary-box">
            <p><strong>Overall Attendance Rate:</strong> <?= number_format($analytics['attendanceStats']['attendanceRate'] ?? 0, 1) ?>%</p>
            <p><strong>Total Records:</strong> <?= $analytics['attendanceStats']['total'] ?? 0 ?></p>
            <p><strong>Present:</strong> <?= $analytics['attendanceStats']['present'] ?? 0 ?> students</p>
            <p><strong>Absent:</strong> <?= $analytics['attendanceStats']['absent'] ?? 0 ?> students</p>
            <p><strong>Late:</strong> <?= $analytics['attendanceStats']['late'] ?? 0 ?> students</p>
            <p><strong>Excused:</strong> <?= $analytics['attendanceStats']['excused'] ?? 0 ?> students</p>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Present</td>
                    <td><?= $analytics['attendanceStats']['present'] ?? 0 ?></td>
                    <td><?= $analytics['attendanceStats']['total'] > 0 ? round(($analytics['attendanceStats']['present'] / $analytics['attendanceStats']['total']) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Absent</td>
                    <td><?= $analytics['attendanceStats']['absent'] ?? 0 ?></td>
                    <td><?= $analytics['attendanceStats']['total'] > 0 ? round(($analytics['attendanceStats']['absent'] / $analytics['attendanceStats']['total']) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Late</td>
                    <td><?= $analytics['attendanceStats']['late'] ?? 0 ?></td>
                    <td><?= $analytics['attendanceStats']['total'] > 0 ? round(($analytics['attendanceStats']['late'] / $analytics['attendanceStats']['total']) * 100, 1) : 0 ?>%</td>
                </tr>
                <tr>
                    <td>Excused</td>
                    <td><?= $analytics['attendanceStats']['excused'] ?? 0 ?></td>
                    <td><?= $analytics['attendanceStats']['total'] > 0 ? round(($analytics['attendanceStats']['excused'] / $analytics['attendanceStats']['total']) * 100, 1) : 0 ?>%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if (!empty($analytics['studentPerformance'])): ?>
    <div class="section">
        <div class="section-title">Top Performing Students</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Rank</th>
                    <th style="width: 35%;">Student Name</th>
                    <th style="width: 20%;">Average Grade</th>
                    <th style="width: 20%;">Performance Level</th>
                    <th style="width: 15%;">Subjects</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Sort students by average (descending)
                usort($analytics['studentPerformance'], function($a, $b) {
                    return $b['average'] <=> $a['average'];
                });
                ?>
                <?php foreach (array_slice($analytics['studentPerformance'], 0, 10) as $index => $student): ?>
                <?php
                $performanceLevel = $student['average'] >= 90 ? 'Excellent (90-100%)' : 
                                  ($student['average'] >= 85 ? 'Very Good (85-89%)' : 
                                  ($student['average'] >= 80 ? 'Good (80-84%)' : 
                                  ($student['average'] >= 75 ? 'Fair (75-79%)' : 
                                  ($student['average'] >= 70 ? 'Passing (70-74%)' : 'Below 70%'))));
                $rankStyle = $index < 3 ? 'font-weight: bold; color: #d4af37;' : '';
                ?>
                <tr>
                    <td style="text-align: center; <?= $rankStyle ?>"><?= $index + 1 ?></td>
                    <td style="<?= $rankStyle ?>"><?= esc($student['name']) ?></td>
                    <td style="text-align: center; <?= $rankStyle ?>"><?= number_format($student['average'], 1) ?>%</td>
                    <td style="<?= $rankStyle ?>"><?= $performanceLevel ?></td>
                    <td style="text-align: center; <?= $rankStyle ?>"><?= $student['grade_count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (count($analytics['studentPerformance']) > 10): ?>
        <p style="text-align: center; font-style: italic; margin-top: 10px;">Showing top 10 students. Total students with grades: <?= count($analytics['studentPerformance']) ?></p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="section">
        <div class="section-title">Student Performance</div>
        <div class="summary-box">
            <p><strong>No student performance data available.</strong> Grades have not been recorded for the current term yet.</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="section">
        <div class="section-title">Term Performance Trends</div>
        <div class="metric-grid">
            <?php foreach ($analytics['termTrends'] as $trend): ?>
            <div class="metric-row">
                <div class="metric-label"><?= esc($trend['term']) ?></div>
                <div class="metric-value"><?= number_format($trend['average'], 1) ?>%</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Recommendations</div>
        <div class="summary-box">
            <?php if (($analytics['classAverage'] ?? 0) >= 85): ?>
                <p><strong>Performance Status:</strong> Excellent! Your class is performing exceptionally well with an average of <?= number_format($analytics['classAverage'], 1) ?>%.</p>
            <?php elseif (($analytics['classAverage'] ?? 0) >= 75): ?>
                <p><strong>Performance Status:</strong> Good progress! Class average is <?= number_format($analytics['classAverage'], 1) ?>%. Consider targeted support for struggling students.</p>
            <?php else: ?>
                <p><strong>Performance Status:</strong> Needs attention. Class average is <?= number_format($analytics['classAverage'], 1) ?>%. Implement intervention strategies.</p>
            <?php endif; ?>
            
            <p><strong>Attendance Impact:</strong> High attendance rate of <?= number_format($analytics['attendanceRate'], 1) ?>% correlates with better academic performance.</p>
            
            <?php if (!empty($analytics['subjectAverages'])): ?>
                <?php
                $lowestSubject = array_reduce($analytics['subjectAverages'], function($carry, $item) {
                    return (!$carry || $item['average'] < $carry['average']) ? $item : $carry;
                });
                ?>
                <p><strong>Subject Focus:</strong> Consider additional support for <?= esc($lowestSubject['subject']) ?> (<?= number_format($lowestSubject['average'], 1) ?>% average).</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>Cauayan South Central School - Teacher Analytics Report | Generated on <?= date('F j, Y \a\t g:i A', time()) ?></p>
        <p>This report contains confidential information. Distribution is restricted to authorized personnel only.</p>
    </div>
</body>
</html>
