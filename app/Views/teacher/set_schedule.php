<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-0">Set Class Schedule</h1>
        <?php if ($teacher): ?>
            <p class="text-muted mb-0"><?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></p>
        <?php endif; ?>
    </div>
    <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
    </a>
</div>

<?php if (empty($sections)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        You are not assigned as an adviser to any section. Please contact the administrator.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Advisory Class Schedule</h5>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <form method="post" action="<?= base_url('teacher/schedule/save') ?>" onsubmit="return prepareFormData(event)">
                <div class="mb-3">
                    <label for="section_id" class="form-label">Select Section</label>
                    <select class="form-select" id="section_id" name="section_id" required onchange="loadSchedule()">
                        <option value="">Choose a section...</option>
                        <?php foreach ($sections as $index => $section): ?>
                            <option value="<?= $section['id'] ?>" <?= $index === 0 ? 'selected' : '' ?>><?= esc($section['section_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="scheduleTable" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 120px;">Time</th>
                                    <th>Monday</th>
                                    <th>Tuesday</th>
                                    <th>Wednesday</th>
                                    <th>Thursday</th>
                                    <th>Friday</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $timeSlots = [
                                    '07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00',
                                    '12:00-13:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'
                                ];
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                                ?>
                                
                                <?php foreach ($timeSlots as $index => $timeSlot): ?>
                                    <tr>
                                        <td class="fw-bold text-center align-middle">
                                            <div class="time-slot-controls">
                                                <button type="button" class="time-edit-btn" onclick="adjustTime(<?= $index ?>, 15)" title="+15 minutes">
                                                    <i class="bi bi-chevron-up"></i>
                                                </button>
                                                <div class="time-display" data-index="<?= $index ?>" onclick="editTimeSlot(<?= $index ?>)" style="cursor: pointer;">
                                                    <span class="time-text"><?= $timeSlot ?></span>
                                                    <div class="time-inputs" style="display: none;">
                                                        <input type="time" class="form-control time-input start-time">
                                                        <span class="time-separator">to</span>
                                                        <input type="time" class="form-control time-input end-time">
                                                    </div>
                                                </div>
                                                <button type="button" class="time-edit-btn" onclick="adjustTime(<?= $index ?>, -15)" title="-15 minutes">
                                                    <i class="bi bi-chevron-down"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <?php foreach ($days as $day): ?>
                                            <td style="padding: 8px;">
                                                <div class="schedule-cell" data-day="<?= $day ?>" data-time="<?= $timeSlot ?>" data-index="<?= $index ?>">
                                                    <input type="text" class="form-control form-control-sm mb-1 subject-input" 
                                                           placeholder="Subject">
                                                    
                                                    <input type="text" class="form-control form-control-sm mb-1 teacher-input" 
                                                           placeholder="Teacher">
                                                    
                                                    <input type="text" class="form-control form-control-sm room-input" 
                                                           placeholder="Room">
                                                </div>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Save Schedule
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<style>
.time-slot-controls {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.time-edit-btn {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 10px;
    padding: 1px 3px;
    cursor: pointer;
    border-radius: 3px;
    min-width: 20px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.time-edit-btn:hover {
    background: #f8f9fa;
    color: #495057;
}

.time-input {
    width: 85px !important;
    font-size: 10px;
    text-align: center;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 2px 4px;
    background: #fff;
    transition: all 0.3s ease;
    font-weight: normal;
    height: 28px;
}

.time-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    outline: none;
    background: #f8f9fa;
}

.time-inputs {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    padding: 8px 12px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.time-separator {
    font-weight: 600;
    color: #6c757d;
    font-size: 12px;
}

.time-display {
    position: relative;
}

.time-display:hover {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 4px 8px;
    transition: all 0.2s ease;
}

.time-text {
    font-weight: 600;
    color: #495057;
    user-select: none;
    font-size: 13px;
}
</style>

<script>
const schedules = <?= json_encode($schedules) ?>;
let timeSlots = [
    '07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00',
    '12:00-13:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'
];

// Auto-load first section on page load
document.addEventListener('DOMContentLoaded', function() {
    // Restore custom time slots from localStorage
    const savedTimeSlots = localStorage.getItem('customTimeSlots');
    if (savedTimeSlots) {
        timeSlots = JSON.parse(savedTimeSlots);
        // Update display
        document.querySelectorAll('.time-text').forEach((timeText, index) => {
            if (timeSlots[index]) {
                timeText.textContent = timeSlots[index];
                // Update data attributes
                const row = timeText.closest('tr');
                const cells = row.querySelectorAll('.schedule-cell');
                cells.forEach(cell => {
                    cell.dataset.time = timeSlots[index];
                });
            }
        });
    }
    
    const sectionSelect = document.getElementById('section_id');
    if (sectionSelect.value) {
        loadSchedule();
    }
});

function loadSchedule() {
    const sectionId = document.getElementById('section_id').value;
    const scheduleTable = document.getElementById('scheduleTable');
    
    if (!sectionId) {
        scheduleTable.style.display = 'none';
        return;
    }
    
    scheduleTable.style.display = 'block';
    
    // Clear all inputs first
    document.querySelectorAll('.schedule-cell input').forEach(input => {
        input.value = '';
    });
    
    // Load existing schedule if available
    if (schedules[sectionId]) {
        const sectionSchedules = schedules[sectionId];
        
        Object.keys(sectionSchedules).forEach(day => {
            Object.keys(sectionSchedules[day]).forEach(timeSlot => {
                const schedule = sectionSchedules[day][timeSlot];
                const cell = document.querySelector(`[data-day="${day}"][data-time="${timeSlot}"]`);
                
                if (cell) {
                    cell.querySelector('.subject-input').value = schedule.subject_name || '';
                    cell.querySelector('.teacher-input').value = schedule.teacher_name || '';
                    cell.querySelector('.room-input').value = schedule.room || '';
                }
            });
        });
    }
}

function adjustTime(index, amount) {
    const timeDisplay = document.querySelectorAll('.time-display')[index];
    const timeText = timeDisplay.querySelector('.time-text');
    const currentTime = timeText.textContent;
    const [startTime, endTime] = currentTime.split('-');
    
    const newStart = addMinutes(startTime, amount);
    const newEnd = addMinutes(endTime, amount);
    
    const newTimeSlot = newStart + '-' + newEnd;
    timeSlots[index] = newTimeSlot;
    timeText.textContent = newTimeSlot;
    
    // Update data attributes for schedule cells
    const row = timeDisplay.closest('tr');
    const cells = row.querySelectorAll('.schedule-cell');
    cells.forEach(cell => {
        cell.dataset.time = newTimeSlot;
    });
    
    // Save custom times to localStorage
    localStorage.setItem('customTimeSlots', JSON.stringify(timeSlots));
}

function addMinutes(time, minutes) {
    const [hours, mins] = time.split(':').map(Number);
    let totalMinutes = hours * 60 + mins + minutes;
    
    if (totalMinutes < 0) totalMinutes = 0;
    if (totalMinutes >= 24 * 60) totalMinutes = 23 * 60 + 59;
    
    const newHours = Math.floor(totalMinutes / 60);
    const newMins = totalMinutes % 60;
    
    return String(newHours).padStart(2, '0') + ':' + String(newMins).padStart(2, '0');
}

function timeToMinutes(time) {
    const [hours, mins] = time.split(':').map(Number);
    return hours * 60 + mins;
}



function editTimeSlot(index) {
    // Close any other open editors first
    document.querySelectorAll('.time-inputs').forEach(inputs => {
        if (inputs.style.display === 'flex') {
            const text = inputs.previousElementSibling;
            if (text) text.style.display = 'block';
            inputs.style.display = 'none';
        }
    });
    
    const timeDisplay = document.querySelectorAll('.time-display')[index];
    const timeText = timeDisplay.querySelector('.time-text');
    const timeInputs = timeDisplay.querySelector('.time-inputs');
    const startInput = timeInputs.querySelector('.start-time');
    const endInput = timeInputs.querySelector('.end-time');
    
    // Initialize inputs with current time slot values
    const currentTimeSlot = timeSlots[index];
    const [currentStart, currentEnd] = currentTimeSlot.split('-');
    startInput.value = currentStart;
    endInput.value = currentEnd;
    
    timeText.style.display = 'none';
    timeInputs.style.display = 'flex';
    
    const saveTime = () => {
        if (!startInput.value || !endInput.value) {
            closeEditor();
            return;
        }
        
        const startMinutes = timeToMinutes(startInput.value);
        const endMinutes = timeToMinutes(endInput.value);
        
        if (startMinutes >= endMinutes) {
            alert('End time must be after start time');
            startInput.focus();
            return;
        }
        
        const newTimeSlot = startInput.value + '-' + endInput.value;
        timeSlots[index] = newTimeSlot;
        timeText.textContent = newTimeSlot;
        closeEditor();
        
        const row = timeDisplay.closest('tr');
        const cells = row.querySelectorAll('.schedule-cell');
        cells.forEach(cell => {
            cell.dataset.time = newTimeSlot;
        });
        
        localStorage.setItem('customTimeSlots', JSON.stringify(timeSlots));
    };
    
    const closeEditor = () => {
        timeText.style.display = 'block';
        timeInputs.style.display = 'none';
    };
    
    let isEditing = true;
    let clickHandler = null;
    
    // Click outside to save
    setTimeout(() => {
        clickHandler = function(e) {
            if (!timeInputs.contains(e.target) && !timeText.contains(e.target) && isEditing) {
                isEditing = false;
                saveTime();
                document.removeEventListener('click', clickHandler);
            }
        };
        document.addEventListener('click', clickHandler);
    }, 200);
    
    startInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            endInput.focus();
        } else if (e.key === 'Escape') {
            isEditing = false;
            closeEditor();
        }
    });
    
    endInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            isEditing = false;
            saveTime();
        } else if (e.key === 'Escape') {
            isEditing = false;
            closeEditor();
        }
    });
    
    startInput.focus();
}

