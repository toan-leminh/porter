<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemporaryQuotes extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'code'
    ];
}
