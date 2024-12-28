<?php

namespace App\Models\Stories;

use App\Enum\PrivacyEnum;
use App\Models\Auth\User;
use App\Traits\ReactionHelper;
use Carbon\Carbon;
use DevDojo\LaravelReactions\Contracts\ReactableInterface;
use DevDojo\LaravelReactions\Traits\Reactable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Story extends Model implements HasMedia, ReactableInterface
{
    use SoftDeletes;
    use HasFactory, InteractsWithMedia;
    use Reactable, ReactionHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 'content', 'privacy', 'background_color',
    ];

    protected $casts = [
        'privacy'   => PrivacyEnum::class,
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // story expire within 24 hours
        static::addGlobalScope('available', function (Builder $builder) {
            $builder->where('created_at', '>', Carbon::now()->subDay());
        });

        // load my reactions by default if authenticated.
        static::addGlobalScope('withMyReactions', function (Builder $builder) {
            if(auth()->user()){
                $builder->with('myReactions');
            }
        });
    }

    public function views(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'story_views', 'story_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
