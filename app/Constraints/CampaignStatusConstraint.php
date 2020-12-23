<?php


namespace App\Constraints;


class CampaignStatusConstraint extends BaseConstraint
{
    const STATUS_RESERVED = 'reserved';
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';

    public static function getAll($valueOnly = false): array
    {
        $all = [
            [
                'value' => static::STATUS_RESERVED,
                'text' => ucfirst(static::STATUS_RESERVED),
            ],
            [
                'value' => static::STATUS_ACTIVE,
                'text' => ucfirst(static::STATUS_ACTIVE),
            ],
            [
                'value' => static::STATUS_CANCELLED,
                'text' => ucfirst(static::STATUS_CANCELLED),
            ],
        ];

        return $valueOnly ? array_column($all, 'value') : $all;
    }
}
