<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. Ajoute cet import

class Advertisement extends Model
{
    use HasFactory; // 2. Ajoute cette ligne à l'intérieur de la classe

    protected $fillable = ['title', 'type', 'media_url', 'start_at', 'end_at'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Scope publicités actives
     */
    public function scopeActive(Builder $query)
    {
        return $query
            ->where('start_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', now());
            });
    }
}
