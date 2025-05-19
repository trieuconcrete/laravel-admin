<?php

namespace App\Enum;

enum UserStatus: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;

    /**
     * Summary of label
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Đang làm việc',
            self::INACTIVE => 'Đã nghỉ việc',
        };
    }

    /**
     * Summary of options
     * @return string[]
     */
    public static function options(): array
    {
        return [
            self::ACTIVE->value => self::ACTIVE->label(),
            self::INACTIVE->value => self::INACTIVE->label(),
        ];
    }

    public static function fromBool(bool $value): self
    {
        return $value ? self::ACTIVE : self::INACTIVE;
    }
}
