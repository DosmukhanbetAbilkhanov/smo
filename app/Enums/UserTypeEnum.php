<?php

declare(strict_types=1);

namespace App\Enums;

enum UserTypeEnum: int
{
    case Admin = 1;
    case RetailCustomer = 2;
    case CompanyCustomer = 3;
    case Seller = 4;

    /**
     * Get the display name for the user type
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => __('common.admin'),
            self::RetailCustomer => __('common.retail_customer'),
            self::CompanyCustomer => __('common.company_customer'),
            self::Seller => __('common.seller'),
        };
    }

    /**
     * Get all user types as an array
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->toArray();
    }

    /**
     * Check if the user type is an admin
     */
    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    /**
     * Check if the user type is a seller
     */
    public function isSeller(): bool
    {
        return $this === self::Seller;
    }

    /**
     * Check if the user type is a customer (retail or company)
     */
    public function isCustomer(): bool
    {
        return $this === self::RetailCustomer || $this === self::CompanyCustomer;
    }

    /**
     * Get user types that can manage products
     *
     * @return array<self>
     */
    public static function canManageProducts(): array
    {
        return [self::Admin, self::Seller];
    }

    /**
     * Get user types that can place orders
     *
     * @return array<self>
     */
    public static function canPlaceOrders(): array
    {
        return [self::RetailCustomer, self::CompanyCustomer];
    }
}
