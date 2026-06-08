<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Enum\UserRole;
use AvitoAds\Model\User;

/**
 * Методы управления пользователями аккаунта.
 */
final class UserResource extends AbstractResource
{
    /**
     * Список пользователей аккаунта.
     *
     * GET /v1/account/{accountID}/users
     *
     * @return User[]
     */
    public function list(): array
    {
        $response = $this->http->request('GET', $this->accountPath('users'));
        $users = $response->getData()['users'] ?? [];

        return array_map(
            static function ($row): User {
                return User::fromArray(is_array($row) ? $row : []);
            },
            is_array($users) ? array_values($users) : []
        );
    }

    /**
     * Добавить пользователя в аккаунт.
     *
     * POST /v1/account/{accountID}/add-user
     *
     * @param string $role см. \AvitoAds\Enum\UserRole
     */
    public function add(int $userId, string $role): void
    {
        UserRole::assertValid($role);
        $this->http->request('POST', $this->accountPath('add-user'), [
            'userId' => $userId,
            'role' => $role,
        ]);
    }

    /**
     * Изменить роль пользователя.
     *
     * POST /v1/account/{accountID}/set-user-role
     *
     * @param string $role см. \AvitoAds\Enum\UserRole
     */
    public function setRole(int $userId, string $role): void
    {
        UserRole::assertValid($role);
        $this->http->request('POST', $this->accountPath('set-user-role'), [
            'userId' => $userId,
            'role' => $role,
        ]);
    }

    /**
     * Удалить пользователя из аккаунта.
     *
     * DELETE /v1/account/{accountID}/delete-user/{userID}
     */
    public function delete(int $userId): void
    {
        $this->http->request('DELETE', $this->accountPath('delete-user/' . $userId));
    }
}
