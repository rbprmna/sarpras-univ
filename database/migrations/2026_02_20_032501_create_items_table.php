<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_number')->unique();
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->enum('condition', ['baik', 'cukup_baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->enum('status', ['aktif', 'tidak_aktif', 'dipinjam', 'dalam_perbaikan'])->default('aktif');
            $table->datetime('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
