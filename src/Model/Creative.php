<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Креатив — конкретное объявление внутри группы.
 */
final class Creative extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->getInt('id');
    }

    public function getName(): ?string
    {
        return $this->getString('name');
    }

    public function getTitle(): ?string
    {
        return $this->getString('title');
    }

    public function getDescription(): ?string
    {
        return $this->getString('description');
    }

    public function getButtonText(): ?string
    {
        return $this->getString('buttonText');
    }

    public function getLink(): ?string
    {
        return $this->getString('link');
    }

    /**
     * Статус креатива: см. \AvitoAds\Enum\CreativesStatus.
     *
     * Неизвестный статус возвращается строкой как есть.
     */
    public function getStatus(): ?string
    {
        return $this->getString('status');
    }

    /**
     * Все коды причин текущего статуса; [] — причин нет.
     *
     * Порядок не отражает приоритет. API может добавлять новые коды: неизвестные
     * значения возвращаются как есть.
     *
     * @return string[]
     */
    public function getStatusReasons(): array
    {
        return $this->getStringList('statusReasons');
    }

    /**
     * Главная причина текущего статуса; "" — главная причина не определена
     * (даже если getStatusReasons() не пуст).
     */
    public function getTopStatusReason(): string
    {
        return (string) $this->getString('topStatusReason');
    }

    public function getGroupId(): ?int
    {
        return $this->getInt('groupID');
    }

    public function getCampaignId(): ?int
    {
        return $this->getInt('campaignID');
    }

    public function getAccountId(): ?int
    {
        return $this->getInt('accountID');
    }

    public function getAdvertiserId(): ?int
    {
        return $this->getInt('advertiserID');
    }

    public function getManagerId(): ?int
    {
        return $this->getInt('managerID');
    }

    /** Модель оплаты: см. \AvitoAds\Enum\CampaignPaymentModel. */
    public function getPaymentModel(): ?string
    {
        return $this->getString('paymentModel');
    }

    /** Тип кампании: см. \AvitoAds\Enum\CampaignType. */
    public function getCampaignType(): ?string
    {
        return $this->getString('campaignType');
    }

    /** ID пользователя, создавшего креатив. */
    public function getUserId(): ?int
    {
        return $this->getInt('userID');
    }

    /** ID посадочной страницы. */
    public function getLandingId(): ?int
    {
        return $this->getInt('landingID');
    }

    /** Оригинальная ссылка для аналитики. */
    public function getOriginUrl(): ?string
    {
        return $this->getString('originUrl');
    }

    /** Ссылка на предпросмотр креатива. */
    public function getPreviewUrl(): ?string
    {
        return $this->getString('previewUrl');
    }

    /**
     * Заполненные поля динамической формы ККТУ.
     *
     * @return KktuFieldValue[]
     */
    public function getKktuFields(): array
    {
        return $this->getModelList('kktuFields', KktuFieldValue::class);
    }

    /**
     * Юридическая информация (LegalInfo) или [].
     *
     * @deprecated С swagger 1.2.0 поле `legalInfo` устарело и сохраняется для
     *             обратной совместимости. Используйте getKktuFields().
     *
     * @return array<string, mixed>
     */
    public function getLegalInfo(): array
    {
        return $this->getArray('legalInfo');
    }
}
