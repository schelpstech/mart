<?php

if (!function_exists('qs_admin_safe_identifier')) {
    function qs_admin_safe_identifier($identifier)
    {
        return preg_match('/^[A-Za-z0-9_]+$/', (string)$identifier) === 1;
    }
}

if (!function_exists('qs_admin_table_exists')) {
    function qs_admin_table_exists($model, $table)
    {
        if (!qs_admin_safe_identifier($table)) {
            return false;
        }

        try {
            $stmt = $model->prepare('SHOW TABLES LIKE :table_name');
            $stmt->execute([':table_name' => $table]);
            return (bool)$stmt->fetch(PDO::FETCH_NUM);
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('qs_admin_table_columns')) {
    function qs_admin_table_columns($model, $table)
    {
        if (!qs_admin_safe_identifier($table)) {
            return [];
        }

        try {
            $stmt = $model->prepare("DESCRIBE `{$table}`");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($row) {
                return $row['Field'];
            }, $rows ?: []);
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('qs_admin_pick_column')) {
    function qs_admin_pick_column(array $columns, array $candidates, $fallback = null)
    {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return $fallback;
    }
}

if (!function_exists('qs_admin_coalesce_select')) {
    function qs_admin_coalesce_select(array $columns, array $candidates, $alias, $fallback = "''")
    {
        if (!qs_admin_safe_identifier($alias)) {
            return $fallback . " AS value";
        }

        $parts = [];
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $columns, true) && qs_admin_safe_identifier($candidate)) {
                $parts[] = "NULLIF(`{$candidate}`, '')";
            }
        }

        $expression = !empty($parts)
            ? 'COALESCE(' . implode(', ', $parts) . ', ' . $fallback . ')'
            : $fallback;

        return $expression . " AS `{$alias}`";
    }
}

if (!function_exists('qs_admin_fetch_settings')) {
    function qs_admin_fetch_settings($model)
    {
        if (!qs_admin_table_exists($model, 'site_settings')) {
            return [];
        }

        try {
            $rows = $model->getRows('site_settings', ['return_type' => 'all']);
        } catch (Exception $e) {
            return [];
        }

        $settings = [];
        foreach ($rows ?: [] as $row) {
            if (isset($row['setting_key'])) {
                $settings[$row['setting_key']] = $row['setting_value'] ?? '';
            }
        }

        return $settings;
    }
}

if (!function_exists('qs_admin_setting')) {
    function qs_admin_setting(array $settings, $key, $default = '')
    {
        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }
}

if (!function_exists('qs_admin_format_date')) {
    function qs_admin_format_date($value)
    {
        if (empty($value)) {
            return '-';
        }

        $timestamp = strtotime($value);
        return $timestamp ? date('M d, Y H:i', $timestamp) : '-';
    }
}
