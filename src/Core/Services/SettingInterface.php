<?php

namespace App\Core\Services;

/**
 * SettingInterface
 * This interface defines the methods for managing application settings.
 * It allows loading settings from a file, retrieving
 * settings by key, setting values, checking existence,
 * and retrieving all settings.
 */
interface SettingInterface
{
    public function load(string $filePath): void;
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function has(string $key): bool;
    public function getAll(): array;
}
