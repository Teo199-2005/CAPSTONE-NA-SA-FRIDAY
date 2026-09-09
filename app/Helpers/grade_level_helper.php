<?php

if (!function_exists('grade_level_options')) {
    /**
     * Kindergarten (0) through Grade 6 — canonical grade-level list.
     * Grade 7 = SNED (Special Needs Education)
     * 99 = Custom (user-defined grade/class name)
     *
     * @return list<int>
     */
    function grade_level_options(): array
    {
        return [0, 1, 2, 3, 4, 5, 6, 7, 99];
    }
}

if (!function_exists('grade_level_min')) {
    function grade_level_min(): int
    {
        return 0;
    }
}

if (!function_exists('grade_level_max')) {
    function grade_level_max(): int
    {
        return 7;
    }
}

if (!function_exists('grade_level_label')) {
    function grade_level_label(int $gradeLevel, ?string $customName = null): string
    {
        if ($gradeLevel === 0) {
            return 'Kindergarten';
        }

        if ($gradeLevel === 7) {
            return 'SNED (Special Needs Education)';
        }

        if ($gradeLevel === 99) {
            return $customName ?: 'Custom';
        }

        return 'Grade ' . $gradeLevel;
    }
}

if (!function_exists('is_custom_grade_level')) {
    function is_custom_grade_level(?int $gradeLevel, ?string $gradingType = null): bool
    {
        if ($gradeLevel === 99) {
            return true;
        }
        if ($gradingType === 'custom') {
            return true;
        }
        return false;
    }
}

if (!function_exists('grade_level_in_list_rule')) {
    function grade_level_in_list_rule(): string
    {
        return 'in_list[0,1,2,3,4,5,6,7,99]';
    }
}

if (!function_exists('grade_level_chart_labels')) {
    /**
     * @return list<string>
     */
    function grade_level_chart_labels(): array
    {
        return array_map(
            static fn (int $g): string => grade_level_label($g),
            grade_level_options()
        );
    }
}

if (!function_exists('grade_level_announcement_role')) {
    function grade_level_announcement_role(int $gradeLevel): string
    {
        if ($gradeLevel === 99) {
            return 'custom';
        }
        return 'grade_' . $gradeLevel;
    }
}

if (!function_exists('is_graduating_grade')) {
    function is_graduating_grade(int $gradeLevel): bool
    {
        return $gradeLevel === grade_level_max();
    }
}

if (!function_exists('is_sned_grade')) {
    function is_sned_grade(int $gradeLevel): bool
    {
        return $gradeLevel === 7;
    }
}

if (!function_exists('grade_level_js_labels')) {
    /**
     * Map of grade level int => label for JavaScript.
     *
     * @return array<string, string>
     */
    function grade_level_js_labels(): array
    {
        $map = [];
        foreach (grade_level_options() as $g) {
            $map[(string) $g] = grade_level_label($g);
        }

        return $map;
    }
}

if (!function_exists('grade_level_display_name')) {
    /**
     * Get the display name for a section's grade level.
     * Handles custom sections with custom names.
     */
    function grade_level_display_name(array $section): string
    {
        $gradeLevel = (int) ($section['grade_level'] ?? 0);
        
        if ($gradeLevel === 99 || ($section['grading_type'] ?? 'numerical') === 'custom') {
            $customName = $section['grade_level_custom'] ?? null;
            return $customName ?: 'Custom';
        }
        
        return grade_level_label($gradeLevel);
    }
}

if (!function_exists('getSymbolBadgeClass')) {
    /**
     * Get Bootstrap badge class for grading symbols
     */
    function getSymbolBadgeClass(string $symbol): string
    {
        $symbolClasses = [
            'P' => 'success',
            'AP' => 'info',
            'D' => 'warning',
            'B' => 'danger',
            'NO/NA' => 'secondary'
        ];
        
        return $symbolClasses[$symbol] ?? 'primary';
    }
}
