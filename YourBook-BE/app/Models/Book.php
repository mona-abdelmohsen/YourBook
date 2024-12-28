<?php

namespace App\Models;

use App\Models\Auth\User;
use App\Traits\Reportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Book extends Model implements hasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use NodeTrait;
    use CanBeFavorited;
    use Reportable;
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'title', 'description', 'privacy', 'category_id', 'parent_id','deleted_at'
    ];


    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function mediaFiles(): BelongsToMany
    {
        return $this->belongsToMany(config('media-library.media_model'), 'books_media', 'book_id', 'media_id');
    }

    /**
     * cover image is the latest image | to be updated later...
     * @return object|null
     */
    public function cover(): null|object
    {
        return $this->media()->first();
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(UserCategory::class);
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('cover')
            ->onlyKeepLatest(1);
    }
}
