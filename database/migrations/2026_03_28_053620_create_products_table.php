<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('sku')->unique();
                $table->string('barcode')->nullable()->unique();
                $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->decimal('price', 10, 2);
                $table->decimal('cost_price', 10, 2)->default(0);
                $table->enum('unit', ['pcs', 'kg', 'box'])->default('pcs');
                $table->unsignedInteger('minimum_stock')->default(0);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });

            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->unique()->after('sku');
            }

            if (! Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete()->after('barcode');
            }

            if (! Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->default(0)->after('price');
            }

            if (! Schema::hasColumn('products', 'unit')) {
                $table->enum('unit', ['pcs', 'kg', 'box'])->default('pcs')->after('cost_price');
            }

            if (! Schema::hasColumn('products', 'minimum_stock')) {
                $table->unsignedInteger('minimum_stock')->default(0)->after('unit');
            }

            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('minimum_stock');
            }

            if (! Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }

            if (! Schema::hasColumn('products', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('is_active');
            }

            if (! Schema::hasColumn('products', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
