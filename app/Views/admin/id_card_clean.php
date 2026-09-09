<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body { 
            margin: 0; 
            padding: 20px; 
            font-family: Arial, sans-serif; 
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container { text-align: center; }
        
        .controls {
            margin-bottom: 20px;
        }
        
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            margin: 0 5px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover { background: #0056b3; }
        
        .id-card {
            width: 2.125in;
            height: 3.375in;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 3mm;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            margin: 0 auto;
        }

        .id-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 20%;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .card-header {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            color: white;
            gap: 8px;
            height: 20%;
        }

        .school-logo {
            width: 35px;
            height: 35px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 2px;
        }

        .school-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .school-name {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: bold;
            margin: 0;
            color: black;
            text-align: center;
            line-height: 1.1;
        }

        .card-body {
            padding: 15px 12px;
            color: white;
            position: relative;
            z-index: 2;
            height: 70%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .student-photo {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            border: 3px solid white;
            object-fit: cover;
            margin-bottom: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .no-photo {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            border: 3px solid white;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: rgba(255,255,255,0.7);
            margin-bottom: 10px;
        }

        .student-info {
            width: 100%;
        }

        .student-name {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 8px 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            line-height: 1.2;
        }

        .student-details {
            font-size: 11px;
            line-height: 1.3;
            opacity: 0.95;
            margin-bottom: 8px;
        }

        .student-id {
            font-size: 12px;
            font-weight: bold;
            background: rgba(255,255,255,0.2);
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.2);
            padding: 3px 12px;
            font-size: 7px;
            text-align: center;
            color: rgba(255,255,255,0.8);
            height: 10%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body { background: white; margin: 0; padding: 0; }
            .controls { display: none; }
            .id-card {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="controls">
            <button onclick="window.print()" class="btn">Print</button>
            <a href="<?= base_url('admin/id-cards') ?>" class="btn">Back</a>
        </div>

        <div class="id-card">
            <div class="card-header">
                <div class="school-logo">
                    <img src="<?= base_url('LPHS2.png') ?>" alt="LPHS Logo">
                </div>
                <div class="school-name">CAUAYAN SOUTH CENTRAL SCHOOL</div>
            </div>
            
            <div class="card-body">
                <?php if (!empty($student['photo'])): ?>
                    <img src="<?= base_url('files/' . $student['photo']) ?>" alt="Student Photo" class="student-photo">
                <?php else: ?>
                    <div class="no-photo">👤</div>
                <?php endif; ?>
                
                <div class="student-info">
                    <div class="student-name"><?= strtoupper(esc($student['first_name'] . ' ' . $student['last_name'])) ?></div>
                    <div class="student-details">
                        <?= esc(grade_level_label((int) ($student['grade_level'] ?? 0))) ?><br>
                        S.Y. <?= esc($student['school_year'] ?? get_current_school_year()) ?><br>
                        <?php if (!empty($student['date_of_birth'])): ?>
                            DOB: <?= date('M d, Y', strtotime($student['date_of_birth'])) ?>
                        <?php endif; ?>
                    </div>
                    <div class="student-id"><?= esc($student['lrn'] ?? 'LRN PENDING') ?></div>
                </div>
            </div>
            
            <div class="card-footer">
                Mabini Street, District I, Cauayan City, Isabela, Philippines
            </div>
        </div>
    </div>
</body>
</html>