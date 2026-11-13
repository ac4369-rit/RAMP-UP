<?php
declare(strict_types=1);

namespace App;

use DateTimeInterface;
use IntlDateFormatter;
use NumberFormatter;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;


final class Translator
{
    
    public const SUPPORTED = ['en', 'hr', 'ja'];
    public const DEFAULT   = 'en';
    public const NAMES     = ['en' => 'English', 'hr' => 'Hrvatski', 'ja' => '日本語'];

    private const RESOURCE_DOMAIN = 'messages+intl-icu';
    private const DOMAIN          = 'messages';
    private const RTL             = ['ar', 'he', 'fa', 'ur'];

    private static ?self $instance = null;

    private SymfonyTranslator $inner;

    private function __construct(private readonly string $locale)
    {
        $this->inner = new SymfonyTranslator($locale);
        $this->inner->addLoader('php', new PhpFileLoader());
        $this->inner->setFallbackLocales([self::DEFAULT]);

        foreach (self::SUPPORTED as $code) {
            $file = dirname(__DIR__) . '/lang/' . $code . '.php';
            if (is_file($file)) {
                $this->inner->addResource('php', $file, $code, self::RESOURCE_DOMAIN);
            }
        }
    }

    public static function boot(string $locale): self
    {
        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }
        return self::$instance = new self($locale);
    }

    public static function instance(): self
    {
        return self::$instance ??= self::boot(self::DEFAULT);
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function direction(): string
    {
        return in_array($this->locale, self::RTL, true) ? 'rtl' : 'ltr';
    }

    /** @param array<string, scalar> $params nombres sin llaves: ['count' => 3] */
    public function trans(string $key, array $params = []): string
    {
        if (!$this->inner->getCatalogue($this->locale)->has($key, self::DOMAIN)) {
            
            return ($_ENV['APP_DEBUG'] ?? '') === 'true' ? '⚠ ' . $key : '';
        }
        return $this->inner->trans($key, $params, self::DOMAIN);
    }

    public function formatDate(DateTimeInterface|string $date, int $style = IntlDateFormatter::MEDIUM): string
    {
        $ts = $date instanceof DateTimeInterface ? $date->getTimestamp() : strtotime($date);
        if ($ts === false) {
            return (string) $date;
        }
        $f = new IntlDateFormatter($this->locale, $style, IntlDateFormatter::NONE);
        return $f->format($ts) ?: (string) $date;
    }

    public function formatNumber(int|float $number, int $decimals = 0): string
    {
        $f = new NumberFormatter($this->locale, NumberFormatter::DECIMAL);
        $f->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
        return $f->format($number) ?: (string) $number;
    }
}
