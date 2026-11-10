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

function format_date(string $date): string
{
    return Translator::instance()->formatDate($date);
}

function format_number(int|float $n, int $decimals = 0): string
{
    return Translator::instance()->formatNumber($n, $decimals);
}
