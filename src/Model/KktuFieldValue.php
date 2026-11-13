<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Заполненное поле динамической формы ККТУ креатива.
 */
final class KktuFieldValue extends AbstractModel
{
    /** Внутренний ключ поля. */
    public function getKey(): ?string
    {
        return $this->getString('key');
    }

    /** Значение поля, приведённое к строке. */
    public function getValue(): ?string
    {
        return $this->getString('value');
    }
}
