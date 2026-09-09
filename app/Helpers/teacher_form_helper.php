<?php

declare(strict_types=1);

if (! function_exists('teacher_month_options')) {
    /**
     * @return array<int, string>
     */
    function teacher_month_options(): array
    {
        return [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }
}

if (! function_exists('teacher_fund_source_options')) {
    /**
     * @return list<string>
     */
    function teacher_fund_source_options(): array
    {
        return ['National', 'Local', 'Special Education Fund', 'Other'];
    }
}

if (! function_exists('teacher_position_options')) {
    /**
     * @return list<string>
     */
    function teacher_position_options(): array
    {
        return [
            'Teacher I',
            'Teacher II',
            'Teacher III',
            'Master Teacher I',
            'Master Teacher II',
            'Head Teacher I',
            'Head Teacher II',
            'Head Teacher III',
            'Head Teacher IV',
            'Head Teacher V',
            'Head Teacher VI',
            'Principal I',
            'Principal II',
            'Principal III',
            'Principal IV',
            'Assistant Principal',
            'Department Head',
            'Teacher',
        ];
    }
}

if (! function_exists('teacher_designation_options')) {
    /**
     * @return list<string>
     */
    function teacher_designation_options(): array
    {
        return [
            'Regular Permanent',
            'Regular Temporary',
            'Contractual',
            'Substitute',
            'Part-time',
            'Provisional',
        ];
    }
}

if (! function_exists('teacher_nature_of_appointment_options')) {
    /**
     * @return list<string>
     */
    function teacher_nature_of_appointment_options(): array
    {
        return [
            'Original',
            'Promotion',
            'Transfer',
            'Reappointment',
            'Reemployment',
            'Reinstatement',
        ];
    }
}

if (! function_exists('teacher_hiring_arrangement_options')) {
    /**
     * @return list<string>
     */
    function teacher_hiring_arrangement_options(): array
    {
        return ['Regular', 'Contractual', 'Part-time', 'Substitute', 'Provisional'];
    }
}

if (! function_exists('teacher_civil_status_options')) {
    /**
     * @return list<string>
     */
    function teacher_civil_status_options(): array
    {
        return ['Single', 'Married', 'Widowed', 'Separated', 'Annulled', 'Divorced'];
    }
}

if (! function_exists('teacher_eligibility_options')) {
    /**
     * @return list<string>
     */
    function teacher_eligibility_options(): array
    {
        return [
            'Licensure Examination for Teachers (LET)',
            'Philippine Board Examination for Teachers (PBET)',
            'Civil Service Professional',
            'Civil Service Subprofessional',
            'Bar/Board Eligibility',
            'Other',
        ];
    }
}

if (! function_exists('teacher_item_status_options')) {
    /**
     * @return list<string>
     */
    function teacher_item_status_options(): array
    {
        return ['Own Station', 'Detached', 'On Detail', 'On Leave', 'Other'];
    }
}

if (! function_exists('teacher_subject_options')) {
    /**
     * @return list<string>
     */
    function teacher_subject_options(): array
    {
        return [
            'Mathematics',
            'Science',
            'English',
            'Filipino',
            'Araling Panlipunan',
            'MAPEH',
            'Values Education',
            'TLE',
            'TLE FSC',
            'TLE/BPP',
            'Elementary General',
        ];
    }
}

if (! function_exists('teacher_format_tin')) {
    function teacher_format_tin(?string $tin): ?string
    {
        if ($tin === null || $tin === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $tin);
        if ($digits === null || strlen($digits) !== 9) {
            return trim($tin);
        }

        return substr($digits, 0, 3) . '-' . substr($digits, 3, 3) . '-' . substr($digits, 6, 3);
    }
}

if (! function_exists('teacher_normalize_philsys')) {
    function teacher_normalize_philsys(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);
        if ($digits === null || strlen($digits) !== 12) {
            return null;
        }

        return $digits;
    }
}

