<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
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

.time-input {
    width: 85px;
    font-size: 11px;
    text-align: center;
    border: 2px solid #007bff;
    border-radius: 6px;
    padding: 4px 6px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.1);
    transition: all 0.2s ease;
}

.time-input:focus {
    outline: none;
    border-color: #0056b3;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    transform: scale(1.05);
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

.room-input-existing {
    border: 1px solid #ced4da;
    transition: all 0.2s ease;
}

.room-input-existing:hover {
    border-color: #007bff;
    box-shadow: 0 0 0 0.1rem rgba(0, 123, 255, 0.25);
}

.room-input-existing:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>

<div class="container-fluid">
    <h2 class="mb-4">Manage Schedule</h2>
    <h4 class="mb-2"><?= esc($section['section_name']) ?> - Grade <?= $section['grade_level'] ?></h4>
    <p class="text-muted mb-4">Section Adviser: <?= !empty($section['adviser_name']) ? esc($section['adviser_name']) : 'No adviser assigned' ?></p>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Weekly Schedule</h5>
        <button class="btn btn-success" onclick="saveAllSchedules()">
            <i class="bi bi-check-circle me-2"></i>Save Schedules
        </button>
    </div>
    
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $index => $day): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" id="<?= $day ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= $day ?>" type="button" role="tab" style="font-size: 1.5rem;">
                    <i class="bi bi-calendar-day me-1"></i> <?= $day ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <div class="tab-content mt-3">
        <?php foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $index => $day): ?>
            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="<?= $day ?>" role="tabpanel">
                <div id="schedule-<?= $day ?>">
                    <div class="text-center py-4">
                        <div class="spinner-border"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const sectionId = <?= $section['id'] ?>;
const sectionName = '<?= esc($section['section_name']) ?>';
const gradeLevel = <?= $section['grade_level'] ?>;

document.addEventListener('DOMContentLoaded', () => {
    ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'].forEach(day => {
        loadSchedules(day);
    });
});

