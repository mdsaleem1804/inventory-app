<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('batch_number')->unique();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('remaining_quantity');
            $table->decimal('cost_price', 12, 2);
            $table->date('expiry_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'remaining_quantity']);
            $table->index(['expiry_date', 'remaining_quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
