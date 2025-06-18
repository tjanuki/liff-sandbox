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
        Schema::create('line_channels', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint_uuid')->unique();
            $table->string('liff_id');
            $table->string('channel_id');
            $table->string('channel_secret');
            $table->text('channel_access_token');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_channels');
    }
};