function loadSchedules(day) {
    fetch(`<?= base_url('admin/schedules/get/') ?>${sectionId}/${day}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                displaySchedules(day, data.schedules);
            }
        });
}

function displaySchedules(day, schedules) {
    const container = document.getElementById(`schedule-${day}`);
    
    const existingTimeSlots = new Set();
    schedules.forEach(s => {
        const timeKey = s.start_time.substring(0,5) + '-' + s.end_time.substring(0,5);
        existingTimeSlots.add(timeKey);
    });
    
    const timeSlots = Array.from(existingTimeSlots).sort().map(timeKey => {
        const [start, end] = timeKey.split('-');
        return {start, end};
    });
    
    let html = '<table class="table table-bordered"><thead><tr><th width="15%">Time</th><th>Schedule</th></tr></thead><tbody>';
    
    timeSlots.forEach((slot, index) => {
        const slotSchedules = schedules.filter(s => 
            s.start_time.substring(0,5) === slot.start && s.end_time.substring(0,5) === slot.end
        );
        
        html += `<tr data-day="${day}" data-time-index="${index}"><td class="fw-bold text-center">
            <div class="time-editable" data-day="${day}" data-index="${index}" onclick="editTime(this)">
                <span class="time-text" style="cursor: pointer; padding: 8px; border-radius: 4px; display: inline-block;">${slot.start}-${slot.end}</span>
                <div class="time-inputs" style="display: none; gap: 8px; align-items: center;">
                    <input type="time" class="form-control start-time" value="${slot.start}" style="width: 140px; min-width: 140px;">
                    <span style="font-weight: bold;">-</span>
                    <input type="time" class="form-control end-time" value="${slot.end}" style="width: 140px; min-width: 140px;">
                    <button type="button" class="btn btn-sm btn-success save-time-btn" style="margin-left: 8px;">
                        <i class="bi bi-check"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger cancel-time-btn" style="margin-left: 4px;" onclick="this.closest('.time-editable').querySelector('.time-text').style.display='inline-block'; this.closest('.time-inputs').style.display='none';">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        </td><td>`;
        
        if (slotSchedules.length > 0) {
            slotSchedules.forEach(s => {
                html += `<div class="p-3 mb-2 bg-light border rounded">
                    <div class="row">
                        <div class="col-md-1 d-flex align-items-center"><input type="checkbox" class="form-check-input schedule-checkbox" value="${s.id}" style="width: 20px; height: 20px; accent-color: #dc3545;"></div>
                        <div class="col-md-3"><label class="form-label">Subject</label><select class="form-control subject-select-existing" data-schedule-id="${s.id}" data-current="${s.subject_id}" onchange="updateTeacherForSubject(this)"><option value="${s.subject_id}">${s.subject_name}</option></select></div>
                        <div class="col-md-3"><label class="form-label">Teacher</label><input type="text" class="form-control teacher-display-existing" value="${s.teacher_name || 'Not Assigned'}" readonly data-teacher-id="${s.teacher_id}"></div>
                        <div class="col-md-4"><label class="form-label">Room <small class="text-muted">(editable)</small></label><input type="text" class="form-control room-input-existing" value="${s.room || ''}" data-schedule-id="${s.id}" placeholder="Enter room"></div>
                    </div>
                </div>`;
            });
        } else {
            html += `<div class="p-3 bg-white border rounded new-schedule" data-day="${day}" data-start="${slot.start}" data-end="${slot.end}">
                <div class="row">
                    <div class="col-md-3"><label class="form-label">Subject</label><select class="form-select subject-select"><option value="">Select Subject</option></select></div>
                    <div class="col-md-3"><label class="form-label">Teacher</label><input type="text" class="form-control teacher-display" readonly placeholder="Select Subject First"><select class="form-select teacher-select d-none"></select></div>
                    <div class="col-md-3"><label class="form-label">Room</label><input type="text" class="form-control room-input" placeholder="Room" style="pointer-events: auto;"></div>
                    <div class="col-md-3 d-flex align-items-end"><button class="btn btn-danger w-100" onclick="removeEmptyScheduleRow(this)"><i class="bi bi-trash me-2"></i>Remove</button></div>
                </div>
            </div>`;
        }
        
        html += '</td></tr>';
    });
    
    html += '</tbody></table>';
    html += `<div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary" onclick="addNewTimeSlot('${day}')"><i class="bi bi-plus-circle me-2"></i>Add Time Slot</button>
        <button class="btn btn-danger" onclick="deleteSelectedSchedules('${day}')"><i class="bi bi-trash me-2"></i>Remove Selected</button>
    </div>`;
    container.innerHTML = html;
    loadSubjectsAndTeachers(day);
    
    // Mark existing room inputs as modified for batch save
    setTimeout(() => {
        container.querySelectorAll('.room-input-existing').forEach(input => {
            input.addEventListener('input', function() {
                this.dataset.modified = 'true';
            });
        });
    }, 100);
    
    // Load subjects and teachers for existing schedules after data is loaded
    if (window.subjectTeachers) {
        loadSubjectsForExisting(container);
        addExistingEventListeners(container);
    } else {
        // Wait for subject teachers data to load
        setTimeout(() => {
            if (window.subjectTeachers) {
                loadSubjectsForExisting(container);
                addExistingEventListeners(container);
            }
        }, 500);
    }
}

function loadSubjectsForSelect(selectEl, day) {
    if (window.subjectTeachers) {
        Object.keys(window.subjectTeachers).forEach(subjectId => {
            const subject = window.subjectTeachers[subjectId];
            selectEl.innerHTML += `<option value="${subjectId}">${subject.subject_name}</option>`;
        });
        
        selectEl.addEventListener('change', function() {
            const row = this.closest('.row');
            const teacherSelect = row.querySelector('.teacher-select');
            const teacherDisplay = row.querySelector('.teacher-display');
            const subjectId = this.value;
            
            if (subjectId && window.subjectTeachers[subjectId]) {
                const teachers = window.subjectTeachers[subjectId].teachers;
                teacherSelect.innerHTML = '';
                teachers.forEach(t => {
                    teacherSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                });
                
                if (teachers.length > 0) {
                    teacherSelect.value = teachers[0].id;
                    teacherDisplay.value = teachers[0].name;
                }
            } else {
                teacherDisplay.value = '';
                teacherSelect.innerHTML = '';
            }
        });
    }
}

