<?php

namespace Naseg\Rateable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Rating extends Model
{
    public $fillable = ['rating','comment','rater_id','user_id'];

    public function rateable()
    {
        return $this->morphTo();
    }
    
    public function rated()
    {
        return $this->morphTo();
    }

    public function user()
    {
        $userClassName = Config::get('auth.model');
        if (is_null($userClassName)) {
            $userClassName = Config::get('auth.providers.users.model');
        }

        return $this->belongsTo($userClassName);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rateable) {
            // $rateable->rateable_user = ;
        });
    }
}
