<?php

declare(strict_types=1);

namespace AvitoAds\Auth;

/**
 * Access token, полученный по client credentials.
 *
 * Хранит само значение токена и абсолютное время истечения (Unix-timestamp),
 * чтобы клиент мог обновлять токен заранее, до фактического `401`.
 */
final class AccessToken
{
    /** @var string */
    private $token;

    /** @var int Unix-timestamp, когда токен перестаёт быть валидным. */
    private $expiresAt;

    /** @var string */
    private $tokenType;

    public function __construct(string $token, int $expiresAt, string $tokenType = 'Bearer')
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->tokenType = $tokenType;
    }

    /**
     * Создаёт токен из ответа эндпоинта /token.
     *
     * @param array{access_token?: string, expires_in?: int, token_type?: string} $payload
     */
    public static function fromResponse(array $payload, ?int $now = null): self
    {
        $now = $now ?? time();
        $expiresIn = isset($payload['expires_in']) ? (int) $payload['expires_in'] : 0;

        return new self(
            (string) ($payload['access_token'] ?? ''),
            $now + $expiresIn,
            (string) ($payload['token_type'] ?? 'Bearer')
        );
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    /**
     * Истёк ли токен с учётом запаса времени (в секундах).
     *
     * Запас (leeway) позволяет обновить токен заранее и не словить `401`
     * на границе срока жизни.
     */
    public function isExpired(int $leeway = 60, ?int $now = null): bool
    {
        $now = $now ?? time();

        return ($now + $leeway) >= $this->expiresAt;
    }

    /**
     * Готовое значение для заголовка Authorization.
     */
    public function getAuthorizationHeader(): string
    {
        return $this->tokenType . ' ' . $this->token;
    }

    /**
     * @return array{token: string, expires_at: int, token_type: string}
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'expires_at' => $this->expiresAt,
            'token_type' => $this->tokenType,
        ];
    }

    /**
     * @param array{token?: string, expires_at?: int, token_type?: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['token'] ?? ''),
            (int) ($data['expires_at'] ?? 0),
            (string) ($data['token_type'] ?? 'Bearer')
        );
    }
}
