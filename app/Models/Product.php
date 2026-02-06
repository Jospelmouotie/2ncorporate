<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'sku', 'stock', 'category_id', 'is_active', 'is_on_sale'
    ];

    // Ajout de 'image_url', 'final_price', 'discount_info' pour Vue.js
    protected $appends = ['image_url', 'final_price', 'discount_info'];

    // Charge automatiquement ces relations (Eager Loading par défaut)
    protected $with = ['images', 'category', 'promotions'];

    /* --- RELATIONS --- */

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function promotions() {
        return $this->hasMany(Promotion::class);
    }

    /**
     * RELATION MANQUANTE : Celle qui causait l'erreur 500
     */
    public function reviews() {
        return $this->hasMany(Review::class);
    }

    /* --- ATTRIBUTS CALCULÉS (ACCESSORS) --- */

    // 1. URL de l'image principale
    public function getImageUrlAttribute()
    {
        // On cherche l'image marquée 'is_main', sinon la première, sinon un placeholder
        $image = $this->images->where('is_main', true)->first() ?: $this->images->first();
        return $image ? $image->url : '/storage/placeholder.png';
    }

    // 2. Calcul du prix final après réduction
    public function getFinalPriceAttribute()
    {
        $promo = $this->promotions
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->first();

        if ($promo) {
            return $this->price - ($this->price * $promo->discount_percentage / 100);
        }

        return $this->price;
    }

    // 3. Infos sur la promotion pour le badge
    public function getDiscountInfoAttribute()
    {
        $promo = $this->promotions
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->first();

        return $promo ? [
            'percentage' => $promo->discount_percentage,
            'label' => '-' . $promo->discount_percentage . '%'
        ] : null;
    }
}
