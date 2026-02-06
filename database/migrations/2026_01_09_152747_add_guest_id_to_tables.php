<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('wishlists', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->change();
        if (!Schema::hasColumn('wishlists', 'guest_id')) {
            $table->string('guest_id')->nullable()->after('user_id');
        }
    });

    Schema::table('carts', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->change();
        if (!Schema::hasColumn('carts', 'guest_id')) {
            $table->string('guest_id')->nullable()->after('user_id');
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
