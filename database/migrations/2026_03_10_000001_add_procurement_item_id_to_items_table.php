<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('procurement_item_id')
                ->nullable()
                ->after('created_by')
                ->constrained('procurement_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['procurement_item_id']);
            $table->dropColumn('procurement_item_id');
        });
    }
};
