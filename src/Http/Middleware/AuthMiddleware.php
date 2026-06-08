<?php

declare(strict_types=1);

namespace AvitoAds\Http\Middleware;

use AvitoAds\Auth\TokenProviderInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Middleware, добавляющий заголовок `Authorization: Bearer <token>` в каждый запрос.
 *
 * Токен берётся из TokenProvider непосредственно перед отправкой, поэтому при
 * повторе запроса (после обновления токена на 401) в новую попытку попадает уже
 * свежий токен.
 */
final class AuthMiddleware
{
    /** @var TokenProviderInterface */
    private $tokenProvider;

    public function __construct(TokenProviderInterface $tokenProvider)
    {
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * @param callable $handler следующий обработчик в цепочке
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        $provider = $this->tokenProvider;

        return static function (RequestInterface $request, array $options) use ($handler, $provider) {
            $token = $provider->getToken();
            $request = $request->withHeader('Authorization', $token->getAuthorizationHeader());

            return $handler($request, $options);
        };
    }
}
