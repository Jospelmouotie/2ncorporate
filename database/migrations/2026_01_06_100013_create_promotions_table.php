<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
  Schema::create('promotions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->decimal('sale_price', 10, 2); // Le prix que le client paiera
    $table->dateTime('start_at');
    $table->dateTime('end_at');
    $table->timestamps();
});;
}

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
