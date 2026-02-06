<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wishlist extends Model
{
    use HasFactory;


protected $fillable = ['user_id', 'guest_id'];
public function products()
    {
        return $this->belongsToMany(Product::class, 'product_wishlist')
                    ->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
