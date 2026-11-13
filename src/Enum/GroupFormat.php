<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Формат группы объявлений (`Group::getFormat()`).
 *
 * Значения в ответах API не проверяются: неизвестное значение возвращается строкой как есть.
 */
final class GroupFormat extends AbstractEnum
{
    public const MEDIA_ROOT = 'mediaRoot';
    public const MEDIA_SELLER_PROFILE = 'mediaSellerProfile';
    public const MEDIA_PREMIUM = 'mediaPremium';
    public const MEDIA = 'media';
    public const NATIVE = 'native';
    public const HTML_PREMIUM = 'htmlPremium';
    public const HTML = 'html';
    public const VIDEO = 'video';
}
