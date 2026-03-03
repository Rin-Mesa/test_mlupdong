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
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->string('qr_token')->nullable()->after('qr_url');
            $table->string('qr_code_path')->nullable()->after('qr_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_code_path']);
        });
    }
};
