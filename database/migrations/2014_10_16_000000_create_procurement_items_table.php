<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementItemsTable extends Migration
{
    public function up()
    {
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procurement_request_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('procurement_items');
    }
}
