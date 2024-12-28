<?php

namespace App\Traits;

use App\Models\Reports\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reportable
{
    /**
     * @return MorphMany
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * @param $data
     * @param Model $reportable
     *
     * @return Report
     */
    public function report($data, Model $reportable): Report
    {
        $report = (new Report())->fill(array_merge($data, [
            'reporter_id' => $reportable->id,
            'reporter_type' => get_class($reportable),
        ]));

        $this->reports()->save($report);

        return $report;
    }
}
