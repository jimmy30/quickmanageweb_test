<?php

namespace App;

use App;
use App\Helpers\ApplicationHelpers;
use Illuminate\Database\Eloquent\Model as Elequent;
use Illuminate\Support\Facades\Config;

class Product extends Elequent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'name', 'price', 'is_active', 
    ];
    
}
