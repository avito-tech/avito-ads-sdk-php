<?php

declare(strict_types=1);

namespace AvitoAds\Exception;

/**
 * Ответ получен, но его тело не удалось разобрать как ожидаемый JSON-объект.
 *
 * Бросается для успешных (2xx) ответов с повреждённым, скалярным или не-JSON
 * телом — чтобы модели не строились из пустых данных.
 */
class InvalidResponseException extends ApiException
{
}