if (! function_exists('teacher_parse_date_parts')) {
    function teacher_parse_date_parts($month, $day, $year): ?string
    {
        $month = (int) $month;
        $day   = (int) $day;
        $year  = (int) $year;

        if ($month < 1 || $month > 12 || $day < 1 || $day > 31 || $year < 1900 || $year > (int) date('Y')) {
            return null;
        }

        if (! checkdate($month, $day, $year)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}

if (! function_exists('teacher_date_parts_from_value')) {
    /**
     * @return array{month: int|null, day: int|null, year: int|null}
     */
    function teacher_date_parts_from_value(?string $date): array
    {
        if (empty($date)) {
            return ['month' => null, 'day' => null, 'year' => null];
        }

        try {
            $dt = new DateTime($date);

            return [
                'month' => (int) $dt->format('n'),
                'day'   => (int) $dt->format('j'),
                'year'  => (int) $dt->format('Y'),
            ];
        } catch (\Throwable $e) {
            return ['month' => null, 'day' => null, 'year' => null];
        }
    }
}

if (! function_exists('teacher_in_list_rule')) {
    function teacher_in_list_rule(array $options): string
    {
        return 'permit_empty|in_list[' . implode(',', $options) . ']';
    }
}

if (! function_exists('teacher_store_validation_rules')) {
    /**
     * @return array<string, string>
     */
    function teacher_store_validation_rules(bool $isCreate = true): array
    {
        $rules = [
            'first_name'           => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-Z\s\-\']+$/]',
            'last_name'            => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-Z\s\-\']+$/]',
            'employment_status'    => 'required|in_list[active,inactive,on_leave,resigned,terminated]',
            // Optional fields with basic validation
            'middle_name'          => 'permit_empty|max_length[100]|regex_match[/^[a-zA-Z\s\-\']*$/]',
            'gender'               => 'permit_empty|in_list[Male,Female]',
            'birth_month'          => 'permit_empty|integer|greater_than[0]|less_than[13]',
            'birth_day'            => 'permit_empty|integer|greater_than[0]|less_than[32]',
            'birth_year'           => 'permit_empty|integer|greater_than[1900]|less_than_equal_to[' . date('Y') . ']',
            'license_number'       => 'permit_empty|exact_length[7]|numeric',
            'tin'                  => 'permit_empty|regex_match[/^\d{3}-\d{3}-\d{3}$/]',
            'personnel_category'   => 'permit_empty|max_length[10]|alpha_numeric_space',
            'fund_source'          => teacher_in_list_rule(teacher_fund_source_options()),
            'position'             => teacher_in_list_rule(teacher_position_options()),
            'designation'          => teacher_in_list_rule(teacher_designation_options()),
            'nature_of_appointment'=> teacher_in_list_rule(teacher_nature_of_appointment_options()),
            'baccalaureate_degree' => 'permit_empty|max_length[255]',
            'prc_specialization'   => 'permit_empty|max_length[100]',
            'prc_major_units_percent' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'minor'                => 'permit_empty|max_length[100]',
            'masters_degree'       => 'permit_empty|max_length[255]',
            'government_employee_no' => 'permit_empty|max_length[20]|numeric',
            'hiring_arrangement'   => teacher_in_list_rule(teacher_hiring_arrangement_options()),
            'religion'             => 'permit_empty|max_length[50]',
            'ethnic_group'         => 'permit_empty|max_length[50]',
            'item_status'          => teacher_in_list_rule(teacher_item_status_options()),
            'civil_status'         => teacher_in_list_rule(teacher_civil_status_options()),
            'philsys_number'       => 'permit_empty|exact_length[12]|numeric',
            'eligibility'          => teacher_in_list_rule(teacher_eligibility_options()),
            'service_month'        => 'permit_empty|greater_than_equal_to[0]|less_than[13]',
            'service_day'          => 'permit_empty|greater_than_equal_to[0]|less_than[32]',
            'service_year'         => 'permit_empty|greater_than_equal_to[0]|less_than_equal_to[' . date('Y') . ']',
            'new_station_month'    => 'permit_empty|greater_than_equal_to[0]|less_than[13]',
            'new_station_day'      => 'permit_empty|greater_than_equal_to[0]|less_than[32]',
            'new_station_year'     => 'permit_empty|greater_than_equal_to[0]|less_than_equal_to[' . date('Y') . ']',
            'contact_number'       => 'permit_empty|max_length[20]',
            'address'              => 'permit_empty|max_length[500]',
            'subjects'             => teacher_in_list_rule(teacher_subject_options()),
            'date_hired'           => 'permit_empty|valid_date',
        ];

        if ($isCreate) {
            $rules['email']    = 'required|valid_email|is_unique[users.email]';
            $rules['password'] = 'required|min_length[8]';
        } else {
            $rules['email'] = 'permit_empty|valid_email';
        }

        return $rules;
    }
}

if (! function_exists('teacher_collect_personnel_from_request')) {
    /**
     * @return array<string, mixed>
     */
    function teacher_collect_personnel_from_request(\CodeIgniter\HTTP\IncomingRequest $request): array
    {
        $birthDate = teacher_parse_date_parts(
            $request->getPost('birth_month'),
            $request->getPost('birth_day'),
            $request->getPost('birth_year')
        );

        $serviceDate = teacher_parse_date_parts(
            $request->getPost('service_month'),
            $request->getPost('service_day'),
            $request->getPost('service_year')
        );

        $newStationDate = teacher_parse_date_parts(
            $request->getPost('new_station_month'),
            $request->getPost('new_station_day'),
            $request->getPost('new_station_year')
        );

        $tin = teacher_format_tin($request->getPost('tin'));
        $philsys = teacher_normalize_philsys($request->getPost('philsys_number'));

        $percent = $request->getPost('prc_major_units_percent');
        $percent = ($percent === null || $percent === '') ? null : (float) $percent;

        $dateHired = $request->getPost('date_hired');
        if (empty($dateHired) && $serviceDate) {
            $dateHired = $serviceDate;
        }

        return [
            'license_number'              => $request->getPost('license_number') ?: null,
            'tin'                         => $tin,
            'personnel_category'          => $request->getPost('personnel_category') ?: null,
            'first_name'                  => trim((string) $request->getPost('first_name')),
            'middle_name'                 => trim((string) $request->getPost('middle_name')) ?: null,
            'last_name'                   => trim((string) $request->getPost('last_name')),
            'gender'                      => $request->getPost('gender'),
            'date_of_birth'               => $birthDate,
            'fund_source'                 => $request->getPost('fund_source') ?: null,
            'position'                    => $request->getPost('position') ?: null,
            'designation'                 => $request->getPost('designation') ?: null,
            'nature_of_appointment'       => $request->getPost('nature_of_appointment') ?: null,
            'baccalaureate_degree'        => trim((string) $request->getPost('baccalaureate_degree')) ?: null,
            'prc_specialization'          => trim((string) $request->getPost('prc_specialization')) ?: null,
            'prc_major_units_percent'     => $percent,
            'minor'                       => trim((string) $request->getPost('minor')) ?: null,
            'masters_degree'              => trim((string) $request->getPost('masters_degree')) ?: null,
            'government_employee_no'      => $request->getPost('government_employee_no') ?: null,
            'hiring_arrangement'          => $request->getPost('hiring_arrangement') ?: null,
            'religion'                    => trim((string) $request->getPost('religion')) ?: null,
            'ethnic_group'                => trim((string) $request->getPost('ethnic_group')) ?: null,
            'item_status'                 => $request->getPost('item_status') ?: null,
            'civil_status'                => $request->getPost('civil_status') ?: null,
            'philsys_number'              => $philsys,
            'eligibility'                 => $request->getPost('eligibility') ?: null,
            'date_first_service'          => $serviceDate,
            'date_first_service_new_station' => $newStationDate,
            'contact_number'              => $request->getPost('contact_number') ?: null,
            'address'                     => $request->getPost('address') ?: null,
            'department'                  => $request->getPost('subjects') ?: null,
            'specialization'              => $request->getPost('prc_specialization') ?: ($request->getPost('subjects') ?: null),
            'date_hired'                  => $dateHired,
            'employment_status'           => $request->getPost('employment_status'),
        ];
    }
}

if (! function_exists('teacher_format_date_display')) {
    function teacher_format_date_display(?string $date): string
    {
        if (empty($date)) {
            return '—';
        }

        try {
            return (new DateTime($date))->format('F j, Y');
        } catch (\Throwable $e) {
            return '—';
        }
    }
}

if (! function_exists('teacher_detail_value')) {
    function teacher_detail_value(?string $value, string $empty = 'Not specified'): string
    {
        if ($value === null || $value === '') {
            return '<span class="teacher-info-value empty">' . esc($empty) . '</span>';
        }

        return '<span class="teacher-info-value">' . esc($value) . '</span>';
    }
}
