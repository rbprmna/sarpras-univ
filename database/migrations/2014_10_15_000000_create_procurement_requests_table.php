<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();

            $table->string('requester_name');
            $table->string('department');
            $table->text('used_for')->nullable();
            $table->enum('request_type', ['biasa', 'segera', 'mendesak'])->default('biasa');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('request_date');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('procurement_requests');
    }
}
