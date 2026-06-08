<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Тип кампании.
 */
final class CampaignType extends AbstractEnum
{
    /** Текстово-графические объявления */
    public const TEXT_IMAGE = 'textImage';

    /** HTML-баннеры */
    public const HTML = 'HTML';

    /** Видеообъявления */
    public const VIDEO = 'video';
}
