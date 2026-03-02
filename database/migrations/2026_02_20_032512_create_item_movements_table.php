<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('to_room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->foreignId('from_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('to_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->enum('type', [
                'masuk',
                'pindah',
                'keluar',
                'pinjam',
                'kembali',
                'perbaikan',
                'selesai_perbaikan',
            ])->default('pindah');
            $table->foreignId('moved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moved_at')->useCurrent();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_movements');
    }
};
