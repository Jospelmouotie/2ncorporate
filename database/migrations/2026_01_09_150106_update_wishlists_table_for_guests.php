<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::table('wishlists', function (Blueprint $table) {
        // Rend le user_id optionnel pour les invités
        $table->foreignId('user_id')->nullable()->change();
        // Ajoute une colonne pour identifier l'invité par son IP
        if (!Schema::hasColumn('wishlists', 'guest_id')) {
            $table->string('guest_id')->nullable()->after('user_id');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         $table->dropColumn('user_id');
    }
};
