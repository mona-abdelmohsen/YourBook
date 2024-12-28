<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $casts = ['meta' => 'array'];

    /**
     * @return MorphTo
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function reporter()
    {
        return $this->morphTo();
    }

    /**
     * @return HasOne
     */
    public function conclusion()
    {
        return $this->hasOne(Conclusion::class);
    }

    /**
     * @return mixed
     */
    public function judge(): mixed
    {
        return $this->conclusion->judge;
    }

    /**
     * @param $data
     * @param Model $judge
     *
     * @return Conclusion
     */
    public function conclude($data, Model $judge): Conclusion
{
    $conclusion = new Conclusion();
    $conclusion->fill(array_merge($data, [
        'report_id' => $this->id,
        'judge_id' => $judge->id,
        'judge_type' => get_class($judge),
    ]));
    $conclusion->save();

    return $conclusion;
}



    /**
     * @return array
     */
    public static function allJudges(): array
    {
        $judges = [];

        foreach (Conclusion::get() as $conclusion) {
            $judges[] = $conclusion->judge;
        }

        return $judges;
    }

    // In the Report model
protected static function booted()
{
    static::saving(function ($report) {
        // Check if reporter_type and reporter_id are empty, then set them
        if (!$report->reporter_type) {
            $report->reporter_type = \App\Models\Auth\User::class;
        }

        if (!$report->reporter_id) {
            $report->reporter_id = auth()->id();
        }
    });
}

}
