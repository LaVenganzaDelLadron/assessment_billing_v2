<?php

namespace App\Services;

class CustomIdGenerator
{
    /**
     * Characters used for ID generation
     */
    private const CHARSET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * Generate a custom formatted ID
     *
     * @param  string  $prefix  Program prefix (PRG, SUB, etc.)
     * @param  int  $externalId  External API ID to use as seed
     */
    public static function generate(string $prefix, int $externalId): string
    {
        // Use the external ID as a seed for consistent generation
        // This ensures the same external ID always generates the same custom ID
        $randomString = self::generateFromSeed($externalId);

        return "{$prefix}-{$randomString}";
    }

    /**
     * Generate consistent random string from external ID seed
     *
     * Uses the external ID to seed the generation, ensuring the same ID
     * always produces the same custom ID (idempotent)
     */
    private static function generateFromSeed(int $seed): string
    {
        // Create a seeded random generator
        mt_srand($seed);

        $length = 8; // 8-character random suffix
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= self::CHARSET[mt_rand(0, strlen(self::CHARSET) - 1)];
        }

        return $result;
    }

    /**
     * Validate custom ID format
     *
     * @param  string  $customId  Custom ID to validate
     */
    public static function isValid(string $customId): bool
    {
        return preg_match('/^[A-Z]{3}-[A-Z0-9]{8}$/', $customId) === 1;
    }

    /**
     * Extract prefix from custom ID
     */
    public static function extractPrefix(string $customId): ?string
    {
        if (preg_match('/^([A-Z]{3})-/', $customId, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
