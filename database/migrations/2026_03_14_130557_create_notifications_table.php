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
        Schema::create('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('telegram_chat_id')->nullable();
            $table->boolean('notifikasi_aktif')->default(false);
            $table->boolean('notifikasi_waspada')->default(true);
            $table->boolean('notifikasi_siaga')->default(true);
            $table->boolean('notifikasi_bahaya')->default(true);

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
