<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Главная цель click-out кампании.
 */
final class CustomGoal extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    public function getName(): ?string
    {
        return $this->getString('name');
    }
}
