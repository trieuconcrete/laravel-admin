<?php

namespace App\Enum;

enum SalaryType: int
{
    case BASIC_SALARY = 1;      // Tài xế ăn lương cơ bản
    case COMMISSION_SALARY = 2; // Tài xế ăn lương doanh số (chạy xe công)

    /**
     * Get all available salary types
     *
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::BASIC_SALARY->value => 'Tài xế ăn lương cơ bản',
            self::COMMISSION_SALARY->value => 'Tài xế ăn lương doanh số',
        ];
    }

    /**
     * Get type label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return self::getTypes()[$this->value] ?? 'Không xác định';
    }

    /**
     * Get type color for UI
     *
     * @return string
     */
    public function getColor(): string
    {
        return match($this) {
            self::BASIC_SALARY => 'primary',
            self::COMMISSION_SALARY => 'success',
            default => 'secondary',
        };
    }

    /**
     * Check if this is basic salary type
     *
     * @return bool
     */
    public function isBasicSalary(): bool
    {
        return $this === self::BASIC_SALARY;
    }

    /**
     * Check if this is commission salary type
     *
     * @return bool
     */
    public function isCommissionSalary(): bool
    {
        return $this === self::COMMISSION_SALARY;
    }

    /**
     * Get options for select dropdown
     *
     * @return array
     */
    public static function options(): array
    {
        return self::getTypes();
    }
} 