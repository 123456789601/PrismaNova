<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inventory_usage_syncs')) {
            Schema::create('inventory_usage_syncs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('external_id')->unique();
                $table->json('payload')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_usage_syncs');
    }
};
