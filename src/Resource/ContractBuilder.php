<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Enum\ContractAction;
use AvitoAds\Enum\ContractCounterpartyType;
use AvitoAds\Enum\ContractSubject;
use AvitoAds\Enum\ContractType;
use AvitoAds\Exception\ValidationException;

/**
 * Конструктор тела запроса для создания договора.
 *
 * Инкапсулирует правила обязательности полей по типу договора и проверяет их
 * до отправки, чтобы вместо ответа 400 от API вернуть понятную ошибку валидации.
 *
 * Правила по типу:
 *  - service:      обязательны subject, isReportingRequired, date, number; запрещён cid.
 *  - intermediary: обязательны subject, object, isReportingRequired,
 *                  isFundsAllocationToPrincipal, date, number; запрещён cid.
 *  - external:     обязателен cid; запрещён parentId; date/number необязательны.
 *
 * Поле intermediary (данные исполнителя) обязательно, если не задан parentId,
 * и недопустимо, если parentId задан (доп. соглашение).
 */
final class ContractBuilder
{
    /** @var array<string, mixed> */
    private $data = [];

    public static function create(): self
    {
        return new self();
    }

    /**
     * Быстрый старт для договора на оказание услуг.
     */
    public static function service(): self
    {
        return (new self())->type(ContractType::SERVICE);
    }

    /**
     * Быстрый старт для посреднического договора.
     */
    public static function intermediaryContract(): self
    {
        return (new self())->type(ContractType::INTERMEDIARY);
    }

    /**
     * Быстрый старт для внешнего договора.
     */
    public static function external(string $cid): self
    {
        return (new self())->type(ContractType::EXTERNAL)->cid($cid);
    }

    public function advertiser(int $advertiserId): self
    {
        $this->data['advertiserId'] = $advertiserId;

        return $this;
    }

    /**
     * @param string $type см. \AvitoAds\Enum\ContractType
     */
    public function type(string $type): self
    {
        ContractType::assertValid($type);
        $this->data['type'] = $type;

        return $this;
    }

    /**
     * Тип контрагента (поле API `description`).
     *
     * @param string $counterpartyType см. \AvitoAds\Enum\ContractCounterpartyType
     */
    public function counterpartyType(string $counterpartyType): self
    {
        ContractCounterpartyType::assertValid($counterpartyType);
        $this->data['description'] = $counterpartyType;

        return $this;
    }

    /**
     * @param string $subject см. \AvitoAds\Enum\ContractSubject
     */
    public function subject(string $subject): self
    {
        ContractSubject::assertValid($subject);
        $this->data['subject'] = $subject;

        return $this;
    }

    /**
     * @param string $object см. \AvitoAds\Enum\ContractAction
     */
    public function object(string $object): self
    {
        ContractAction::assertValid($object);
        $this->data['object'] = $object;

        return $this;
    }

    public function cid(string $cid): self
    {
        $this->data['cid'] = $cid;

        return $this;
    }

    /**
     * @param string $date дата в формате YYYY-MM-DD
     */
    public function date(string $date): self
    {
        $this->data['date'] = $date;

        return $this;
    }

    public function number(string $number): self
    {
        $this->data['number'] = $number;

        return $this;
    }

    public function reportingRequired(bool $value): self
    {
        $this->data['isReportingRequired'] = $value;

        return $this;
    }

    public function fundsAllocationToPrincipal(bool $value): self
    {
        $this->data['isFundsAllocationToPrincipal'] = $value;

        return $this;
    }

    public function parentId(int $parentId): self
    {
        $this->data['parentId'] = $parentId;

        return $this;
    }

    /**
     * Данные исполнителя (посредника).
     *
     * @param array<string, mixed> $intermediary
     */
    public function intermediary(array $intermediary): self
    {
        $this->data['intermediary'] = $intermediary;

        return $this;
    }

    /**
     * Проверяет и собирает тело запроса.
     *
     * @return array<string, mixed>
     * @throws ValidationException при нарушении правил по типу договора
     */
    public function build(): array
    {
        $this->require('advertiserId', 'Не задан advertiserId.');
        $this->require('type', 'Не задан type договора.');
        $this->require('description', 'Не задан тип контрагента (counterpartyType).');

        $type = $this->data['type'];
        $hasParent = isset($this->data['parentId']);

        if ($type === ContractType::EXTERNAL) {
            $this->require('cid', 'Для внешнего договора (external) обязателен cid.');
            if ($hasParent) {
                throw new ValidationException('Для внешнего договора (external) нельзя передавать parentId.');
            }
        }

        if ($type === ContractType::SERVICE || $type === ContractType::INTERMEDIARY) {
            $this->require('subject', sprintf('Для договора типа %s обязателен subject.', $type));
            $this->require(
                'isReportingRequired',
                sprintf('Для договора типа %s обязателен isReportingRequired.', $type)
            );
            $this->require('date', sprintf('Для договора типа %s обязательны date и number.', $type));
            $this->require('number', sprintf('Для договора типа %s обязательны date и number.', $type));
            if (isset($this->data['cid'])) {
                throw new ValidationException(sprintf('Для договора типа %s нельзя передавать cid.', $type));
            }
        }

        if ($type === ContractType::INTERMEDIARY) {
            $this->require('object', 'Для посреднического договора обязателен object.');
            $this->require(
                'isFundsAllocationToPrincipal',
                'Для посреднического договора обязателен isFundsAllocationToPrincipal.'
            );
        }

        $hasIntermediary = isset($this->data['intermediary']);
        if ($hasParent && $hasIntermediary) {
            throw new ValidationException('Для доп. соглашения (задан parentId) нельзя передавать intermediary.');
        }
        if (!$hasParent && !$hasIntermediary) {
            throw new ValidationException('Не задан parentId — необходимо передать данные исполнителя (intermediary).');
        }

        return $this->data;
    }

    private function require(string $key, string $message): void
    {
        if (!array_key_exists($key, $this->data)) {
            throw new ValidationException($message);
        }
    }
}
