<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    public $timestamps = false;
}
