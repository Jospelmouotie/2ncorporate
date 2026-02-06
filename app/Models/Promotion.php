<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

   protected $fillable = ['product_id', 'sale_price', 'start_at', 'end_at'];
   protected $casts = [
    'start_at' => 'datetime',
    'end_at'   => 'datetime',
    'sale_price' => 'decimal:2'
];

// Important pour charger le produit avec la promo

    protected $dates = ['start_at', 'end_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('start_at', '<=', $now)
                     ->where('end_at', '>=', $now);
    }
}