function addExistingEventListeners(container) {
    container.querySelectorAll('.subject-select-existing, .teacher-select-existing').forEach(select => {
        select.addEventListener('change', function() {
            updateExistingSchedule(this);
        });
    });
}

function loadSubjectsAndTeachers(day) {
    fetch(`<?= base_url('admin/schedules/subject-teachers/') ?>${sectionId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.subjectTeachers = data.subject_teachers;
                document.querySelectorAll(`#schedule-${day} .subject-select`).forEach(sel => {
                    loadSubjectsForSelect(sel, day);
                });
                
                // Load existing schedules dropdowns after data is available
                const container = document.getElementById(`schedule-${day}`);
                loadSubjectsForExisting(container);
                addExistingEventListeners(container);
            }
        });
}

function removeEmptyScheduleRow(btn) {
    const row = btn.closest('tr');
    const scheduleDiv = btn.closest('.new-schedule');
    const hasData = scheduleDiv?.querySelector('.subject-select')?.value;
    
    if (hasData) {
        if (!confirm('This schedule has data. Are you sure you want to remove it?')) {
            return;
        }
    }
    
    row.remove();
}

function addNewTimeSlot(day) {
    const table = document.querySelector(`#schedule-${day} table tbody`);
    const lastRow = table.querySelector('tr:last-child');
    const lastIndex = lastRow ? parseInt(lastRow.dataset.timeIndex) : -1;
    const newIndex = lastIndex + 1;
    
    const newRow = document.createElement('tr');
    newRow.dataset.day = day;
    newRow.dataset.timeIndex = newIndex;
    newRow.className = 'new-time-slot';
    newRow.innerHTML = `
        <td class="fw-bold text-center">
            <div class="time-editable" data-day="${day}" data-index="${newIndex}" onclick="editTime(this)">
                <span class="time-text" style="cursor: pointer; padding: 8px; border-radius: 4px; display: inline-block;">--:-- - --:--</span>
                <div class="time-inputs" style="display: none; gap: 8px; align-items: center;">
                    <input type="time" class="form-control start-time" value="" style="width: 140px; min-width: 140px;">
                    <span style="font-weight: bold;">-</span>
                    <input type="time" class="form-control end-time" value="" style="width: 140px; min-width: 140px;">
                    <button type="button" class="btn btn-sm btn-success save-time-btn" style="margin-left: 8px;">
                        <i class="bi bi-check"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger cancel-time-btn" style="margin-left: 4px;" onclick="this.closest('.time-editable').querySelector('.time-text').style.display='inline-block'; this.closest('.time-inputs').style.display='none';">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        </td>
        <td>
            <div class="p-3 bg-white border rounded new-schedule" data-day="${day}" data-start="" data-end="">
                <div class="row">
                    <div class="col-md-3"><label class="form-label">Subject</label><select class="form-select subject-select"><option value="">Select Subject</option></select></div>
                    <div class="col-md-3"><label class="form-label">Teacher</label><input type="text" class="form-control teacher-display" readonly placeholder="Select Subject First"><select class="form-select teacher-select d-none"></select></div>
                    <div class="col-md-3"><label class="form-label">Room</label><input type="text" class="form-control room-input" placeholder="Room" style="pointer-events: auto;"></div>
                    <div class="col-md-3 d-flex align-items-end"><button class="btn btn-danger w-100" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i> Remove</button></div>
                </div>
            </div>
        </td>
    `;
    
    table.appendChild(newRow);
    loadSubjectsForSelect(newRow.querySelector('.subject-select'), day);
}

