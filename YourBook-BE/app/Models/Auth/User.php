<?php

namespace App\Models\Auth;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Interfaces\MustVerifyMobile;
use App\Models\Book;
use App\Models\Media;
use App\Models\Posts\Post;
use App\Models\Setting;
use App\Models\Stories\Story;
use App\Models\UserCategory;
use App\Notifications\VerifyEmailByCode;
use App\Traits\Comment\Commenter;
use App\Traits\Reportable;
use DevDojo\LaravelReactions\Models\Reaction;
use DevDojo\LaravelReactions\Traits\Reacts;
use Exception;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Multicaret\Acquaintances\Interaction;
use Multicaret\Acquaintances\Status;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeFollowed;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use Multicaret\Acquaintances\Traits\CanBeRated;
use Multicaret\Acquaintances\Traits\CanFavorite;
use Multicaret\Acquaintances\Traits\CanFollow;
use Multicaret\Acquaintances\Traits\CanLike;
use Multicaret\Acquaintances\Traits\CanRate;
use Multicaret\Acquaintances\Traits\Friendable;
use Qirolab\Laravel\Reactions\Contracts\ReactsInterface;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements  MustVerifyEmail, MustVerifyMobile, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable;
    use \App\Traits\MustVerifyMobile;
    use InteractsWithMedia;
    use Reacts;
    use Commenter;
    use Reportable;
    // use SoftDeletes;
    use Reacts;

    // protected $dates = ['deleted_at'];

    
    /** FriendShip Management  */
    use Friendable;
    use CanFollow, CanBeFollowed;
    use CanLike, CanBeLiked;
    use CanRate, CanBeRated;
    use CanFavorite, CanBeFavorited;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email', 'account_updated',
        'password',
        'phone', 'avatar', 'gender',
        'country_code', 'country_id',
        'privacy',
        'about', 'birth_date', 'fcm_token',
        'enable'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_last_attempt_date' => 'datetime',
        'email_verify_code_sent_at' => 'datetime',
        'enable' => 'boolean',

    ];

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.'.$this->id;
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|array|null
     */
    public function routeNotificationForFcm(): array|string|null
    {
        return $this->fcm_token;
    }

    /**
     * @param bool $newData
     * @return void
     * @throws Exception
     */
    public function sendEmailVerificationNotification(bool $newData = false): void
    {
        if($newData)
        {
            $this->forceFill([
                'email_verify_code' => random_int(111111, 999999),
                'email_attempts_left' => config('mobile.max_attempts'),
                'email_verify_code_sent_at' => now(),
            ])->save();
        }

        $this->notify(new VerifyEmailByCode);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verify_code' => NULL,
            'email_verified_at' => $this->freshTimestamp(),
            'email_attempts_left' => 0,
        ])->save();
    }

    /**
     * @return HasMany
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * return user profile picture url
     * @return Application|string|UrlGenerator|\Illuminate\Contracts\Foundation\Application
     */
    public function profilePicture(): Application|string|UrlGenerator|\Illuminate\Contracts\Foundation\Application
    {
        return $this->avatar ?
            url('storage/avatars/'.basename($this->avatar)): 'https://placehold.co/400';
    }

    /**
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(UserCategory::class);
    }

    /**
     * @param Media|\Spatie\MediaLibrary\MediaCollections\Models\Media|null $media
     * @return void
     */
    public function registerMediaConversions(Media|\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
//        $this->addMediaConversion('thumb')
//            ->width(368)
//            ->height(232)
//            ->extractVideoFrameAtSecond(10)
//            ->performOnCollections('videos')
//            ->nonQueued();
    }

    public function canBefriend($recipient): bool
    {
        // if user has Blocked the recipient and changed his mind
        // he can send a friend request after unblocking
        if ($this->hasBlocked($recipient)) {
            $this->unblockFriend($recipient);

            return true;
        }


        if($this->isBlockedBy($recipient)){
            return false;
        }

        // if sender has a friendship with the recipient return false
        if ($friendship = $this->getFriendship($recipient)) {
            // if previous friendship was Denied then let the user send fr
            //if ($friendship->status != Status::DENIED) {
                return false;
            //}
        }

        return true;
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }


    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function reactions()
{
    return $this->morphMany(Reaction::class, 'reactable');
}


    
}
