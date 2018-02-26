<?php

namespace App\Traits;

trait Favorable
{
    /**
     * Boot the trait
     */
    protected static function bootFavoritable()
    {
        static::deleting(function ($model) {
            $model->favorites->each->delete();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(\App\Favorite::class, 'favorited');
    }

    /**
     *
     */
    public function favorite()
    {
        $attr = ['user_id' => auth()->id()];
        //prevent favorite twice
        if (! $this->favorites()->where($attr)->exists()) {
            $this->favorites()->create(['user_id' => auth()->id()]);
        }
    }

    /**
     * Unfavorite the current reply.
     */
    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];

        $this->favorites()->where($attributes)->get()->each->delete();
    }

    /**
     * @return bool
     */
    public function isFavorited()
    {
        return ! ! $this->favorites->where('user_id', auth()->id())->count();
    }

    /**
     * @return int
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    /**
     * @return bool
     */
    public function getIsFavoritedAttribute()
    {

        return $this->isFavorited();
    }
}