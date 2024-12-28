<?php

namespace App\Enums;

enum SettingName: string
{
    case FollowList = 'follow_list';
    case Favourite = 'favourite';
    case CommentMyPost = 'comment_my_post';
    case MentionMe = 'mention_me';
    case ActivityStatus = 'activity_status';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
