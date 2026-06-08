<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Пользователь рекламного аккаунта.
 */
final class User extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    /** Роль пользователя: см. \AvitoAds\Enum\UserRole. */
    public function getRole(): ?string
    {
        return $this->getString('role');
    }

    /** Заходил ли пользователь в кабинет хотя бы раз. */
    public function getHasLoggedIn(): ?bool
    {
        return $this->getBool('hasLoggedIn');
    }
}
