<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * HTTP 403 — токен не имеет прав на указанный в пути `accountID`.
 */
class AccessDeniedException extends ApiException
{
}
