<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Базовое исключение для ошибок, полученных от API Авито Реклама.
 *
 * Содержит код ошибки и сообщение из тела ответа (схема `{code, message}`),
 * HTTP-статус, а также исходные PSR-7 объекты запроса/ответа для отладки.
 */
class ApiException extends \RuntimeException implements AvitoAdsException
{
    /** @var string|null Машиночитаемый код ошибки из тела ответа. */
    private $errorCode;

    /** @var int HTTP-статус ответа. */
    private $statusCode;

    /** @var RequestInterface|null */
    private $request;

    /** @var ResponseInterface|null */
    private $response;

    public function __construct(
        string $message,
        int $statusCode = 0,
        ?string $errorCode = null,
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Машиночитаемый код ошибки из поля `code` тела ответа.
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * HTTP-статус ответа (400, 401, 403, 404, 429, 500, 503...).
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
