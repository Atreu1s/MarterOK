<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Лаба 9: поле подтверждения модерации. false — ждёт проверки, true — одобрен.
// Отклонённый комментарий удаляется.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table): void {
            $table->boolean('is_approved')->default(false)->index()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropIndex(['is_approved']);
            $table->dropColumn('is_approved');
        });
    }
};
