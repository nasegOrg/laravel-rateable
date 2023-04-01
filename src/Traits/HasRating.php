<?php
namespace Naseg\Rateable\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Naseg\Rateable\Models\Rating;

trait HasRating{

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'causer');
    }

    protected function userAvgRatings(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->ratings->avg("rating"),1),
        );
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
            get: fn () => $this->ratings->groupBy('user_id')->count(),
        );
    }

    public function userRatings()
    {
        return number_format($this->ratings->withAvg('ratings',"rating")
        ->pluck("ratings_avg_rating")->avg(),1)
       ;
    }

}