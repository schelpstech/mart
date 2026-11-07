<?php

function qs_booking_now()
{
    return new DateTimeImmutable('now', new DateTimeZone('Europe/London'));
}

function qs_booking_csrf_token()
{
    if (empty($_SESSION['booking_csrf_token'])) $_SESSION['booking_csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['booking_csrf_token'];
}

function qs_booking_valid_csrf($token)
{
    return is_string($token) && isset($_SESSION['booking_csrf_token']) && hash_equals($_SESSION['booking_csrf_token'], $token);
}

function qs_booking_service_ids($values)
{
    if (!is_array($values) || !$values || count($values) > 64) throw new InvalidArgumentException('Choose at least one service from the list.');
    $ids = [];
    foreach ($values as $value) {
        if ((!is_string($value) && !is_int($value)) || !preg_match('/^[1-9][0-9]{0,8}$/D', (string)$value)) throw new InvalidArgumentException('Please choose valid services from the list.');
        $ids[] = (int)$value;
    }
    return array_values(array_unique($ids));
}

function qs_booking_appointment($date, $time, $now = null)
{
    if (!is_string($date) || !is_string($time) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $time)) {
        throw new InvalidArgumentException('Choose a preferred date and time for your appointment.');
    }
    $appointment = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $date . ' ' . $time, new DateTimeZone('Europe/London'));
    // Round-trip validation rejects impossible dates and times skipped by daylight-saving changes.
    if (!$appointment || $appointment->format('Y-m-d H:i') !== $date . ' ' . $time) throw new InvalidArgumentException('Please choose a valid appointment date and time.');
    if ($appointment <= ($now ?? qs_booking_now())) throw new InvalidArgumentException('Choose an appointment time in the future. Times are shown in UK time.');
    return ['appointment_date' => $date, 'appointment_time' => $time];
}

function qs_booking_duration_minutes($duration)
{
    if (preg_match('/^(\d{1,3}):([0-5]\d)$/D', trim((string)$duration), $match)) return (int)$match[1] * 60 + (int)$match[2];
    return 0;
}

function qs_booking_duration_label($minutes)
{
    $minutes = max(0, (int)$minutes);
    if (!$minutes) return 'Duration confirmed by the salon';
    $hours = intdiv($minutes, 60);
    $remainder = $minutes % 60;
    return trim(($hours ? $hours . ' hr' . ($hours === 1 ? '' : 's') : '') . ($remainder ? ' ' . $remainder . ' min' : ''));
}
