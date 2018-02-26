<?php

namespace App\Traits;

use App\Activity;

trait RecordsActivity
{
    /**
     *
     */
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) {
            return;
        }

        //
        foreach (static::getActivitiesToRecord() as $event) {
            static:: $event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        //
        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }

    /**
     * @return array
     */
    protected static function getActivitiesToRecord()
    {
        return ['created'];
    }

    /**
     * @param $event
     */
    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    /**
     * @return mixed
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * @param $event
     * @return string
     */
    protected function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }
}