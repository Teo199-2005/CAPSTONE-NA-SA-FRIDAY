<?php

declare(strict_types=1);

if (! function_exists('student_ethnicity_options')) {
    /**
     * @return array<string, string> value => label
     */
    function student_ethnicity_options(): array
    {
        return [
            'Tagalog'                 => 'Tagalog',
            'Cebuano'                 => 'Cebuano / Bisaya',
            'Ilocano'                 => 'Ilocano',
            'Hiligaynon'              => 'Hiligaynon (Ilonggo)',
            'Bicolano'                => 'Bicolano',
            'Waray'                   => 'Waray',
            'Kapampangan'             => 'Kapampangan',
            'Pangasinense'            => 'Pangasinense',
            'Maranao'                 => 'Maranao / Maguindanaon / Iranun',
            'Lumad'                   => 'Lumad / other Indigenous',
            'Chinese_Filipino'        => 'Chinese Filipino',
            'Spanish_Filipino'        => 'Spanish Filipino / Latino Filipino',
            'Mixed'                   => 'Mixed ethnicity',
            'Foreign_national'        => 'Foreign national',
            'Prefer_not_to_say'       => 'Prefer not to say',
            'Other'                   => 'Other (describe in notes if needed)',
        ];
    }
}

if (! function_exists('ethnicity_option_label')) {
    function ethnicity_option_label(?string $code): string
    {
        if ($code === null || $code === '') {
            return '—';
        }
        $opts = student_ethnicity_options();

        return $opts[$code] ?? $code;
    }
}

if (! function_exists('nutrition_status_filter_options')) {
    /**
     * @return array<string, string>
     */
    function nutrition_status_filter_options(): array
    {
        return [
            ''                                              => 'All statuses',
            'incomplete'                                    => 'Missing height / weight / ethnicity',
            \App\Libraries\StudentNutritionClassifier::STATUS_SEVERELY_UNDERWEIGHT => 'Severely underweight',
            \App\Libraries\StudentNutritionClassifier::STATUS_UNDERWEIGHT          => 'Underweight',
            \App\Libraries\StudentNutritionClassifier::STATUS_NORMAL               => 'Normal',
            \App\Libraries\StudentNutritionClassifier::STATUS_OVERWEIGHT           => 'Overweight',
            \App\Libraries\StudentNutritionClassifier::STATUS_OBESE                => 'Obese',
            \App\Libraries\StudentNutritionClassifier::STATUS_UNKNOWN              => 'Unknown / incomplete',
        ];
    }
}
