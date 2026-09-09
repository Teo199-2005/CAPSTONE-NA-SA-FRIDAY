<?php
namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\StudentModel;
use App\Models\ParentModel;
use App\Models\LoginAttemptModel;
use App\Models\PlatformRatingModel;

class Auth extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function login()
    {
        // If user is already logged in, redirect to dashboard
        try {
            if ($this->auth->loggedIn()) {
                return redirect()->to($this->getDashboardUrl());
            }
        } catch (\Throwable $e) {
            // Database may not be configured yet; continue to show login form
        }

        // Get registration status
        try {
            $systemSettingModel = new \App\Models\SystemSettingModel();
            $registrationSetting = $systemSettingModel->getSetting('registration_enabled', null);
            if ($registrationSetting === null) {
                $registrationSetting = $systemSettingModel->getSetting('enrollment_enabled', 1); // backward compatibility
            }
            $registrationEnabled = (bool) $registrationSetting;
        } catch (\Throwable $e) {
            $registrationEnabled = true;
        }
        
        // Use modern login page
        return view('auth/login', [
            'title' => 'Login - CSCS SMS',
            'registrationEnabled' => $registrationEnabled
        ]);
    }

    public function attempt()
    {
        $rules = [
            'identifier' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $identifier = trim((string) $this->request->getPost('identifier'));
        $password = (string) $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');
        $ipAddress = $this->request->getIPAddress();

        try {
            return $this->performLoginAttempt($identifier, $password, $remember, $ipAddress);
        } catch (DatabaseException $e) {
            log_message('critical', 'Login database error: ' . $e->getMessage());

            return redirect()->back()->withInput()
                ->with('error', 'The system cannot connect to the database right now. Please try again in a few minutes or contact your school administrator.');
        }
    }

    /**
     * @return ResponseInterface
     */
    private function performLoginAttempt(string $identifier, string $password, bool $remember, string $ipAddress)
    {
        // Check if user is locked out
        $lockoutInfo = $this->checkLockout($identifier, $ipAddress);
        if ($lockoutInfo['locked']) {
            return redirect()->back()->withInput()
                ->with('error', $lockoutInfo['message'])
                ->with('locked_until', $lockoutInfo['locked_until']);
        }

        helper(['auth', 'student_auth']);

        // Find user by PRC license (teacher) or LRN (student)
        $teacherModel = model('TeacherModel');
        $studentModel = model('StudentModel');
        $userModel = model(UserModel::class);
        
        $user = null;
        
        // Check if it's a teacher (PRC license or email)
        $teacher = $teacherModel->where('license_number', $identifier)
                                ->orWhere('email', $identifier)
                                ->first();
        
        // Debug logging
        log_message('info', 'Login attempt - Identifier: ' . $identifier);
        log_message('info', 'Teacher found: ' . ($teacher ? 'Yes (ID: ' . $teacher['id'] . ', User ID: ' . ($teacher['user_id'] ?? 'NULL') . ')' : 'No'));
        
        if ($teacher && $teacher['user_id']) {
            $user = $userModel->find($teacher['user_id']);
            log_message('info', 'User found from teacher: ' . ($user ? 'Yes (ID: ' . $user->id . ')' : 'No'));
        }
        
        // Check if it's a student (LRN or email)
        if (!$user) {
            $student = $studentModel->where('lrn', $identifier)->first();
            if (!$student && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $student = $studentModel->where('email', $identifier)->first();
            }
            log_message('info', 'Student found by LRN/email: ' . ($student ? 'Yes (ID: ' . $student['id'] . ', User ID: ' . ($student['user_id'] ?? 'NULL') . ')' : 'No'));
            if ($student && !empty($student['user_id'])) {
                $user = $userModel->find($student['user_id']);
                log_message('info', 'User found from student: ' . ($user ? 'Yes (ID: ' . $user->id . ')' : 'No'));
            }
        }
        
        // Check by email (fallback for teachers/admin/other users not found via role tables)
        if (!$user && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = $userModel->where('email', $identifier)->first();
        }
        
        if (!$user) {
            log_message('info', 'Login failed - No user found for identifier: ' . $identifier);
            // Also check what teachers exist in database for debugging
            $allTeachers = $teacherModel->select('id, first_name, last_name, license_number, email')->findAll();
            log_message('info', 'All teachers in database: ' . json_encode($allTeachers));
            return redirect()->back()->withInput()->with('error', 'Invalid PRC license number, LRN, email, or password.');
        }
        
        // Get password hash from auth_identities
        $db = \Config\Database::connect();
        $identity = $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->get()
            ->getRow();
        
        log_message('info', 'Auth identity found: ' . ($identity ? 'Yes (Name: ' . $identity->name . ')' : 'No'));

        $passwordValid = verify_auth_identity_password($password, $identity);
        log_message('info', 'Password verification: ' . ($passwordValid ? 'Success' : 'Failed'));
        
        if (!$identity || !$passwordValid) {
            log_message('info', 'Login failed - Invalid credentials for user ID: ' . $user->id);
            $this->recordFailedAttempt($identifier, $ipAddress);
            $lockoutInfo = $this->checkLockout($identifier, $ipAddress);
            $response = redirect()->back()->withInput()->with('error', $lockoutInfo['locked'] ? $lockoutInfo['message'] : 'Invalid PRC license number, LRN, email, or password.');
            if ($lockoutInfo['locked']) {
                $response = $response->with('locked_until', $lockoutInfo['locked_until']);
            }
            return $response;
        }
        
        // Handle remember me functionality
        if ($remember) {
            // Store only the identifier cookie for pre-filling login form
            // Password is intentionally NOT stored in cookies for security
            setcookie('remembered_identifier', $identifier, [
                'expires' => time() + (30 * 24 * 60 * 60),
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            // Clear any previously stored password cookie
            setcookie('remembered_password', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        } else {
            // Clear remember me cookies if not checked
            setcookie('remembered_identifier', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            setcookie('remembered_password', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        
        // Ensure user is active
        if ((int) ($user->active ?? 0) !== 1) {
            $user->active = 1;
            $userModel->save($user);
        }
        
        // Manually log in the user
        $sessionAuth = auth()->getAuthenticator('session');
        $sessionAuth->login($user);
        
        // Clear failed attempts on successful login
        $this->clearFailedAttempts($identifier, $ipAddress);

        helper('admin_access');
        if ($user->inGroup('admin_staff')) {
            $dest = admin_staff_post_login_redirect_url((int) $user->id);
            if ($dest === null) {
                $sessionAuth->logout();

                return redirect()->to(base_url('login'))
                    ->with('error', 'No admin portal pages have been assigned to your account. Please contact a master administrator.');
            }

            return redirect()->to($dest);
        }
        
        return redirect()->to($this->getDashboardUrl());
    }

    private function checkLockout(string $identifier, string $ipAddress): array
    {
        try {
            $attemptModel = new LoginAttemptModel();
            $attempt = $attemptModel->where('identifier', $identifier)
                                    ->where('ip_address', $ipAddress)
                                    ->first();
        } catch (\Throwable $e) {
            log_message('error', 'Login lockout check skipped: ' . $e->getMessage());

            return ['locked' => false];
        }

        if (!$attempt) {
            return ['locked' => false];
        }

        if ($attempt['locked_until'] && strtotime($attempt['locked_until']) > time()) {
            $remainingTime = strtotime($attempt['locked_until']) - time();
            $minutes = max(1, (int) ceil($remainingTime / 60));
            return [
                'locked' => true,
                'message' => "Too many failed login attempts. Please try again in {$minutes} minute(s).",
                'locked_until' => $attempt['locked_until']
            ];
        }

        // Lock expired — reset counter so the next attempt is not instantly locked again
        if ($attempt['locked_until'] && strtotime($attempt['locked_until']) <= time()) {
            $attemptModel->update($attempt['id'], [
                'attempts'     => 0,
                'locked_until' => null,
            ]);
        }

        return ['locked' => false];
    }

    private function recordFailedAttempt(string $identifier, string $ipAddress): void
    {
        try {
            $attemptModel = new LoginAttemptModel();
            $attempt = $attemptModel->where('identifier', $identifier)
                                    ->where('ip_address', $ipAddress)
                                    ->first();
        } catch (\Throwable $e) {
            log_message('error', 'Failed to record login attempt: ' . $e->getMessage());

            return;
        }

        if ($attempt) {
            $newAttempts = $attempt['attempts'] + 1;
            $lockoutMinutes = $this->calculateLockoutTime($newAttempts);
            
            $attemptModel->update($attempt['id'], [
                'attempts' => $newAttempts,
                'locked_until' => $lockoutMinutes > 0 ? date('Y-m-d H:i:s', time() + ($lockoutMinutes * 60)) : null
            ]);
        } else {
            $attemptModel->insert([
                'identifier' => $identifier,
                'ip_address' => $ipAddress,
                'attempts' => 1,
                'locked_until' => null
            ]);
        }
    }

    private function calculateLockoutTime(int $attempts): int
    {
        if ($attempts < 3) {
            return 0;
        }
        // Progressive lockout: 1 min, 5 min, 10 min, 30 min, 60 min, etc.
        $lockoutTimes = [1, 5, 10, 30, 60];
        $index = $attempts - 3;
        return $lockoutTimes[$index] ?? 60;
    }

    private function clearFailedAttempts(string $identifier, string $ipAddress): void
    {
        try {
            $attemptModel = new LoginAttemptModel();
            $attemptModel->where('identifier', $identifier)
                         ->where('ip_address', $ipAddress)
                         ->delete();
        } catch (\Throwable $e) {
            log_message('error', 'Failed to clear login attempts: ' . $e->getMessage());
        }
    }

    public function register()
    {
        if (! $this->isRegistrationOpen()) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Student registration is currently closed. Please check back later or contact the school office.');
        }

        return view('auth/register', [
            'title' => 'Student Registration - CSCS SMS',
        ]);
    }

    public function forgot()
    {
        // Delegate to Shield magic link if enabled, otherwise show informational page
        if (setting('Auth.allowMagicLinkLogins')) {
            return redirect()->to(url_to('magic-link'));
        }
        return redirect()->to(base_url('login'))
            ->with('error', 'Password recovery is not enabled. Please contact the administrator.');
    }

    /**
     * Redirect logged-in user to the correct dashboard
     */
    public function dashboard()
    {
        try {
            if ($this->auth->loggedIn()) {
                return redirect()->to($this->getDashboardUrl());
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return redirect()->to(base_url('login'));
    }

    public function store()
    {
        if (! $this->isRegistrationOpen()) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Student registration is currently closed.');
        }

        helper('student_form');

        $rules = [
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'middle_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'gender' => 'required|in_list[Male,Female]',
            'date_of_birth' => 'required|valid_date',
            'grade_level' => 'required|integer|greater_than_equal_to[0]|less_than[7]',
            'lrn' => 'required|exact_length[12]|numeric|is_unique[students.lrn]',
            'student_type' => 'required|in_list[New Student,Transferee,Old Student]',
            'place_of_birth' => 'required|max_length[255]',
            'nationality' => 'required|in_list[' . implode(',', nationality_options()) . ']',
            'religion' => 'required|max_length[100]',
            'contact_number' => 'required|max_length[20]',
            'address' => 'required',
            'emergency_contact_name' => 'required|max_length[255]',
            'emergency_contact_number' => 'required|max_length[20]',
            'emergency_contact_relationship' => emergency_contact_relationship_rule(),
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorStep = $this->detectErrorStep($errors);
            return redirect()->back()->withInput()->with('errors', $errors)->with('error_step', $errorStep);
        }

        $userModel = new UserModel();
        $studentModel = new StudentModel();

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $firstName = $this->request->getPost('first_name');
            $lastName = $this->request->getPost('last_name');

            if (! $userModel->save([
                'email'    => $email,
                'password' => $password,
                'active'   => 0,
            ])) {
                throw new \Exception('Failed to create user account');
            }

            $userId = (int) $userModel->getInsertID();
            $user = $userModel->findById($userId);

            if (! $user) {
                throw new \Exception('Failed to load new user account');
            }

            $user->fill([
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ]);
            $userModel->save($user);
            $user->addGroup('student');

            $gradeLevel = (int) $this->request->getPost('grade_level');

            $studentData = [
                'user_id' => $userId,
                'lrn' => $this->request->getPost('lrn'),
                'student_type' => $this->request->getPost('student_type'),
                'first_name' => $this->request->getPost('first_name'),
                'middle_name' => $this->request->getPost('middle_name'),
                'last_name' => $this->request->getPost('last_name'),
                'suffix' => $this->request->getPost('suffix'),
                'gender' => $this->request->getPost('gender'),
                'date_of_birth' => $this->request->getPost('date_of_birth'),
                'place_of_birth' => $this->request->getPost('place_of_birth'),
                'nationality' => $this->request->getPost('nationality') ?: 'Filipino',
                'religion' => $this->request->getPost('religion'),
                'contact_number' => $this->request->getPost('contact_number'),
                'email' => $this->request->getPost('email'),
                'address' => $this->request->getPost('address'),
                'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
                'emergency_contact_number' => $this->request->getPost('emergency_contact_number'),
                'emergency_contact_relationship' => $this->request->getPost('emergency_contact_relationship'),
                'enrollment_status' => 'pending',
                'grade_level' => $gradeLevel,
                'school_year' => get_current_school_year(),
                'temp_password' => $this->request->getPost('password')
            ];
            
            log_message('debug', 'Registration - Student data to save: ' . json_encode($studentData));

            $studentId = $studentModel->skipValidation(true)->insert($studentData);

            if (!$studentId) {
                $errors = $studentModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                throw new \Exception('Failed to create student record: ' . $errorMsg);
            }

            helper('student_auth');
            if (! sync_student_auth_password($userId, $email, $password)) {
                throw new \Exception('Failed to configure login credentials');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to(base_url('login'))
                ->with('success', 'Registration submitted successfully! You will receive an email with your login credentials once your application is approved by school administrators.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->with('error_step', 4);
        }
    }
    
    private function isRegistrationOpen(): bool
    {
        try {
            $systemSettingModel = new \App\Models\SystemSettingModel();
            $registrationSetting = $systemSettingModel->getSetting('registration_enabled', null);
            if ($registrationSetting === null) {
                $registrationSetting = $systemSettingModel->getSetting('enrollment_enabled', 1);
            }

            return (bool) $registrationSetting;
        } catch (\Throwable $e) {
            return true;
        }
    }

    private function detectErrorStep(array $errors): int
    {
        $step1Fields = ['first_name', 'last_name', 'middle_name', 'gender', 'date_of_birth', 'grade_level', 'lrn', 'student_type', 'place_of_birth', 'nationality', 'religion'];
        $step2Fields = ['email', 'contact_number', 'address'];
        $step3Fields = ['emergency_contact_name', 'emergency_contact_number', 'emergency_contact_relationship'];
        $step4Fields = ['password', 'password_confirm'];
        
        foreach ($errors as $field => $error) {
            if (in_array($field, $step1Fields)) return 1;
            if (in_array($field, $step2Fields)) return 2;
            if (in_array($field, $step3Fields)) return 3;
            if (in_array($field, $step4Fields)) return 4;
        }
        
        return 1;
    }

    public function logout(): ResponseInterface
    {
        if ($this->auth->loggedIn()) {
            helper('platform_rating');
            $user = $this->auth->user();
            $role = platform_rating_responder_role($user);

            if ($role !== null) {
                $model = new PlatformRatingModel();
                if (! $model->hasSubmittedForCurrentTerm((int) $user->id)) {
                    $dashboard = $role === 'teacher'
                        ? base_url('teacher/dashboard')
                        : base_url('student/dashboard');

                    return redirect()->to($dashboard)->with('platform_rating_required', true);
                }
            }
        }

        $this->auth->logout();

        return redirect()->to(base_url('/'));
    }

    public function demo($role = null, $subRole = null)
    {
        // Build the role key from parameters
        $roleKey = $subRole !== null ? strtolower($role) . '/' . strtolower($subRole) : strtolower($role);
        
        if (!$roleKey) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Invalid demo role specified.');
        }

        $db = \Config\Database::connect();
        $userModel = model(UserModel::class);
        
        // Demo credentials matching DemoAccountsCompleteSeeder
        $demoCredentials = [
            'admin' => [
                'email' => 'demo.admin@lphs.edu',
                'password' => 'DemoPass123!',
                'redirect' => base_url('admin/dashboard')
            ],
            'teacher/nonnumeric' => [
                'email' => 'teacher.santos@lphs.edu',
                'password' => 'Teacher123!',
                'identifier' => 'PRC-2024-001', // non-numerical PRC license
                'redirect' => base_url('teacher/dashboard')
            ],
            'teacher/numerical' => [
                'email' => 'teacher.reyes@lphs.edu',
                'password' => 'Teacher123!',
                'identifier' => 'EMP-2024-002', // numerical employee ID
                'redirect' => base_url('teacher/dashboard')
            ],
            'student/numerical' => [
                'email' => 'demo.student1@lphs.edu',
                'password' => 'DemoPass123!',
                'identifier' => '136001000010', // numerical LRN
                'redirect' => base_url('student/dashboard')
            ],
            'student/nonnumeric' => [
                'email' => 'demo.student2@lphs.edu',
                'password' => 'DemoPass123!',
                'identifier' => 'STU-2024-DEMO', // non-numerical LRN
                'redirect' => base_url('student/dashboard')
            ]
        ];

        if (!isset($demoCredentials[$roleKey])) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Invalid demo role: ' . $roleKey);
        }

        $cred = $demoCredentials[$roleKey];
        
        // Find user by email
        $user = $userModel->where('email', $cred['email'])->first();
        
        if (!$user) {
            log_message('error', "Demo login failed - User not found: {$cred['email']}");
            return redirect()->to(base_url('login'))
                ->with('error', 'Demo account not found. Please run the demo seeder first.');
        }

        // Verify password
        $identity = $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->get()
            ->getRow();

        if (!$identity || !verify_auth_identity_password($cred['password'], $identity)) {
            log_message('error', "Demo login failed - Invalid password for: {$cred['email']}");
            return redirect()->to(base_url('login'))
                ->with('error', 'Demo account password is invalid. Please run: php spark db:seed DemoAccountsSeeder');
        }

        // Ensure user is active
        if ((int) ($user->active ?? 0) !== 1) {
            $user->active = 1;
            $userModel->save($user);
        }

        // Special handling for teacher demo - log in as teacher
        if (strpos($roleKey, 'teacher') === 0) {
            $teacherModel = model('TeacherModel');
            $teacher = $teacherModel->where('user_id', $user->id)->first();
            
            if (!$teacher) {
                // Try finding by email
                $teacher = $teacherModel->where('email', $user->email)->first();
            }
            
            if ($teacher) {
                log_message('info', "Demo teacher login successful: {$teacher['first_name']} {$teacher['last_name']}");
            }
        }

        // Special handling for student demo - log in as student
        if (strpos($roleKey, 'student') === 0) {
            $studentModel = model('StudentModel');
            $student = $studentModel->where('user_id', $user->id)->first();
            
            if (!$student) {
                $student = $studentModel->where('email', $user->email)->first();
            }
            
            if ($student) {
                log_message('info', "Demo student login successful: {$student['first_name']} {$student['last_name']} (LRN: {$student['lrn']})");
            }
        }

        // Log in the user
        try {
            $sessionAuth = auth()->getAuthenticator('session');
            $sessionAuth->login($user);
            log_message('info', "Demo login successful - User ID: {$user->id}, Email: {$user->email}, Role: {$roleKey}");
        } catch (\Throwable $e) {
            log_message('error', 'Demo login session error: ' . $e->getMessage());
            return redirect()->to(base_url('login'))
                ->with('error', 'Demo login failed due to session error. Please try again.');
        }

        return redirect()->to($cred['redirect']);
    }

    /**
     * Get dashboard URL based on user role
     */
    private function getDashboardUrl(): string
    {
        // Check if user is logged in before accessing user data
        if (!$this->auth->loggedIn()) {
            return base_url('login');
        }

        try {
            $user = $this->auth->user();

            helper('admin_access');
            if ($user->inGroup('admin')) {
                return base_url('admin/dashboard');
            }
            if ($user->inGroup('admin_staff')) {
                return admin_staff_post_login_redirect_url((int) $user->id) ?? base_url('/');
            }
            if ($user->inGroup('teacher')) {
                return base_url('teacher/dashboard');
            } elseif ($user->inGroup('student')) {
                // Check if student is approved before allowing access
                $studentModel = new \App\Models\StudentModel();
                $student = $studentModel->where('user_id', $user->id)->first();

                if (!$student) {
                    // Try to find student by email as fallback
                    $student = $studentModel->where('email', $user->email)->first();
                    
                    if (!$student) {
                        // Student record not found - logout and redirect to login with error
                        $this->auth->logout();
                        session()->setFlashdata('error', 'Student record not found. Please contact the administration.');
                        return base_url('login');
                    } else {
                        // Update student record with correct user_id
                        $studentModel->update($student['id'], ['user_id' => $user->id]);
                    }
                }

                // Check enrollment status - allow enrolled students to access dashboard
                if ($student['enrollment_status'] === 'enrolled') {
                    return base_url('student/dashboard');
                } elseif ($student['enrollment_status'] === 'approved') {
                    return base_url('student/dashboard');
                } elseif ($student['enrollment_status'] === 'pending') {
                    // Student is pending approval - logout and show message
                    $this->auth->logout();
                    session()->setFlashdata('error', 'Your enrollment is still pending approval. Please wait for admin approval before accessing the system.');
                    return base_url('login');
                } elseif ($student['enrollment_status'] === 'rejected') {
                    // Student was rejected - logout and show message
                    $this->auth->logout();
                    session()->setFlashdata('error', 'Your enrollment application has been rejected. Please contact the administration for more information.');
                    return base_url('login');
                } else {
                    // Student has invalid status - logout and show message
                    $this->auth->logout();
                    session()->setFlashdata('error', 'Your account status is invalid. Please contact the administration.');
                    return base_url('login');
                }
            } elseif ($user->inGroup('parent')) {
                return base_url('parent/dashboard');
            }
        } catch (\Throwable $e) {
            // If there's any error getting user data, redirect to login
            return base_url('login');
        }

        return base_url('/');
    }

}