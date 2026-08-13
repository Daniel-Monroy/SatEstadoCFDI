<?php

declare(strict_types=1);

namespace DanielMonroy\SatEstadoCfdi\Support;

final class PrintedExpression
{
    /** @var list<string> */
    private const array REQUIRED_KEYS = ['id', 're', 'rr', 'tt'];

    /**
     * @return array<string, string>
     */
    public static function parse(string $expression): array
    {
        $queryString = self::extractQueryString($expression);

        if ($queryString === '') {
            return [];
        }

        parse_str($queryString, $parsed);

        $params = [];

        foreach ($parsed as $key => $value) {
            if (is_scalar($value)) {
                $params[(string) $key] = (string) $value;
            }
        }

        return $params;
    }

    /**
     * @return list<string>
     */
    public static function missingRequiredFields(string $expression): array
    {
        $params = self::parse($expression);

        return array_values(array_filter(
            self::REQUIRED_KEYS,
            static fn (string $key): bool => '' === ($params[$key] ?? '')
        ));
    }

    public static function extractQueryString(string $expression): string
    {
        $expression = trim($expression);

        if ($expression === '') {
            return '';
        }

        $query = parse_url($expression, PHP_URL_QUERY);

        if (is_string($query)) {
            return $query;
        }

        if (str_contains($expression, '?')) {
            $expression = substr($expression, strpos($expression, '?') + 1);
        }

        return ltrim($expression, '?');
    }
}
