<?php

declare(strict_types=1);

namespace App\Libraries;

/**
 * WHO 2007 BMI-for-age (5–19 years) using LMS parameters from RCPCH WHO2007.csv (BMI columns).
 * Falls back to adult BMI categories outside the reference range.
 */
class StudentNutritionClassifier
{
    public const STATUS_SEVERELY_UNDERWEIGHT = 'severely_underweight';
    public const STATUS_UNDERWEIGHT          = 'underweight';
    public const STATUS_NORMAL               = 'normal';
    public const STATUS_OVERWEIGHT           = 'overweight';
    public const STATUS_OBESE                = 'obese';
    public const STATUS_UNKNOWN              = 'unknown';

    /** @var array<int, array{L:float,M:float,S:float}>|null */
    private static ?array $boysLms = null;

    /** @var array<int, array{L:float,M:float,S:float}>|null */
    private static ?array $girlsLms = null;

    public static function computeBmi(?float $heightCm, ?float $weightKg): ?float
    {
        if ($heightCm === null || $weightKg === null || $heightCm <= 0 || $weightKg <= 0) {
            return null;
        }
        $hM = $heightCm / 100.0;

        return round($weightKg / ($hM * $hM), 2);
    }

    /**
     * @return array{bmi: ?float, nutrition_status: string, z_score: ?float}
     */
    public static function classify(
        ?float $heightCm,
        ?float $weightKg,
        ?string $gender,
        ?string $dateOfBirthYmd
    ): array {
        $bmi = self::computeBmi($heightCm, $weightKg);
        if ($bmi === null) {
            return ['bmi' => null, 'nutrition_status' => self::STATUS_UNKNOWN, 'z_score' => null];
        }

        if ($gender !== 'Male' && $gender !== 'Female') {
            return ['bmi' => $bmi, 'nutrition_status' => self::adultCategoryFromBmi($bmi), 'z_score' => null];
        }

        $ageMonths = self::ageInMonths($dateOfBirthYmd);
        if ($ageMonths === null) {
            return ['bmi' => $bmi, 'nutrition_status' => self::adultCategoryFromBmi($bmi), 'z_score' => null];
        }

        if ($ageMonths < 61 || $ageMonths > 228) {
            return ['bmi' => $bmi, 'nutrition_status' => self::adultCategoryFromBmi($bmi), 'z_score' => null];
        }

        $lms = self::interpolatedLms($gender === 'Male' ? 'boys' : 'girls', $ageMonths);
        if ($lms === null) {
            return ['bmi' => $bmi, 'nutrition_status' => self::adultCategoryFromBmi($bmi), 'z_score' => null];
        }

        $z = self::bmiToZScore($bmi, $lms['L'], $lms['M'], $lms['S']);
        $status = self::statusFromZScore($z);

        return ['bmi' => $bmi, 'nutrition_status' => $status, 'z_score' => $z];
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_SEVERELY_UNDERWEIGHT => 'Severely underweight',
            self::STATUS_UNDERWEIGHT          => 'Underweight',
            self::STATUS_NORMAL               => 'Normal',
            self::STATUS_OVERWEIGHT           => 'Overweight',
            self::STATUS_OBESE                => 'Obese',
            default                           => 'Unknown',
        };
    }

    private static function adultCategoryFromBmi(float $bmi): string
    {
        if ($bmi < 18.5) {
            return self::STATUS_UNDERWEIGHT;
        }
        if ($bmi < 25.0) {
            return self::STATUS_NORMAL;
        }
        if ($bmi < 30.0) {
            return self::STATUS_OVERWEIGHT;
        }

        return self::STATUS_OBESE;
    }

    private static function statusFromZScore(float $z): string
    {
        if ($z < -3.0) {
            return self::STATUS_SEVERELY_UNDERWEIGHT;
        }
        if ($z < -2.0) {
            return self::STATUS_UNDERWEIGHT;
        }
        if ($z <= 1.0) {
            return self::STATUS_NORMAL;
        }
        if ($z <= 2.0) {
            return self::STATUS_OVERWEIGHT;
        }

        return self::STATUS_OBESE;
    }

    private static function bmiToZScore(float $bmi, float $L, float $M, float $S): float
    {
        if ($M <= 0 || $S <= 0) {
            return 0.0;
        }
        if (abs($L) < 1e-12) {
            return log($bmi / $M) / $S;
        }

        return (pow($bmi / $M, $L) - 1.0) / ($L * $S);
    }

    private static function ageInMonths(?string $dateOfBirthYmd): ?float
    {
        if ($dateOfBirthYmd === null || $dateOfBirthYmd === '') {
            return null;
        }
        try {
            $dob = new \DateTimeImmutable($dateOfBirthYmd);
        } catch (\Exception) {
            return null;
        }
        $now = new \DateTimeImmutable('today');
        if ($dob > $now) {
            return null;
        }
        $diff = $dob->diff($now);

        return ($diff->y * 12) + $diff->m + ($diff->d / 30.4375);
    }

    /**
     * @return ?array{L:float,M:float,S:float}
     */
    private static function interpolatedLms(string $sexKey, float $ageMonths): ?array
    {
        self::ensureLmsLoaded();
        $table = $sexKey === 'boys' ? self::$boysLms : self::$girlsLms;
        if ($table === null || $table === []) {
            return null;
        }

        $mLow = (int) floor($ageMonths);
        $mHigh = (int) ceil($ageMonths);
        if ($mLow < 61) {
            $mLow = 61;
        }
        if ($mHigh > 228) {
            $mHigh = 228;
        }
        if (! isset($table[$mLow], $table[$mHigh])) {
            return $table[(int) round($ageMonths)] ?? null;
        }
        if ($mLow === $mHigh) {
            return $table[$mLow];
        }
        $t = $ageMonths - $mLow;
        $a = $table[$mLow];
        $b = $table[$mHigh];

        return [
            'L' => $a['L'] + $t * ($b['L'] - $a['L']),
            'M' => $a['M'] + $t * ($b['M'] - $a['M']),
            'S' => $a['S'] + $t * ($b['S'] - $a['S']),
        ];
    }

    private static function ensureLmsLoaded(): void
    {
        if (self::$boysLms !== null) {
            return;
        }

        $path = APPPATH . 'Config/Data/who2007.csv';
        self::$boysLms  = [];
        self::$girlsLms = [];

        if (! is_file($path)) {
            return;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return;
        }

        $lineNo = 0;
        $prevBoys  = ['L' => 0.0, 'M' => 0.0, 'S' => 0.0];
        $prevGirls = ['L' => 0.0, 'M' => 0.0, 'S' => 0.0];

        while (($line = fgets($handle)) !== false) {
            ++$lineNo;
            if ($lineNo < 6) {
                continue;
            }
            $row = str_getcsv(trim($line));
            if (count($row) < 20) {
                continue;
            }
            $month = (int) $row[0];
            if ($month < 61 || $month > 228) {
                continue;
            }

            $bL = self::parseFloatOrNull($row[14] ?? '');
            $bM = self::parseFloatOrNull($row[15] ?? '');
            $bS = self::parseFloatOrNull($row[16] ?? '');
            $gL = self::parseFloatOrNull($row[17] ?? '');
            $gM = self::parseFloatOrNull($row[18] ?? '');
            $gS = self::parseFloatOrNull($row[19] ?? '');

            if ($bL !== null && $bM !== null && $bS !== null) {
                $prevBoys = ['L' => $bL, 'M' => $bM, 'S' => $bS];
            } else {
                $bL = $prevBoys['L'];
                $bM = $prevBoys['M'];
                $bS = $prevBoys['S'];
            }
            if ($gL !== null && $gM !== null && $gS !== null) {
                $prevGirls = ['L' => $gL, 'M' => $gM, 'S' => $gS];
            } else {
                $gL = $prevGirls['L'];
                $gM = $prevGirls['M'];
                $gS = $prevGirls['S'];
            }

            self::$boysLms[$month]  = ['L' => $bL, 'M' => $bM, 'S' => $bS];
            self::$girlsLms[$month] = ['L' => $gL, 'M' => $gM, 'S' => $gS];
        }
        fclose($handle);
    }

    private static function parseFloatOrNull(string $v): ?float
    {
        $v = trim($v);
        if ($v === '') {
            return null;
        }

        return (float) $v;
    }
}
