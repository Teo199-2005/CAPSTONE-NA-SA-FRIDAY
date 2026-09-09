<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Schedules extends BaseController
{
    public function index()
    {
        $sectionModel = new \App\Models\SectionModel();
        $sections = $sectionModel->getSectionsWithAdviser(get_current_school_year());
        
        return view('admin/schedules', [
            'title' => 'Section Schedules - CSCS SMS',
            'sections' => $sections
        ]);
    }
    
    public function section($sectionId)
    {
        $sectionModel = new \App\Models\SectionModel();
        $db = \Config\Database::connect();
        
        // Get section with adviser information
        $section = $db->query(
            "SELECT s.*, CONCAT(t.first_name, ' ', t.last_name) as adviser_name
             FROM sections s
             LEFT JOIN teachers t ON t.id = s.adviser_id
             WHERE s.id = ?",
            [$sectionId]
        )->getRowArray();
        
        if (!$section) {
            return redirect()->to('admin/schedules')->with('error', 'Section not found');
        }
        
        return view('admin/section_schedule', [
            'title' => 'Manage Schedule - ' . $section['section_name'],
            'section' => $section
        ]);
    }
    
    public function getSchedules($sectionId, $day)
    {
        $db = \Config\Database::connect();
        $schedules = $db->query(
            "SELECT ts.*, s.subject_name, CONCAT(t.first_name, ' ', t.last_name) as teacher_name
             FROM teacher_schedules ts
             LEFT JOIN subjects s ON s.id = ts.subject_id
             LEFT JOIN teachers t ON t.id = ts.teacher_id
             WHERE ts.section_id = ? AND ts.day_of_week = ?
             ORDER BY ts.start_time",
            [$sectionId, $day]
        )->getResultArray();
        
        return $this->response->setJSON(['success' => true, 'schedules' => $schedules]);
    }
    
    public function form($sectionId, $day)
    {
        $db = \Config\Database::connect();
        $subjects = $db->table('section_subjects ss')
            ->select('s.*')
            ->join('subjects s', 's.id = ss.subject_id')
            ->where('ss.section_id', $sectionId)
            ->get()->getResultArray();
        
        $teachers = $db->table('teachers')->where('employment_status', 'active')->get()->getResultArray();
        
        $html = '<div class="modal fade" id="scheduleModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form onsubmit="saveSchedule(event)" action="' . base_url('admin/schedules/save') . '">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Schedule - ' . $day . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="section_id" value="' . $sectionId . '">
                            <input type="hidden" name="day_of_week" value="' . $day . '">
                            <div class="mb-3">
                                <label>Subject</label>
                                <select name="subject_id" class="form-select" required>';
        foreach ($subjects as $s) {
            $html .= '<option value="' . $s['id'] . '">' . esc($s['subject_name']) . '</option>';
        }
        $html .= '</select>
                            </div>
                            <div class="mb-3">
                                <label>Teacher</label>
                                <select name="teacher_id" class="form-select" required>';
        foreach ($teachers as $t) {
            $html .= '<option value="' . $t['id'] . '">' . esc($t['first_name'] . ' ' . $t['last_name']) . '</option>';
        }
        $html .= '</select>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label>Start Time</label>
                                    <input type="time" name="start_time" class="form-control" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label>End Time</label>
                                    <input type="time" name="end_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Room</label>
                                <input type="text" name="room" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
        
        return $this->response->setBody($html);
    }
    
    public function save()
    {
        $db = \Config\Database::connect();
        
        $data = [
            'section_id' => $this->request->getPost('section_id'),
            'subject_id' => $this->request->getPost('subject_id'),
            'teacher_id' => $this->request->getPost('teacher_id'),
            'day_of_week' => $this->request->getPost('day_of_week'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'room' => $this->request->getPost('room'),
            'school_year' => get_current_school_year(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Check for subject conflict on same day
        $subjectConflict = $this->checkSubjectConflict(
            $data['subject_id'],
            $data['section_id'],
            $data['day_of_week'],
            $data['school_year']
        );
        
        if ($subjectConflict) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Subject conflict: ' . $subjectConflict['subject_name'] . ' is already scheduled on ' . $data['day_of_week'] . ' from ' . date('g:i A', strtotime($subjectConflict['start_time'])) . ' to ' . date('g:i A', strtotime($subjectConflict['end_time']))
            ]);
        }
        
        // Check for teacher time conflict
        $teacherConflict = $this->checkTeacherConflict(
            $data['teacher_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            $data['school_year']
        );
        
        if ($teacherConflict) {
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Teacher conflict: ' . $teacherConflict['teacher_name'] . ' is already scheduled for ' . $teacherConflict['subject_name'] . ' in ' . $teacherConflict['section_name'] . ' on ' . $data['day_of_week'] . ' from ' . date('g:i A', strtotime($teacherConflict['start_time'])) . ' to ' . date('g:i A', strtotime($teacherConflict['end_time']))
            ]);
        }
        
        // Check for room conflict if room is specified
        if (!empty($data['room'])) {
            $roomConflict = $this->checkRoomConflict(
                $data['room'],
                $data['day_of_week'],
                $data['start_time'],
                $data['end_time'],
                $data['school_year']
            );
            
            if ($roomConflict) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Room conflict: ' . $data['room'] . ' is already occupied by ' . $roomConflict['teacher_name'] . ' for ' . $roomConflict['subject_name'] . ' in ' . $roomConflict['section_name'] . ' on ' . $data['day_of_week'] . ' from ' . date('g:i A', strtotime($roomConflict['start_time'])) . ' to ' . date('g:i A', strtotime($roomConflict['end_time']))
                ]);
            }
        }
        
        if ($db->table('teacher_schedules')->insert($data)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'error' => 'Failed to save']);
    }
    
    public function delete($id)
    {
        $db = \Config\Database::connect();
        if ($db->table('teacher_schedules')->where('id', $id)->delete()) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false]);
    }
    
    public function updateRoom()
    {
        $db = \Config\Database::connect();
        
        $scheduleId = $this->request->getPost('schedule_id');
        $room = $this->request->getPost('room');
        
        // Get current schedule details
        $currentSchedule = $db->table('teacher_schedules')
            ->where('id', $scheduleId)
            ->get()->getRowArray();
            
        if (!$currentSchedule) {
            return $this->response->setJSON(['success' => false, 'error' => 'Schedule not found']);
        }
        
        // Check for room conflict if room is specified (excluding current schedule)
        if (!empty($room)) {
            $roomConflict = $db->query(
                "SELECT ts.*, sub.subject_name, sec.section_name,
                       CONCAT(t.first_name, ' ', t.last_name) as teacher_name
                FROM teacher_schedules ts
                LEFT JOIN subjects sub ON sub.id = ts.subject_id
                LEFT JOIN sections sec ON sec.id = ts.section_id
                LEFT JOIN teachers t ON t.id = ts.teacher_id
                WHERE ts.room = ? 
                AND ts.day_of_week = ? 
                AND ts.school_year = ?
                AND ts.id != ?
                AND (
                    (ts.start_time < ? AND ts.end_time > ?) OR
                    (ts.start_time < ? AND ts.end_time > ?) OR
                    (ts.start_time >= ? AND ts.end_time <= ?)
                )",
                [
                    $room, $currentSchedule['day_of_week'], $currentSchedule['school_year'], $scheduleId,
                    $currentSchedule['end_time'], $currentSchedule['start_time'],
                    $currentSchedule['start_time'], $currentSchedule['start_time'],
                    $currentSchedule['start_time'], $currentSchedule['end_time']
                ]
            )->getRowArray();
            
            if ($roomConflict) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Room conflict: ' . $room . ' is already occupied by ' . $roomConflict['teacher_name'] . ' for ' . $roomConflict['subject_name'] . ' in ' . $roomConflict['section_name'] . ' on ' . $currentSchedule['day_of_week'] . ' from ' . date('g:i A', strtotime($roomConflict['start_time'])) . ' to ' . date('g:i A', strtotime($roomConflict['end_time']))
                ]);
            }
        }
        
        $data = [
            'room' => $room,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($db->table('teacher_schedules')->where('id', $scheduleId)->update($data)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'error' => 'Failed to update room']);
    }
    
    public function updateBothFields()
    {
        $db = \Config\Database::connect();
        
        $scheduleId = $this->request->getPost('schedule_id');
        $subjectId = $this->request->getPost('subject_id');
        $teacherId = $this->request->getPost('teacher_id');
        
        // Get current schedule details
        $currentSchedule = $db->table('teacher_schedules')
            ->where('id', $scheduleId)
            ->get()->getRowArray();
            
        if (!$currentSchedule) {
            return $this->response->setJSON(['success' => false, 'error' => 'Schedule not found']);
        }
        
        // Check for teacher conflict (excluding current schedule)
        $teacherConflict = $db->query(
            "SELECT ts.*, sub.subject_name, sec.section_name, 
                   CONCAT(t.first_name, ' ', t.last_name) as teacher_name
            FROM teacher_schedules ts
            LEFT JOIN subjects sub ON sub.id = ts.subject_id
            LEFT JOIN sections sec ON sec.id = ts.section_id
            LEFT JOIN teachers t ON t.id = ts.teacher_id
            WHERE ts.teacher_id = ? 
            AND ts.day_of_week = ? 
            AND ts.school_year = ?
            AND ts.id != ?
            AND (
                (ts.start_time < ? AND ts.end_time > ?) OR
                (ts.start_time < ? AND ts.end_time > ?) OR
                (ts.start_time >= ? AND ts.end_time <= ?)
            )",
            [
                $teacherId, $currentSchedule['day_of_week'], $currentSchedule['school_year'], $scheduleId,
                $currentSchedule['end_time'], $currentSchedule['start_time'],
                $currentSchedule['start_time'], $currentSchedule['start_time'],
                $currentSchedule['start_time'], $currentSchedule['end_time']
            ]
        )->getRowArray();
        
        if ($teacherConflict) {
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Teacher conflict: ' . $teacherConflict['teacher_name'] . ' is already scheduled for ' . $teacherConflict['subject_name'] . ' in ' . $teacherConflict['section_name'] . ' on ' . $currentSchedule['day_of_week'] . ' from ' . date('g:i A', strtotime($teacherConflict['start_time'])) . ' to ' . date('g:i A', strtotime($teacherConflict['end_time']))
            ]);
        }
        
        $data = [
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($db->table('teacher_schedules')->where('id', $scheduleId)->update($data)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'error' => 'Failed to update schedule']);
    }
    
    public function subjects($sectionId)
    {
        $db = \Config\Database::connect();
        $subjects = $db->table('section_subjects ss')
            ->select('s.*')
            ->join('subjects s', 's.id = ss.subject_id')
            ->where('ss.section_id', $sectionId)
            ->where('ss.is_active', 1)
            ->get()->getResultArray();
        
        return $this->response->setJSON(['success' => true, 'subjects' => $subjects]);
    }
    
    public function subjectTeachers($sectionId)
    {
        $db = \Config\Database::connect();
        
        // Get subjects for this section
        $subjects = $db->table('section_subjects ss')
            ->select('s.id, s.subject_name')
            ->join('subjects s', 's.id = ss.subject_id')
            ->where('ss.section_id', $sectionId)
            ->where('ss.is_active', 1)
            ->get()->getResultArray();
        
        $result = [];
        foreach ($subjects as $subject) {
            // Get teachers assigned to this subject in this section
            $teachers = $db->query(
                "SELECT DISTINCT t.id, CONCAT(t.first_name, ' ', t.last_name) as name
                 FROM teachers t
                 INNER JOIN teacher_schedules ts ON ts.teacher_id = t.id
                 WHERE ts.section_id = ? AND ts.subject_id = ?
                 UNION
                 SELECT t.id, CONCAT(t.first_name, ' ', t.last_name) as name
                 FROM teachers t
                 INNER JOIN sections s ON s.adviser_id = t.id
                 WHERE s.id = ?",
                [$sectionId, $subject['id'], $sectionId]
            )->getResultArray();
            
            // If no teachers assigned, get all active teachers
            if (empty($teachers)) {
                $teachers = $db->table('teachers')
                    ->select('id, CONCAT(first_name, " ", last_name) as name', false)
                    ->where('employment_status', 'active')
                    ->get()->getResultArray();
            }
            
            $result[$subject['id']] = [
                'subject_name' => $subject['subject_name'],
                'teachers' => $teachers
            ];
        }
        
        return $this->response->setJSON(['success' => true, 'subject_teachers' => $result]);
    }
    
    public function sectionSchedule($sectionId)
    {
        $db = \Config\Database::connect();
        
        $classSchedules = $db->query(
            "SELECT ts.*, sub.subject_name, 
                   CONCAT(t.first_name, ' ', t.last_name) as teacher_name
            FROM teacher_schedules ts
            LEFT JOIN subjects sub ON sub.id = ts.subject_id
            LEFT JOIN teachers t ON t.id = ts.teacher_id
            WHERE ts.section_id = ? AND ts.school_year = ?
            ORDER BY ts.day_of_week, ts.start_time",
            [$sectionId, get_current_school_year()]
        )->getResultArray();
        
        // Organize schedules by day and time
        $schedules = [];
        foreach ($classSchedules as $schedule) {
            $timeSlot = date('H:i', strtotime($schedule['start_time'])) . '-' . date('H:i', strtotime($schedule['end_time']));
            $schedules[strtolower($schedule['day_of_week'])][$timeSlot] = $schedule;
        }
        
        return $this->response->setJSON(['success' => true, 'schedules' => $schedules]);
    }
    
    private function checkTeacherConflict($teacherId, $dayOfWeek, $startTime, $endTime, $schoolYear)
    {
        $db = \Config\Database::connect();
        
        $conflict = $db->query(
            "SELECT ts.*, sub.subject_name, sec.section_name, 
                   CONCAT(t.first_name, ' ', t.last_name) as teacher_name
            FROM teacher_schedules ts
            LEFT JOIN subjects sub ON sub.id = ts.subject_id
            LEFT JOIN sections sec ON sec.id = ts.section_id
            LEFT JOIN teachers t ON t.id = ts.teacher_id
            WHERE ts.teacher_id = ? 
            AND ts.day_of_week = ? 
            AND ts.school_year = ?
            AND (
                (ts.start_time < ? AND ts.end_time > ?) OR
                (ts.start_time < ? AND ts.end_time > ?) OR
                (ts.start_time >= ? AND ts.end_time <= ?)
            )",
            [
                $teacherId, $dayOfWeek, $schoolYear,
                $endTime, $startTime,
                $startTime, $startTime,
                $startTime, $endTime
            ]
        )->getRowArray();
        
        return $conflict;
    }
    
    private function checkRoomConflict($room, $dayOfWeek, $startTime, $endTime, $schoolYear)
    {
        $db = \Config\Database::connect();
        
        $conflict = $db->query(
            "SELECT ts.*, sub.subject_name, sec.section_name,
                   CONCAT(t.first_name, ' ', t.last_name) as teacher_name
            FROM teacher_schedules ts
            LEFT JOIN subjects sub ON sub.id = ts.subject_id
            LEFT JOIN sections sec ON sec.id = ts.section_id
            LEFT JOIN teachers t ON t.id = ts.teacher_id
            WHERE ts.room = ? 
            AND ts.day_of_week = ? 
            AND ts.school_year = ?
            AND (
                (ts.start_time < ? AND ts.end_time > ?) OR
                (ts.start_time < ? AND ts.end_time > ?) OR
                (ts.start_time >= ? AND ts.end_time <= ?)
            )",
            [
                $room, $dayOfWeek, $schoolYear,
                $endTime, $startTime,
                $startTime, $startTime,
                $startTime, $endTime
            ]
        )->getRowArray();
        
        return $conflict;
    }
    
    private function checkSubjectConflict($subjectId, $sectionId, $dayOfWeek, $schoolYear)
    {
        $db = \Config\Database::connect();
        
        $conflict = $db->query(
            "SELECT ts.*, sub.subject_name
            FROM teacher_schedules ts
            LEFT JOIN subjects sub ON sub.id = ts.subject_id
            WHERE ts.subject_id = ? 
            AND ts.section_id = ?
            AND ts.day_of_week = ? 
            AND ts.school_year = ?",
            [$subjectId, $sectionId, $dayOfWeek, $schoolYear]
        )->getRowArray();
        
        return $conflict;
    }
}

