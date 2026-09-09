<?php

if (!function_exists('get_current_school_year')) {
    function get_current_school_year(): string
    {
        try {
            $db = \Config\Database::connect();
            $setting = $db->table('system_settings')
                ->where('setting_key', 'current_school_year')
                ->get()
                ->getRowArray();
            
            if ($setting && !empty($setting['setting_value'])) {
                return $setting['setting_value'];
            }
        } catch (\Exception $e) {
            // Fall back to calculated year if database error
        }
        
        // Fallback: calculate based on current date
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');
        
        if ($currentMonth >= 6) {
            return $currentYear . '-' . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . '-' . $currentYear;
        }
    }
}

if (!function_exists('get_current_term')) {
    function get_current_term(): int
    {
        try {
            $db = \Config\Database::connect();
            $setting = $db->table('system_settings')
                ->where('setting_key', 'current_term')
                ->get()
                ->getRowArray();

            if ($setting && !empty($setting['setting_value'])) {
                return (int) $setting['setting_value'];
            }
        } catch (\Exception $e) {
            // Fall back to calculated term
        }

        // Fallback: calculate based on current month.
        // Elementary term layout: Jun-Sep = Term 1, Oct-Jan = Term 2, Feb-May = Term 3.
        $month = (int) date('n');
        if ($month >= 6 && $month <= 9) return 1;
        if ($month >= 10 || $month <= 1) return 2;
        return 3;
    }
}
