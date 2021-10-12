<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $guarded=['id'];

    public function reviews()
    {
        return $this->hasMany(Reviews::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
