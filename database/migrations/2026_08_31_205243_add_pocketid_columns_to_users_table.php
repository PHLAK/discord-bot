<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Run the migrations. */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pocketid_id')->nullable()->unique()->after('remember_token');
            $table->string('pocketid_token')->nullable()->after('pocketid_id');
            $table->string('pocketid_refresh_token')->nullable()->after('pocketid_token');
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pocketid_id', 'pocketid_token', 'pocketid_refresh_token']);
        });
    }
};