function saveAllSchedules() {
    const schedules = [];
    const roomUpdates = [];
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    
    // Collect new schedules
    days.forEach(day => {
        document.querySelectorAll(`#schedule-${day} .new-schedule`).forEach(slot => {
            const subjectSelect = slot.querySelector('.subject-select');
            const teacherSelect = slot.querySelector('.teacher-select');
            const roomInput = slot.querySelector('.room-input');
            const row = slot.closest('tr');
            const timeText = row?.querySelector('.time-text')?.textContent;
            
            if (subjectSelect?.value && teacherSelect?.value && timeText && timeText !== '--:-- - --:--') {
                const timeParts = timeText.includes(' - ') ? timeText.split(' - ') : timeText.split('-');
                const start = timeParts[0].trim();
                const end = timeParts[1].trim();
                schedules.push({
                    section_id: sectionId,
                    subject_id: subjectSelect.value,
                    teacher_id: teacherSelect.value,
                    day_of_week: day,
                    start_time: start + ':00',
                    end_time: end + ':00',
                    room: roomInput?.value || ''
                });
            }
        });
        
        // Collect modified room inputs
        document.querySelectorAll(`#schedule-${day} .room-input-existing[data-modified="true"]`).forEach(input => {
            roomUpdates.push({
                schedule_id: input.dataset.scheduleId,
                room: input.value
            });
        });
    });
    
    if (schedules.length === 0 && roomUpdates.length === 0) {
        showAlert('No changes to save.', 'error');
        return;
    }
    
    let savedCount = 0;
    let errors = [];
    
    const allPromises = [];
    
    // Save new schedules
    schedules.forEach(schedule => {
        const formData = new FormData();
        formData.append('section_id', schedule.section_id);
        formData.append('subject_id', schedule.subject_id);
        formData.append('teacher_id', schedule.teacher_id);
        formData.append('day_of_week', schedule.day_of_week);
        formData.append('start_time', schedule.start_time.replace(':00', ''));
        formData.append('end_time', schedule.end_time.replace(':00', ''));
        formData.append('room', schedule.room);
        
        allPromises.push(
            fetch('<?= base_url('admin/schedules/save') ?>', {method: 'POST', body: formData})
                .then(r => r.json())
                .then(data => {
                    if (data.success) savedCount++;
                    else errors.push(data.error || 'Unknown error');
                })
        );
    });
    
    // Save room updates
    roomUpdates.forEach(update => {
        const formData = new FormData();
        formData.append('schedule_id', update.schedule_id);
        formData.append('room', update.room);
        
        allPromises.push(
            fetch('<?= base_url('admin/schedules/update-room') ?>', {method: 'POST', body: formData})
                .then(r => r.json())
                .then(data => {
                    if (data.success) savedCount++;
                    else errors.push(data.error || 'Unknown error');
                })
        );
    });
    
    Promise.all(allPromises)
    .then(() => {
        if (savedCount > 0) {
            const msg = errors.length > 0 
                ? `Saved ${savedCount} item(s). ${errors.length} failed.<br><small>${errors[0]}</small>`
                : `Successfully saved ${savedCount} item(s)!`;
            showAlert(msg, errors.length > 0 ? 'error' : 'success');
            days.forEach(day => loadSchedules(day));
        } else {
            showAlert('Failed to save changes.<br><small>' + (errors[0] || 'Unknown error') + '</small>', 'error');
        }
    })
    .catch(error => {
        showAlert('Network error: ' + error.message, 'error');
    });
}

function deleteSelectedSchedules(day) {
    const selected = Array.from(document.querySelectorAll(`#schedule-${day} .schedule-checkbox:checked`)).map(cb => cb.value);
    
    if (selected.length === 0) {
        showAlert('Please select schedules to remove', 'error');
        return;
    }
    
    showConfirmModal(`Delete ${selected.length} selected schedule(s)?`, () => {
        let completed = 0;
        let failed = 0;
        
        selected.forEach(id => {
            fetch(`<?= base_url('admin/schedules/delete/') ?>${id}`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) completed++;
                else failed++;
                
                if (completed + failed === selected.length) {
                    loadSchedules(day);
                    if (completed > 0) {
                        showAlert(`${completed} schedule(s) deleted successfully!`, 'success');
                    }
                    if (failed > 0) {
                        showAlert(`${failed} schedule(s) failed to delete`, 'error');
                    }
                }
            });
        });
    });
}

