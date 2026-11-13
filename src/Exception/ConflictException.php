<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * HTTP 409 — конфликт версий.
 *
 * Возвращается методами изменения бюджета и ставки группы (`change-budget`,
 * `change-price`), если группа изменилась параллельно. Перечитайте группу и
 * повторите изменение. Наследует ApiException, поэтому обработчики базового
 * класса продолжают ловить эту ошибку.
 */
class ConflictException extends ApiException
{
}
