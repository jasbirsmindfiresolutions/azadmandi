<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category',
        'user_id',
        'name',
        'description',
        'price_per_kg',
        'is_dynamic_price_enabled',
        'is_out_of_stock',
        'total_stock',
    ];

}