function showConfirmModal(message, onConfirm) {
    const modalHtml = `
    <div class="modal fade" id="scheduleConfirmModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #6c757d; border-color: #6c757d;">Cancel</button>
                    <button type="button" class="btn btn-danger" id="scheduleConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>`;
    
    const existing = document.getElementById('scheduleConfirmModal');
    if (existing) existing.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modalEl = document.getElementById('scheduleConfirmModal');
    const modal = new bootstrap.Modal(modalEl);
    
    document.getElementById('scheduleConfirmBtn').addEventListener('click', function() {
        modal.hide();
        setTimeout(() => onConfirm(), 300);
    }, { once: true });
    
    modal.show();
}

function loadSubjectsForExisting(container) {
    if (window.subjectTeachers) {
        container.querySelectorAll('.subject-select-existing').forEach(select => {
            const currentValue = select.dataset.current;
            select.innerHTML = '';
            Object.keys(window.subjectTeachers).forEach(subjectId => {
                const subject = window.subjectTeachers[subjectId];
                const selected = subjectId === currentValue ? 'selected' : '';
                select.innerHTML += `<option value="${subjectId}" ${selected}>${subject.subject_name}</option>`;
            });
        });
        
        container.querySelectorAll('.teacher-select-existing').forEach(select => {
            const currentValue = select.dataset.current;
            select.innerHTML = '';
            const allTeachers = new Map();
            Object.values(window.subjectTeachers).forEach(subject => {
                subject.teachers.forEach(teacher => {
                    allTeachers.set(teacher.id, teacher.name);
                });
            });
            allTeachers.forEach((name, id) => {
                const selected = id === currentValue ? 'selected' : '';
                select.innerHTML += `<option value="${id}" ${selected}>${name}</option>`;
            });
        });
    }
}

function updateTeacherForSubject(subjectSelect) {
    const scheduleId = subjectSelect.dataset.scheduleId;
    const subjectId = subjectSelect.value;
    const row = subjectSelect.closest('.row');
    const teacherDisplay = row.querySelector('.teacher-display-existing');
    
    if (window.subjectTeachers && window.subjectTeachers[subjectId]) {
        const teachers = window.subjectTeachers[subjectId].teachers;
        if (teachers.length > 0) {
            const teacher = teachers[0];
            teacherDisplay.value = teacher.name;
            teacherDisplay.dataset.teacherId = teacher.id;
            
            updateExistingScheduleFields(scheduleId, subjectId, teacher.id, subjectSelect);
        } else {
            teacherDisplay.value = 'No teacher assigned';
            teacherDisplay.dataset.teacherId = '';
        }
    }
}

function updateExistingScheduleFields(scheduleId, subjectId, teacherId, selectElement) {
    const formData = new FormData();
    formData.append('schedule_id', scheduleId);
    formData.append('subject_id', subjectId);
    formData.append('teacher_id', teacherId);
    
    selectElement.style.backgroundColor = '#fff3cd';
    selectElement.disabled = true;
    
    fetch('<?= base_url('admin/schedules/update-both-fields') ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            selectElement.style.backgroundColor = '#d1edff';
            setTimeout(() => {
                selectElement.style.backgroundColor = '';
            }, 1000);
        } else {
            selectElement.style.backgroundColor = '#f8d7da';
            setTimeout(() => {
                selectElement.style.backgroundColor = '';
            }, 2000);
        }
    })
    .finally(() => {
        selectElement.disabled = false;
    });
}

