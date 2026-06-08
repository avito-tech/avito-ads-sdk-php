<?php

declare(strict_types=1);

namespace AvitoAds\Http;

use AvitoAds\Exception\AccessDeniedException;
use AvitoAds\Exception\ApiException;
use AvitoAds\Exception\AuthenticationException;
use AvitoAds\Exception\BadRequestException;
use AvitoAds\Exception\InvalidResponseException;
use AvitoAds\Exception\NetworkException;
use AvitoAds\Exception\NotFoundException;
use AvitoAds\Exception\RateLimitException;
use AvitoAds\Exception\ServerException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Реализация транспортного слоя поверх Guzzle.
 *
 * Отправляет запросы относительно базового URL, разбирает JSON-ответ, читает
 * заголовок `Api-Point-Balance` и преобразует HTTP-ошибки в типизированные
 * исключения библиотеки. Авторизация и повторы навешиваются через middleware
 * при сборке Guzzle-клиента (см. ClientBuilder).
 */
final class GuzzleHttpClient implements HttpClientInterface
{
    /** @var ClientInterface */
    private $guzzle;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ClientInterface $guzzle, ?LoggerInterface $logger = null)
    {
        $this->guzzle = $guzzle;
        $this->logger = $logger ?? new NullLogger();
    }

    public function request(string $method, string $path, ?array $body = null): ApiResponse
    {
        $options = [
            // Ошибки HTTP обрабатываем сами, чтобы вернуть типизированные исключения.
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        if ($body !== null) {
            $options['json'] = $body;
        }

        try {
            $response = $this->guzzle->request($method, $path, $options);
        } catch (ConnectException $e) {
            throw new NetworkException(
                'Сетевая ошибка при обращении к API Авито Реклама: ' . $e->getMessage(),
                0,
                null,
                $e->getRequest(),
                null,
                $e
            );
        } catch (GuzzleException $e) {
            throw new NetworkException(
                'Ошибка транспорта при обращении к API Авито Реклама: ' . $e->getMessage(),
                (int) $e->getCode(),
                null,
                null,
                null,
                $e
            );
        }

        return $this->handleResponse($response, $method, $path);
    }

    private function handleResponse(ResponseInterface $response, string $method, string $path): ApiResponse
    {
        $status = $response->getStatusCode();
        $rawBody = (string) $response->getBody();
        $isSuccess = $status >= 200 && $status < 300;
        $data = $this->decodeBody($rawBody, $isSuccess, $response, $method, $path);
        $apiPointBalance = $this->extractApiPointBalance($response);

        if ($isSuccess) {
            return new ApiResponse($status, $data, $apiPointBalance, $response);
        }

        throw $this->buildException($status, $data, $response, $method, $path);
    }

    /**
     * Разбирает тело ответа в JSON-объект.
     *
     * Пустое тело трактуется как `[]` (например, 204 No Content). Для успешных
     * ответов повреждённый, скалярный или не-JSON ответ приводит к
     * InvalidResponseException, чтобы модели не строились из пустых данных. Для
     * ошибочных статусов битое тело допускается (возвращается `[]`), чтобы не
     * маскировать настоящий HTTP-статус ошибки.
     *
     * @return array<string, mixed>
     */
    private function decodeBody(
        string $rawBody,
        bool $isSuccess,
        ResponseInterface $response,
        string $method,
        string $path
    ): array {
        if ($rawBody === '') {
            return [];
        }

        try {
            $decoded = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            if ($isSuccess) {
                throw new InvalidResponseException(
                    sprintf('Не удалось разобрать JSON-ответ %s %s: %s', $method, $path, $e->getMessage()),
                    0,
                    null,
                    null,
                    $response,
                    $e
                );
            }

            return [];
        }

        if (!is_array($decoded)) {
            if ($isSuccess) {
                throw new InvalidResponseException(
                    sprintf('Ответ %s %s не является JSON-объектом.', $method, $path),
                    0,
                    null,
                    null,
                    $response
                );
            }

            return [];
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    private function extractApiPointBalance(ResponseInterface $response): ?int
    {
        $header = $response->getHeaderLine('Api-Point-Balance');

        return ($header !== '' && is_numeric($header)) ? (int) $header : null;
    }

    /**
     * Сопоставляет HTTP-статус с типизированным исключением библиотеки.
     *
     * @param array<string, mixed> $data
     */
    private function buildException(
        int $status,
        array $data,
        ResponseInterface $response,
        string $method,
        string $path
    ): ApiException {
        $code = isset($data['code']) ? (string) $data['code'] : null;
        $message = isset($data['message']) && $data['message'] !== ''
            ? (string) $data['message']
            : sprintf('Запрос %s %s завершился с HTTP %d.', $method, $path, $status);

        $this->logger->error(sprintf('Avito Ads: ошибка API HTTP %d — %s', $status, $message));

        switch (true) {
            case $status === 400:
                return new BadRequestException($message, $status, $code, null, $response);
            case $status === 401:
                return new AuthenticationException($message, $status, $code, null, $response);
            case $status === 403:
                return new AccessDeniedException($message, $status, $code, null, $response);
            case $status === 404:
                return new NotFoundException($message, $status, $code, null, $response);
            case $status === 429:
                return new RateLimitException($message, $status, $code, null, $response);
            case $status >= 500:
                return new ServerException($message, $status, $code, null, $response);
            default:
                return new ApiException($message, $status, $code, null, $response);
        }
    }
}
