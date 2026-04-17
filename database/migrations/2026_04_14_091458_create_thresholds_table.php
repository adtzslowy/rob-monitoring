<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('device_id');

            // Suhu
            $table->float('suhu_min')->nullable();
            $table->float('suhu_max')->nullable();

            // Kelembapan
            $table->float('kelembapan_min')->nullable();
            $table->float('kelembapan_max')->nullable();

            // Tekanan Udara
            $table->float('tekanan_udara_min')->nullable();
            $table->float('tekanan_udara_max')->nullable();

            // Kecepatan Angin
            $table->float('kecepatan_angin_min')->nullable();
            $table->float('kecepatan_angin_max')->nullable();

            // Arah Angin
            $table->float('arah_angin_min')->nullable();
            $table->float('arah_angin_max')->nullable();

            // Ketinggian Air
            $table->float('ketinggian_air_min')->nullable();
            $table->float('ketinggian_air_max')->nullable();

            $table->timestamps();

            // Satu record per device
            $table->unique('device_id');

            // Foreign key — cocok dengan bigint(20) non-unsigned
            $table->foreign('device_id')
                ->references('id')
                ->on('devices')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_thresholds');
    }
};