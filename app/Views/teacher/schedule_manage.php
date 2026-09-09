<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
.form-select-sm, .form-control-sm {
    font-size: 16px;
    padding: 8px 12px;
}

.schedule-cell {
    min-height: 80px;
    padding: 8px;
}

.card-header {
    transition: all 0.3s ease;
}

.card-header:hover {
    opacity: 0.9;
}

.bi-chevron-down, .bi-chevron-up {
    transition: transform 0.3s ease;
}

.time-editable {
    display: flex;
    justify-content: center;
    align-items: center;
}

.time-editable .time-text:hover {
    background: #e7f3ff;
    color: #0056b3;
}

.time-inputs {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    min-width: 200px;
}

.time-display {
    transition: all 0.2s ease;
    padding: 4px 8px;
    border-radius: 4px;
}

.time-display:hover {
    background: #e3f2fd;
    cursor: pointer;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0">Set Class Schedule</h1>
        <?php if ($teacher): ?>
            <p class="text-muted mb-0"><?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></p>
        <?php endif; ?>
    </div>
    <div>
        <a href="<?= base_url('teacher/schedule') ?>" class="btn btn-outline-secondary me-2">
            <i class="bi bi-eye me-2"></i>View Schedule
        </a>
        <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</div>

<?php if ($error = session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= esc($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($success = session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($sections)): ?>
<div class="alert alert-info">
    <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Your Assigned Sections & Subjects</h6>
    <p class="mb-0">You can only schedule classes for the following section-subject combinations assigned by the admin:</p>
    <ul class="mb-0 mt-2">
        <?php foreach ($sections as $section): ?>
            <li>
                <strong><?= esc($section['section_name']) ?> (<?= esc(grade_level_label((int) $section['grade_level'])) ?>)</strong>:
                <?php 
                if (isset($assignedCombinations[$section['id']])) {
                    $sectionSubjects = array_filter($subjects, function($subj) use ($assignedCombinations, $section) {
                        return in_array($subj['id'], $assignedCombinations[$section['id']]);
                    });
                    echo implode(', ', array_map(function($s) { return esc($s['subject_name']); }, $sectionSubjects));
                }
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> No Assignments Yet</h6>
    <p class="mb-0">The admin has not assigned any section-subject combinations to you yet. Please contact the admin to get your teaching assignments.</p>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Weekly Schedule</h5>
    </div>
    <div class="card-body">
        <?php 
        // Get all unique time slots from existing schedules
        $existingTimeSlots = [];
        foreach ($schedules as $daySchedules) {
            foreach ($daySchedules as $timeKey => $schedule) {
                $existingTimeSlots[$timeKey] = true;
            }
        }
        
        // Default time slots
        $defaultTimeSlots = [
            '07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00',
            '11:00-12:00', '12:00-13:00', '13:00-14:00', '14:00-15:00',
            '15:00-16:00', '16:00-17:00'
        ];
        
        // Merge and sort time slots
        $allTimeSlots = array_unique(array_merge($defaultTimeSlots, array_keys($existingTimeSlots)));
        usort($allTimeSlots, function($a, $b) {
            return strcmp($a, $b);
        });
        $timeSlots = $allTimeSlots;
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $dayColors = [
            'Monday' => '#F4D03F',
            'Tuesday' => '#34495E',
            'Wednesday' => '#F4D03F',
            'Thursday' => '#34495E',
            'Friday' => '#F4D03F'
        ];
        $dayTextColors = [
            'Monday' => '#1a1a1a',
            'Tuesday' => '#ffffff',
            'Wednesday' => '#1a1a1a',
            'Thursday' => '#ffffff',
            'Friday' => '#1a1a1a'
        ];
        ?>
        
        <form id="scheduleForm" method="post" action="<?= base_url('teacher/schedule/save') ?>">
            <?= csrf_field() ?>
            
            <?php foreach ($days as $day): ?>
            <div class="card mb-3" style="border-left: 4px solid <?= $dayColors[$day] ?>;">
                <div class="card-header" style="cursor: pointer; background: <?= $dayColors[$day] ?>;" onclick="toggleDay('<?= $day ?>')">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: <?= $dayTextColors[$day] ?>; font-weight: bold;">
                            <i class="bi bi-calendar-day me-2"></i><?= $day ?>
                        </h5>
                        <i class="bi bi-chevron-down" style="color: <?= $dayTextColors[$day] ?>;" id="icon-<?= $day ?>"></i>
                    </div>
                </div>
                <div class="card-body" id="day-<?= $day ?>" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="20%" style="font-size: 16px;">Time</th>
                                    <th width="80%" style="font-size: 16px;">Schedule</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($timeSlots as $timeSlot): ?>
                                <tr data-day="<?= $day ?>" data-time-index="<?= array_search($timeSlot, $timeSlots) ?>">
                                    <td class="fw-bold text-center" style="font-size: 16px;">
                                        <div class="time-editable" data-day="<?= $day ?>" data-index="<?= array_search($timeSlot, $timeSlots) ?>" onclick="editTime(this)">
                                            <span class="time-text" style="cursor: pointer; padding: 8px; border-radius: 4px; display: inline-block;"><?= $timeSlot ?></span>
                                            <div class="time-inputs" style="display: none; gap: 8px; align-items: center;">
                                                <input type="time" class="form-control start-time" value="<?= explode('-', $timeSlot)[0] ?>" style="width: 140px; min-width: 140px;">
                                                <span style="font-weight: bold;">-</span>
                                                <input type="time" class="form-control end-time" value="<?= explode('-', $timeSlot)[1] ?>" style="width: 140px; min-width: 140px;">
                                                <button type="button" class="btn btn-sm btn-success save-time-btn" style="margin-left: 8px;">
                                                    <i class="bi bi-check"></i> Save
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $currentSchedule = $schedules[strtolower($day)][$timeSlot] ?? null;
                                        $startTime = explode('-', $timeSlot)[0];
                                        $endTime = explode('-', $timeSlot)[1];
                                        ?>
                                        <div class="schedule-cell" data-day="<?= $day ?>" data-start="<?= $startTime ?>" data-end="<?= $endTime ?>">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label" style="font-size: 16px; font-weight: 600;">Section</label>
                                                    <select class="form-select form-select-sm section-select" 
                                                            name="schedule[<?= strtolower($day) ?>][<?= $timeSlot ?>][section_id]" 
                                                            onchange="updateSubjectOptions(this, '<?= $day ?>', '<?= $timeSlot ?>')">
                                                        <option value="">Select Section</option>
                                                        <?php foreach ($sections as $section): ?>
                                                            <option value="<?= $section['id'] ?>" 
                                                                    <?= $currentSchedule && $currentSchedule['section_id'] == $section['id'] ? 'selected' : '' ?>>
                                                                <?= esc($section['section_name']) ?> (<?= esc(grade_level_label((int) $section['grade_level'])) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" style="font-size: 16px; font-weight: 600;">Subject</label>
                                                    <select class="form-select form-select-sm subject-select" 
                                                            name="schedule[<?= strtolower($day) ?>][<?= $timeSlot ?>][subject_id]" 
                                                            data-day="<?= $day ?>" data-time="<?= $timeSlot ?>" disabled>
                                                        <option value="">Select Section First</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" style="font-size: 16px; font-weight: 600;">Room</label>
                                                    <input type="text" class="form-control form-control-sm room-input" 
                                                           name="schedule[<?= strtolower($day) ?>][<?= $timeSlot ?>][room]" 
                                                           placeholder="Room" value="<?= esc($currentSchedule['room'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle me-2"></i>Save Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const assignedCombinations = <?= json_encode($assignedCombinations ?? []) ?>;
const allSubjects = <?= json_encode($subjects ?? []) ?>;
const existingSchedules = <?= json_encode($schedules ?? []) ?>;
const defaultTimeSlots = <?= json_encode($timeSlots) ?>;
let timeSlotsPerDay = {
    'Monday': [...defaultTimeSlots],
    'Tuesday': [...defaultTimeSlots],
    'Wednesday': [...defaultTimeSlots],
    'Thursday': [...defaultTimeSlots],
    'Friday': [...defaultTimeSlots]
};

function editTime(element) {
    const timeText = element.querySelector('.time-text');
    const timeInputs = element.querySelector('.time-inputs');
    const startInput = timeInputs.querySelector('.start-time');
    const endInput = timeInputs.querySelector('.end-time');
    const day = element.dataset.day;
    const index = parseInt(element.dataset.index);
    const row = element.closest('tr');
    const cell = row.querySelector('.schedule-cell');
    
    // Initialize inputs with current cell data, not PHP template values
    startInput.value = cell.dataset.start;
    endInput.value = cell.dataset.end;
    
    timeText.style.display = 'none';
    timeInputs.style.display = 'flex';
    
    let isEditing = true;
    let isSaving = false;
    
    const saveTime = () => {
        if (!isEditing || isSaving) return;
        
        isSaving = true;
        
        let newStart = startInput.value;
        let newEnd = endInput.value;
        
        if (!newStart || !newEnd) {
            alert('Please enter both start and end times');
            isSaving = false;
            return;
        }
        
        // Auto-adjust if end time is before or equal to start time
        if (newEnd <= newStart) {
            const [hours, minutes] = newStart.split(':');
            const startDate = new Date(2000, 0, 1, parseInt(hours), parseInt(minutes));
            startDate.setHours(startDate.getHours() + 1);
            newEnd = startDate.toTimeString().substring(0, 5);
            endInput.value = newEnd;
        }
        
        isEditing = false;
        
        if (newStart && newEnd) {
            const newTimeSlot = newStart + '-' + newEnd;
            const dayRows = document.querySelectorAll(`tr[data-day="${day}"]`);
            const oldStart = cell.dataset.start;
            const oldEnd = cell.dataset.end;
            
            // Calculate slot duration
            const newStartDate = new Date('1970-01-01 ' + newStart);
            const newEndDate = new Date('1970-01-01 ' + newEnd);
            const slotDuration = (newEndDate - newStartDate) / 60000;
            
            // Update current slot
            timeSlotsPerDay[day][index] = newTimeSlot;
            timeText.textContent = newTimeSlot;
            cell.dataset.start = newStart;
            cell.dataset.end = newEnd;
            
            // Update form field names to use new time slot
            const dayLower = day.toLowerCase();
            const sectionSelect = cell.querySelector('.section-select');
            const subjectSelect = cell.querySelector('.subject-select');
            const roomInput = cell.querySelector('.room-input');
            
            if (sectionSelect) sectionSelect.name = `schedule[${dayLower}][${newTimeSlot}][section_id]`;
            if (subjectSelect) subjectSelect.name = `schedule[${dayLower}][${newTimeSlot}][subject_id]`;
            if (roomInput) roomInput.name = `schedule[${dayLower}][${newTimeSlot}][room]`;
            
            // Auto-adjust all slots to be continuous
            let currentTime = new Date('1970-01-01 ' + newStart);
            
            // Adjust slots above (backwards)
            for (let i = parseInt(index) - 1; i >= 0; i--) {
                const row = dayRows[i];
                const otherTimeEditable = row.querySelector('.time-editable');
                const otherTimeText = otherTimeEditable.querySelector('.time-text');
                const otherCell = row.querySelector('.schedule-cell');
                
                const endTime = new Date(currentTime);
                currentTime.setMinutes(currentTime.getMinutes() - slotDuration);
                
                const otherNewStart = currentTime.toTimeString().substring(0, 5);
                const otherNewEnd = endTime.toTimeString().substring(0, 5);
                
                otherCell.dataset.start = otherNewStart;
                otherCell.dataset.end = otherNewEnd;
                otherTimeText.textContent = otherNewStart + '-' + otherNewEnd;
                timeSlotsPerDay[day][i] = otherNewStart + '-' + otherNewEnd;
            }
            
            // Reset for slots below
            currentTime = new Date('1970-01-01 ' + newEnd);
            
            // Adjust slots below (forwards)
            for (let i = parseInt(index) + 1; i < dayRows.length; i++) {
                const row = dayRows[i];
                const otherTimeEditable = row.querySelector('.time-editable');
                const otherTimeText = otherTimeEditable.querySelector('.time-text');
                const otherCell = row.querySelector('.schedule-cell');
                
                const startTime = new Date(currentTime);
                currentTime.setMinutes(currentTime.getMinutes() + slotDuration);
                
                const otherNewStart = startTime.toTimeString().substring(0, 5);
                const otherNewEnd = currentTime.toTimeString().substring(0, 5);
                
                otherCell.dataset.start = otherNewStart;
                otherCell.dataset.end = otherNewEnd;
                otherTimeText.textContent = otherNewStart + '-' + otherNewEnd;
                timeSlotsPerDay[day][i] = otherNewStart + '-' + otherNewEnd;
            }
        }
        
        timeText.style.display = 'inline-block';
        timeInputs.style.display = 'none';
    };
    
    const cancel = () => {
        isEditing = false;
        timeText.style.display = 'inline-block';
        timeInputs.style.display = 'none';
    };
    
    timeInputs.addEventListener('click', (e) => e.stopPropagation());
    
    setTimeout(() => {
        document.addEventListener('click', function closeOnClickOutside(e) {
            if (!timeInputs.contains(e.target) && !timeText.contains(e.target)) {
                if (isEditing) {
                    saveTime();
                }
                document.removeEventListener('click', closeOnClickOutside);
            }
        });
    }, 150);
    
    startInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            endInput.focus();
        } else if (e.key === 'Escape') {
            cancel();
        }
    });
    
    endInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveTime();
        } else if (e.key === 'Escape') {
            cancel();
        }
    });
    
    const saveBtn = timeInputs.querySelector('.save-time-btn');
    if (saveBtn) {
        saveBtn.onclick = (e) => {
            e.stopPropagation();
            e.preventDefault();
            saveTime();
        };
    }
    
    setTimeout(() => startInput.focus(), 50);
}

