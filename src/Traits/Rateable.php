<?php

namespace Naseg\Rateable\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Naseg\Rateable\Models\Rating;

trait Rateable
{
    /**
     * This model has many ratings.
     *
     * @param mixed $rating
     * @param mixed $value
     * @param string $comment
     *
     * @return Rating
     */
    public function rate(int $rating, Model $causer, string $comment, Model $user = null)
    {
        $model = new Rating();
        $model->rating = $rating;
        $model->comment = $comment;
        $model->causer_type = get_class($causer);
        $model->causer_id = $causer->getKey();
        $model->user_id = $user->id ?? Auth::id();

        $this->ratings()->save($model);
    }

    public function rateOnce(int $rating, Model $causer, string $comment, Model $user = null)
    {
        $model = Rating::query()
            ->where('rateable_type', '=', $this->getMorphClass())
            ->where('rateable_id', '=', $this->id)
            ->where('user_id', '=',  $user->id ?? Auth::id())
            ->first()
        ;

        if ($model) {
            $model->rating = $rating;
            $model->comment = $comment;
            $model->save();
        } else {
            $this->rate($rating,$causer, $comment,$user);
        }
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    protected function avgRating(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->ratings->avg("rating"),1),
        );
    }

    protected function sumRating(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ratings->sum("rating"),
        );
    }

    protected function timesRated(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ratings->count(),
        );
    }

    protected function usersRated(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ratings->groupBy('user_id')->pluck('user_id')->count(),
        );
    }
    protected function userAvgRatings(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->withAvg("ratings","rating")->avg("ratings_avg_rating"),1),
        );
    }

  

    public function userRatings()
    {
        return number_format($this->ratings->withAvg('ratings',"rating")
        ->pluck("ratings_avg_rating")->avg(),1)
       ;
    }

    public function userSumRating()
    {
        return $this->ratings->where('user_id', Auth::id())->sum('rating');
    }

    public function ratingPercent($max = 5)
    {
        $quantity = $this->ratings()->count();
        $total = $this->sumRating();

        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    // Getters

    public function getUserAverageRatingAttribute()
    {
        return $this->userAverageRating();
    }

    public function getUserSumRatingAttribute()
    {
        return $this->userSumRating();
    }
}
