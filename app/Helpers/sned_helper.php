<?php

if (!function_exists('sned_quarters')) {
    /**
     * Returns the list of quarters used in SNED grading.
     * SNED uses Quarters 1-4 (independent from admin term setting).
     *
     * @return list<int>
     */
    function sned_quarters(): array
    {
        return [1, 2, 3, 4];
    }
}