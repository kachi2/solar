<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'image',
        'gallery',
        'description',
        'price' ,
        'sale_price',
        'percentage',
        'exchange_rate'
    ];

    
    public function category(){
      return $this->belongsTo(Category::class);
    }
      
    public function PriceList(){
        return $this->hasMany(ProductVariation::class);
    }

    public function subCategory(){
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
    }
}