function prepareFormData(event) {
    const form = event.target;
    const cells = document.querySelectorAll('.schedule-cell');
    
    form.querySelectorAll('input[type="hidden"]').forEach(input => input.remove());
    
    cells.forEach(cell => {
        const day = cell.dataset.day;
        const index = cell.dataset.index;
        const currentTime = timeSlots[index];
        
        const subject = cell.querySelector('.subject-input').value;
        const teacher = cell.querySelector('.teacher-input').value;
        const room = cell.querySelector('.room-input').value;
        
        if (subject || teacher || room) {
            const subjectInput = document.createElement('input');
            subjectInput.type = 'hidden';
            subjectInput.name = `schedule[${day}][${currentTime}][subject]`;
            subjectInput.value = subject;
            form.appendChild(subjectInput);
            
            const teacherInput = document.createElement('input');
            teacherInput.type = 'hidden';
            teacherInput.name = `schedule[${day}][${currentTime}][teacher]`;
            teacherInput.value = teacher;
            form.appendChild(teacherInput);
            
            const roomInput = document.createElement('input');
            roomInput.type = 'hidden';
            roomInput.name = `schedule[${day}][${currentTime}][room]`;
            roomInput.value = room;
            form.appendChild(roomInput);
        }
    });
    
    return true;
}
</script>

<?= $this->endSection() ?>