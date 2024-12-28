<?php

namespace App\Enums;

enum SettingValue: string
{
    case All = 'All';
    case MyFriends = 'My Friends';
    case FriendsOfFriends = 'Friends of Friends';
    case OnlyMe = 'Only Me';

    case TurnOn = 'Turn On';
    case TurnOff = 'Turn Off';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