function updateExistingRoom(input) {
    const scheduleId = input.dataset.scheduleId;
    const room = input.value;
    const originalValue = input.defaultValue;
    
    if (room === originalValue) {
        return; // No change
    }
    
    const formData = new FormData();
    formData.append('schedule_id', scheduleId);
    formData.append('room', room);
    
    // Visual feedback
    input.style.backgroundColor = '#fff3cd';
    input.disabled = true;
    
    fetch('<?= base_url('admin/schedules/update-room') ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.style.backgroundColor = '#d1edff';
            input.defaultValue = room; // Update the default value
            
            // Show brief success message
            const tempMsg = document.createElement('small');
            tempMsg.className = 'text-success';
            tempMsg.textContent = '✓ Saved';
            tempMsg.style.position = 'absolute';
            tempMsg.style.right = '5px';
            tempMsg.style.top = '50%';
            tempMsg.style.transform = 'translateY(-50%)';
            input.parentElement.style.position = 'relative';
            input.parentElement.appendChild(tempMsg);
            
            setTimeout(() => {
                input.style.backgroundColor = '';
                if (tempMsg.parentElement) tempMsg.remove();
            }, 1500);
        } else {
            input.style.backgroundColor = '#f8d7da';
            input.value = originalValue; // Revert to original value
            setTimeout(() => {
                input.style.backgroundColor = '';
            }, 2000);
        }
    })
    .catch(error => {
        input.style.backgroundColor = '#f8d7da';
        input.value = originalValue;
        setTimeout(() => {
            input.style.backgroundColor = '';
        }, 2000);
    })
    .finally(() => {
        input.disabled = false;
    });
}

function showAlert(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const iconClass = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-x-circle' : 'bi-info-circle';
    
    const modalHtml = `
    <div class="modal fade" id="scheduleAlertModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="bi ${iconClass} fs-1 text-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'}"></i>
                    <div class="mt-3 mb-0">${message}</div>
                    <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>`;
    
    const existing = document.getElementById('scheduleAlertModal');
    if (existing) existing.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('scheduleAlertModal'));
    modal.show();
}

function editTime(element) {
    const timeText = element.querySelector('.time-text');
    const timeInputs = element.querySelector('.time-inputs');
    const startInput = timeInputs.querySelector('.start-time');
    const endInput = timeInputs.querySelector('.end-time');
    const day = element.dataset.day;
    const index = parseInt(element.dataset.index);
    const row = element.closest('tr');
    
    timeText.style.display = 'none';
    timeInputs.style.display = 'flex';
    
    let isEditing = true;
    let isSaving = false;
    
    const saveTime = () => {
        if (!isEditing || isSaving) return;
        
        isSaving = true;
        
        const newStart = startInput.value;
        const newEnd = endInput.value;
        
        if (!newStart || !newEnd || newEnd <= newStart) {
            alert('Invalid time range');
            isSaving = false;
            return;
        }
        
        const oldTimeSlot = timeText.textContent;
        const scheduleDiv = row.querySelector('td:last-child > div');
        const hasSchedule = scheduleDiv.querySelector('.subject-select')?.value;
        
        if (hasSchedule && oldTimeSlot !== (newStart + '-' + newEnd)) {
            if (!confirm('Warning: Changing this time slot will clear the schedule data for this time. Continue?')) {
                isSaving = false;
                timeText.style.display = 'inline-block';
                timeInputs.style.display = 'none';
                return;
            }
        }
        
        isEditing = false;
        
        if (newStart && newEnd) {
            const newTimeSlot = newStart + '-' + newEnd;
            timeText.textContent = newTimeSlot;
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
    startInput.addEventListener('click', (e) => e.stopPropagation());
    endInput.addEventListener('click', (e) => e.stopPropagation());
    
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
        saveBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            e.preventDefault();
            saveTime();
        });
    }
    
    const cancelBtn = timeInputs.querySelector('.cancel-time-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            e.preventDefault();
            cancel();
        });
    }
    
    setTimeout(() => startInput.focus(), 50);
}
</script>

<?= $this->endSection() ?>
