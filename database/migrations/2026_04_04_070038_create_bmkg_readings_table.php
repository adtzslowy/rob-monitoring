<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bmkg_readings', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah', 50)->index();
            $table->string('adm4', 20);
            $table->timestamp('local_datetime')->nullable()->index();
            $table->timestamp('utc_datetime')->nullable();
            $table->float('suhu')->nullable();
            $table->float('kelembapan')->nullable();
            $table->float('kecepatan_angin')->nullable();
            $table->float('arah_angin_deg')->nullable();
            $table->string('arah_angin', 20)->nullable();
            $table->float('curah_hujan')->nullable();
            $table->string('cuaca', 100)->nullable();
            $table->string('cuaca_icon', 200)->nullable();
            $table->timestamp('fetched_at')->nullable();

            $table->index(['wilayah', 'local_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bmkg_readings');
    }
};
