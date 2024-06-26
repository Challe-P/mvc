<?php

namespace App\Service;

/**
 * A class to implement some global functions, so that I can bypass them in unit tests
 */
class InterfaceHelper
{
    public function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    public function unlink(string $path): bool
    {
        return unlink($path);
    }

    public function exec(string $command, array &$output = null, int &$returnVar = null): void
    {
        exec($command, $output, $returnVar);
    }

    public function isString($var): bool
    {
        return is_string($var);
    }
}