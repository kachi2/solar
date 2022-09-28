<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $fillable = [
        'menu_id', 'title', 'image', 'contents'
    ];

}
