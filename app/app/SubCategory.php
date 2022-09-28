<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    
    protected $fillable = [
        'category_id', 'name', 'image'
    ];

    protected $table = 'sub_categories';

    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function products(){
        return $this->hasMany(Product::class);
    }


}
