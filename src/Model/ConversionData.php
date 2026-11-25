<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Количество конверсий одного типа (контактов или подписок) с разбивкой по платформам.
 */
final class ConversionData extends AbstractModel
{
    public function getTotalCount(): int
    {
        return (int) $this->getInt('totalCount');
    }

    public function getWebCount(): int
    {
        return (int) $this->getInt('webCount');
    }

    public function getMavCount(): int
    {
        return (int) $this->getInt('mavCount');
    }

    public function getAndroidCount(): int
    {
        return (int) $this->getInt('androidCount');
    }

    public function getIosCount(): int
    {
        return (int) $this->getInt('iosCount');
    }

    /** CR: отношение событий к кликам (0.05 = 5%). */
    public function getCr(): float
    {
        return (float) $this->getFloat('cr');
    }
}
