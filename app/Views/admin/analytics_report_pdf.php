<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LPHS Teacher Analytics Report</title>
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
        
        .content {
            margin: 20px 0;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 10px;
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
        <div class="report-info">Report Generated: <?= $reportDate ?></div>
    </div>
    
    <div class="content">
        <?= $announcement['body'] ?>
    </div>
    
    <div class="footer">
        <p>Cauayan South Central School - Teacher Analytics Report | Generated on <?= date('F j, Y \a\t g:i A') ?></p>
        <p>This report contains confidential information. Distribution is restricted to authorized personnel only.</p>
    </div>
</body>
</html>
