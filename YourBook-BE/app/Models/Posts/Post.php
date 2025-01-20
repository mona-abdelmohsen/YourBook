<?php

namespace App\Models\Posts;

use App\Enum\PrivacyEnum;
use App\helper\HashtagParser;
use App\Models\Auth\User;
use App\Models\Book;
use App\Traits\Comment\Commentable;
use App\Traits\ReactionHelper;
use App\Traits\Reportable;
use ArrayAccess;
use DevDojo\LaravelReactions\Contracts\ReactableInterface;
use DevDojo\LaravelReactions\Models\Reaction;
use DevDojo\LaravelReactions\Traits\Reactable;
use DevDojo\LaravelReactions\Traits\Reacts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Renderer\HtmlRenderer;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

class Post extends Model implements hasMedia, ReactableInterface
{
    use SoftDeletes;
    use HasFactory, InteractsWithMedia;
    use Reactable, ReactionHelper;
    use Commentable;
    use HasTags;
    use CanBeFavorited;
    use Reportable;
    use Reactable;

    protected $table='posts';
    protected $with = ['sharedPost'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 'content', 'mood_id', 'location', 'share_link',
        'privacy', 'show_in_feed', 'content_background','share_id'
    ];

    // protected $casts = [
    //     'privacy'   => PrivacyEnum::class,
    // ];
    
    protected $casts = [
        'privacy' => 'string',  
    ];


    /**
     * @return BelongsTo
     */
    public function mood(): BelongsTo
    {
        return $this->belongsTo(Mood::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function taggedFriends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_tagged_friends', 'post_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function taggedBooks(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'post_books', 'post_id', 'book_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function sharedPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'share_id');
    }


    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::saving( function($model) {

            App::singleton('tagqueue', function() {
                return new \App\helper\TagQueue;
            });

            $conv = new CommonMarkConverter();
            $conv->getEnvironment()->addInlineParser(new HashtagParser());
//            $text = mb_convert_encoding($model->content, 'UTF-8', mb_detect_encoding($model->content));
//            $text = mb_check_encoding($text, 'UTF-8') ? $text : mb_convert_encoding($text, 'UTF-8', 'UTF-8');
            $conv->convert($model->content);
        });

        self::saved( function($model) {
            $tags = app('tagqueue')->getTags();
            $model->syncTags($tags);
        });
    }


    public function syncTags(string | array | ArrayAccess $tags): static
    {
        if (is_string($tags)) {
            $tags = Arr::wrap($tags);
        }

        $className = static::getTagClassName();

        $tags = collect($className::findOrCreate($tags));
        $tags = $tags->pluck('id')->toArray();
        $this->tags()->sync($tags);

        $className::whereIn('id', $tags)->increment('count');

        return $this;
    }


}
