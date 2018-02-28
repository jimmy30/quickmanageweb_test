<?php

namespace App;

use App;
use App\Helpers\ApplicationHelpers;
use Illuminate\Database\Eloquent\Model as Elequent;
use Illuminate\Support\Facades\Config;

class Ad extends Elequent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'title', 'url', 'start_time', 'end_time', 'is_active', 
    ];
    
}
