<?php

declare(strict_types=1);

namespace AvitoAds\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Унифицированный ответ API.
 *
 * Содержит декодированное тело ответа (как ассоциативный массив), HTTP-статус,
 * остаток API-баллов из заголовка `Api-Point-Balance` и исходный PSR-7 ответ.
 */
final class ApiResponse
{
    /** @var int */
    private $statusCode;

    /** @var array<string, mixed> */
    private $data;

    /** @var int|null Остаток API-баллов (заголовок Api-Point-Balance). */
    private $apiPointBalance;

    /** @var ResponseInterface */
    private $raw;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(int $statusCode, array $data, ?int $apiPointBalance, ResponseInterface $raw)
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->apiPointBalance = $apiPointBalance;
        $this->raw = $raw;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Декодированное тело ответа.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Остаток API-баллов после выполнения запроса, либо null если заголовок не пришёл.
     */
    public function getApiPointBalance(): ?int
    {
        return $this->apiPointBalance;
    }

    public function getRaw(): ResponseInterface
    {
        return $this->raw;
    }
}
