<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Conclusion extends Model
{
    /**
     * @var string
     */
    protected $table = 'reports_conclusions';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $casts = ['meta' => 'array'];
    protected $fillable = ['conclusion', 'action_taken', 'report_id'];

    /**
     * @return BelongsTo
     */
    public function conclusion(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * @return MorphTo
     */
    public function report()
{
    return $this->belongsTo(Report::class);
}


public function judge(): MorphTo
{
    return $this->morphTo();
}

}
