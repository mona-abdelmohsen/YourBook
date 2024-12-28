<?php

namespace App\Enum;

enum PrivacyEnum: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
    case FRIENDS = 'friends';
    case FRIENDS_OF_FRIENDS = 'friends_of_friends';
}
