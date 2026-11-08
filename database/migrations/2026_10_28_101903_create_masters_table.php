<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masters', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug')->unique();
            $table->string('title', 160);
            $table->text('bio');
            $table->json('services');
            $table->unsignedTinyInteger('experience_years')->default(1);
            $table->unsignedInteger('base_price')->default(500);
            $table->string('district', 100);
            $table->string('schedule', 160);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masters');
    }
};
