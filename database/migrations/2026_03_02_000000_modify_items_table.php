<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Hapus kolom brand dan unit_id
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['brand', 'unit_id']);

            // Tambah kolom baru
            $table->text('specification')->nullable()->after('description');
            $table->unsignedInteger('quantity')->default(1)->after('specification');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['specification', 'quantity']);
            $table->string('brand')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
