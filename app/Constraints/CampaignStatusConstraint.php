<?php


namespace App\Constraints;


class CampaignStatusConstraint extends BaseConstraint
{
    const STATUS_RESERVED = 'reserved';
    const STATUS_BOOKED = 'booked';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_AVAILABLE = 'available'; // Status when positions belongs to no campaign / cancel campaign

    public static function getAll($valueOnly = false): array
    {
        $all = [
            [
                'value' => static::STATUS_RESERVED,
                'text' => ucfirst(static::STATUS_RESERVED),
            ],
            [
                'value' => static::STATUS_BOOKED,
                'text' => ucfirst(static::STATUS_BOOKED),
            ],
            [
                'value' => static::STATUS_CANCELLED,
                'text' => ucfirst(static::STATUS_CANCELLED),
            ],
        ];

        return $valueOnly ? array_column($all, 'value') : $all;
    }
}
