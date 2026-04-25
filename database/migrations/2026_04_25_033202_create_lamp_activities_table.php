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
    Schema::create('lamp_activities', function (Blueprint $table) {
        $table->id();
        $table->string('device_id')->default('CCTV-YOLOv8');
        // Kita catat setiap step: PIR_TRIGGER, HUMAN_DETECTED, LAMP_ON, LAMP_OFF
        $table->string('event_type'); 
        // Simpan nilai akurasi YOLO (0.00 - 1.00) untuk bahan skripsi
        $table->float('confidence_score')->nullable(); 
        // Kita gunakan timestamp manual agar sinkron dengan waktu kejadian di server
        $table->timestamp('recorded_at')->useCurrent();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lamp_activities');
    }
};
