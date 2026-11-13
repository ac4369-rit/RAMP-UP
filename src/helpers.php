<?php
declare(strict_types=1);

use App\Translator;

function env(string $key, ?string $default = null): ?string
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function t(string $key, array $params = []): string
{
    return Translator::instance()->trans($key, $params);
}

function app_timezone(): DateTimeZone
{
    try {
        return new DateTimeZone(env('APP_TIMEZONE', 'UTC') ?? 'UTC');
    } catch (Exception) {
        return new DateTimeZone('UTC');
    }
}

function format_date(string $date): string
{
    return Translator::instance()->formatDate($date);
}

function format_datetime(?string $value): string
{
    if ($value === null || $value === '') {
        return '';
    }
    try {
        $dt = new DateTimeImmutable($value, new DateTimeZone('UTC'));
    } catch (Exception) {
        return $value;
    }
    $f = new IntlDateFormatter(
        Translator::instance()->locale(),
        IntlDateFormatter::MEDIUM,
        IntlDateFormatter::SHORT,
        app_timezone()
    );
    return $f->format($dt) ?: $value;
}

function format_time(string $time): string
{
    $dt = DateTimeImmutable::createFromFormat('!H:i:s', $time, new DateTimeZone('UTC'))
        ?: DateTimeImmutable::createFromFormat('!H:i', $time, new DateTimeZone('UTC'));
    if ($dt === false) {
        return $time;
    }
    $f = new IntlDateFormatter(
        Translator::instance()->locale(),
        IntlDateFormatter::NONE,
        IntlDateFormatter::SHORT,
        'UTC'
    );
    return $f->format($dt) ?: $time;
}

function days_ago(?string $value): int
{
    if ($value === null || $value === '') {
        return 0;
    }
    try {
        $then = new DateTimeImmutable($value, new DateTimeZone('UTC'));
    } catch (Exception) {
        return 0;
    }
    $diff = $then->diff(new DateTimeImmutable('now', new DateTimeZone('UTC')));
    return $diff->invert ? 0 : (int) $diff->days;
}

function weekday_name(int $day): string
{
    if ($day < 1 || $day > 7) {
        return '';
    }

    $dt = new DateTimeImmutable(sprintf('2024-01-%02d', $day), new DateTimeZone('UTC'));
    $f  = new IntlDateFormatter(Translator::instance()->locale(), IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'UTC', null, 'EEEE');
    return $f->format($dt) ?: (string) $day;
}

function format_number(int|float $n, int $decimals = 0): string
{
    return Translator::instance()->formatNumber($n, $decimals);
}

function sort_by_name(array $rows): array
{
    $collator = new Collator(Translator::instance()->locale());
    usort($rows, fn (array $a, array $b) => $collator->compare($a['name'] ?? '', $b['name'] ?? ''));
    return $rows;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $sent = $_POST['_csrf'] ?? '';
    return is_string($sent) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

function flash_set(string $key, array $params = []): void
{
    $_SESSION['flash'] = ['key' => $key, 'params' => $params];
}

function flash_take(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function async_attrs(): string
{
    return sprintf(
        'data-async-region aria-live="polite" data-msg-loading="%s" data-msg-error="%s" data-msg-offline="%s" data-msg-retry="%s"',
        e(t('state.loading')),
        e(t('state.error')),
        e(t('state.offline')),
        e(t('state.retry'))
    );
}
