<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * HTTP 401 — access token отсутствует, просрочен или невалиден.
 */
class AuthenticationException extends ApiException
{
}
