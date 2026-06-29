<?php

if (!function_exists('qs_load_env')) {
    function qs_load_env($path)
    {
        if (!is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, 'export ') === 0) {
                $line = trim(substr($line, 7));
            }

            $separatorPosition = strpos($line, '=');

            if ($separatorPosition === false) {
                continue;
            }

            $key = trim(substr($line, 0, $separatorPosition));
            $value = trim(substr($line, $separatorPosition + 1));

            if ($key === '') {
                continue;
            }

            $valueLength = strlen($value);
            if ($valueLength >= 2) {
                $first = $value[0];
                $last = $value[$valueLength - 1];

                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }

            if (!array_key_exists($key, $_SERVER)) {
                $_SERVER[$key] = $value;
            }

            if (getenv($key) === false) {
                putenv($key . '=' . $value);
            }
        }
    }
}

if (!function_exists('env_value')) {
    function env_value($key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('env_bool')) {
    function env_bool($key, $default = false)
    {
        $value = env_value($key, null);

        if ($value === null) {
            return $default;
        }

        $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $parsed === null ? $default : $parsed;
    }
}

qs_load_env(dirname(__DIR__) . '/.env');
