<?php

declare(strict_types=1);

namespace AvitoAds\Auth;

use AvitoAds\Auth\Storage\InMemoryTokenStorage;
use AvitoAds\Auth\Storage\TokenStorageInterface;
use AvitoAds\Exception\AuthenticationException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Получение access token по схеме OAuth 2.0 client credentials.
 *
 * Делает POST на эндпоинт `/token`, кэширует токен в TokenStorage и обновляет
 * его заранее (с учётом leeway) до истечения срока жизни. Эндпоинт `/token`
 * находится в корне домена (`https://api.avito.ru/token`) и не зависит от
 * выбора прод/песочница.
 */
final class OAuthTokenProvider implements TokenProviderInterface
{
    /** @var ClientInterface Guzzle-клиент исключительно для запроса токена. */
    private $httpClient;

    /** @var Credentials */
    private $credentials;

    /** @var string */
    private $tokenUrl;

    /** @var TokenStorageInterface */
    private $storage;

    /** @var int Запас в секундах для досрочного обновления. */
    private $leeway;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ClientInterface $httpClient,
        Credentials $credentials,
        string $tokenUrl = 'https://api.avito.ru/token',
        ?TokenStorageInterface $storage = null,
        int $leeway = 60,
        ?LoggerInterface $logger = null
    ) {
        $this->httpClient = $httpClient;
        $this->credentials = $credentials;
        $this->tokenUrl = $tokenUrl;
        $this->storage = $storage ?? new InMemoryTokenStorage();
        $this->leeway = $leeway;
        $this->logger = $logger ?? new NullLogger();
    }

    public function getToken(): AccessToken
    {
        $key = $this->credentials->getCacheKey();
        $token = $this->storage->get($key);

        if ($token !== null && !$token->isExpired($this->leeway)) {
            return $token;
        }

        $token = $this->requestNewToken();
        $this->storage->set($key, $token);

        return $token;
    }

    public function forgetToken(): void
    {
        $this->storage->delete($this->credentials->getCacheKey());
    }

    /**
     * Запрашивает новый токен у эндпоинта /token.
     *
     * @throws AuthenticationException
     */
    private function requestNewToken(): AccessToken
    {
        $this->logger->debug('Avito Ads: запрос нового access token.');

        try {
            $response = $this->httpClient->request('POST', $this->tokenUrl, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->credentials->getClientId(),
                    'client_secret' => $this->credentials->getClientSecret(),
                ],
                'http_errors' => true,
            ]);
        } catch (GuzzleException $e) {
            throw new AuthenticationException(
                'Не удалось получить access token: ' . $e->getMessage(),
                (int) $e->getCode(),
                null,
                null,
                null,
                $e
            );
        }

        $body = (string) $response->getBody();
        /** @var array<string, mixed>|null $payload */
        $payload = json_decode($body, true);

        if (!is_array($payload)) {
            throw new AuthenticationException(
                'Эндпоинт /token вернул некорректный ответ: тело не является JSON-объектом.'
            );
        }

        $accessToken = $payload['access_token'] ?? null;
        if (!is_string($accessToken) || $accessToken === '') {
            throw new AuthenticationException(
                'Эндпоинт /token вернул некорректный ответ: отсутствует или пустой access_token.'
            );
        }

        $expiresIn = $payload['expires_in'] ?? null;
        if (!is_int($expiresIn) && !(is_string($expiresIn) && ctype_digit($expiresIn))) {
            throw new AuthenticationException(
                'Эндпоинт /token вернул некорректный ответ: отсутствует или некорректный expires_in.'
            );
        }
        if ((int) $expiresIn <= 0) {
            throw new AuthenticationException(
                'Эндпоинт /token вернул некорректный ответ: expires_in должен быть положительным.'
            );
        }

        if (isset($payload['token_type']) && (!is_string($payload['token_type']) || $payload['token_type'] === '')) {
            throw new AuthenticationException(
                'Эндпоинт /token вернул некорректный ответ: некорректный token_type.'
            );
        }

        return AccessToken::fromResponse($payload);
    }
}
