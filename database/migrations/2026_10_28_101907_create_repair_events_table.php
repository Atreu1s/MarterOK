<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('repair_booking_id')->constrained()->cascadeOnDelete();
            $table->string('actor', 100);
            $table->string('status', 30);
            $table->text('message')->nullable();
            $table->unsignedInteger('quoted_price')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_events');
    }
};