function toggleDay(day) {
    const allDays = document.querySelectorAll('[id^="day-"]');
    const currentContent = document.getElementById('day-' + day);
    const currentIcon = document.getElementById('icon-' + day);
    
    allDays.forEach(el => {
        if (el !== currentContent) {
            el.style.display = 'none';
        }
    });
    
    document.querySelectorAll('[id^="icon-"]').forEach(icon => {
        if (icon !== currentIcon) {
            icon.className = 'bi bi-chevron-down';
        }
    });
    
    if (currentContent.style.display === 'none' || currentContent.style.display === '') {
        currentContent.style.display = 'block';
        currentIcon.className = 'bi bi-chevron-up';
    } else {
        currentContent.style.display = 'none';
        currentIcon.className = 'bi bi-chevron-down';
    }
}

function updateSubjectOptions(sectionSelect, day, timeSlot) {
    const sectionId = sectionSelect.value;
    const cell = sectionSelect.closest('.schedule-cell');
    const subjectSelect = cell.querySelector('.subject-select');
    
    if (!sectionId || !assignedCombinations[sectionId]) {
        subjectSelect.innerHTML = '<option value="">Select Section First</option>';
        subjectSelect.disabled = true;
        return;
    }
    
    const currentSubjectId = subjectSelect.value;
    subjectSelect.innerHTML = '<option value="">Select Subject</option>';
    
    const assignedSubjectIds = assignedCombinations[sectionId];
    allSubjects.forEach(subject => {
        if (assignedSubjectIds.includes(subject.id)) {
            const option = document.createElement('option');
            option.value = subject.id;
            option.textContent = subject.subject_name;
            if (currentSubjectId == subject.id) {
                option.selected = true;
            }
            subjectSelect.appendChild(option);
        }
    });
    
    subjectSelect.disabled = false;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.schedule-cell').forEach(cell => {
        const day = cell.dataset.day;
        const startTime = cell.dataset.start;
        const endTime = cell.dataset.end;
        const timeSlot = startTime + '-' + endTime;
        
        const dayKey = day.toLowerCase();
        const existingSchedule = existingSchedules[dayKey] && existingSchedules[dayKey][timeSlot];
        
        if (existingSchedule) {
            const sectionSelect = cell.querySelector('.section-select');
            const subjectSelect = cell.querySelector('.subject-select');
            
            if (sectionSelect && existingSchedule.section_id) {
                sectionSelect.value = existingSchedule.section_id;
                updateSubjectOptions(sectionSelect, day, timeSlot);
                
                setTimeout(() => {
                    if (subjectSelect && existingSchedule.subject_id) {
                        subjectSelect.value = existingSchedule.subject_id;
                    }
                }, 50);
            }
        }
    });
});
</script>

<?= $this->endSection() ?>