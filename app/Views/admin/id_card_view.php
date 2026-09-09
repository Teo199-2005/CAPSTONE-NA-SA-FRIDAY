<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .id-card { page-break-after: avoid; }
            * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }
        }

        .container {
            text-align: center;
            max-width: 600px;
        }

        .controls {
            margin-bottom: 30px;
        }

        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #545b62; }

        .id-card {
            width: 4.5in;
            height: 3in;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            margin: 0 auto;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        .id-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .card-header {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 20px;
            color: white;
            gap: 12px;
        }

        .school-logo {
            width: 55px;
            height: 55px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 3px;
        }

        .school-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .school-name {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            color: black;
            text-align: center;
        }

        .card-body {
            padding: 40px 20px 20px 20px;
            color: white;
            position: relative;
            z-index: 2;
        }

        .student-photo {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            border: 4px solid white;
            object-fit: cover;
            float: left;
            margin-right: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .no-photo {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            border: 4px solid white;
            background: rgba(255,255,255,0.2);
            float: left;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: rgba(255,255,255,0.7);
        }

        .student-info {
            overflow: hidden;
        }

        .student-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 6px 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .student-details {
            font-size: 13px;
            line-height: 1.4;
            opacity: 0.95;
        }

        .student-id {
            font-size: 14px;
            font-weight: bold;
            background: rgba(255,255,255,0.2);
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 6px;
        }

        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.2);
            padding: 6px 20px;
            font-size: 10px;
            text-align: center;
            color: rgba(255,255,255,0.8);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="controls no-print">
            <?php if (empty($student['lrn'])): ?>
                <button onclick="generateLRN(<?= $student['id'] ?>)" class="btn btn-success">
                    Generate LRN
                </button>
            <?php endif; ?>
            <button onclick="window.print()" class="btn">
                Print ID Card
            </button>
            <a href="<?= base_url('admin/id-cards') ?>" class="btn btn-secondary">
                Back to List
            </a>
        </div>

        <div class="id-card">
            <div class="card-header">
                <div class="school-logo">
                    <img src="<?= base_url('LPHS2.png') ?>" alt="CSCS Logo">
                </div>
                <div class="school-name">CAUAYAN SOUTH CENTRAL SCHOOL</div>
            </div>
            
            <div class="card-body">
                <?php if (!empty($student['photo'])): ?>
                    <img src="<?= base_url('files/' . $student['photo']) ?>" alt="Student Photo" class="student-photo">
                <?php elseif (!empty($student['photo_path'])): ?>
                    <img src="<?= base_url($student['photo_path']) ?>" alt="Student Photo" class="student-photo">
                <?php else: ?>
                    <div class="no-photo">👤</div>
                <?php endif; ?>
                
                <div class="student-info">
                    <div class="student-name"><?= strtoupper(esc($student['first_name'] . ' ' . $student['last_name'])) ?></div>
                    <div class="student-details">
                        <?= esc(grade_level_label((int) ($student['grade_level'] ?? 0))) ?><?= $student['section_name'] ? ' - ' . esc($student['section_name']) : '' ?><br>
                        S.Y. <?= esc($student['school_year'] ?? get_current_school_year()) ?><br>
                        <?php if (!empty($student['date_of_birth'])): ?>
                            DOB: <?= date('M j, Y', strtotime($student['date_of_birth'])) ?>
                        <?php endif; ?>
                    </div>
                    <div class="student-id"><?= esc($student['lrn'] ?? 'LRN PENDING') ?></div>
                </div>
            </div>
            
            <div class="card-footer">
                Panglao, Bohol • Valid for Current School Year Only
            </div>
        </div>
    </div>

    <script>
    function generateLRN(studentId) {
        if (!confirm('Generate an LRN for this student?')) return;
        
        fetch('<?= base_url('admin/id-cards/generate-lrn') ?>/' + studentId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to generate LRN'));
            }
        })
        .catch(error => {
            alert('Error generating LRN');
        });
    }
    </script>
</body>
</html>