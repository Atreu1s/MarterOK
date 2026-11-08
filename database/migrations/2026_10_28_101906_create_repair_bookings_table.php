<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_bookings', function (Blueprint $table): void {
            $table->id();
            $table->uuid('submission_key')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('master_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('service', 40);
            $table->string('device', 120);
            $table->text('description');
            $table->string('contact_name', 100);
            $table->string('phone', 20);
            $table->json('photos')->nullable();
            $table->string('status', 30)->default('new')->index();
            $table->unsignedInteger('quoted_price')->nullable();
            $table->timestamp('quote_accepted_at')->nullable();
            $table->text('public_message')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamp('consented_at');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_bookings');
    }
};
