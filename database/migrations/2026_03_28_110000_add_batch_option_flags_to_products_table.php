<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_batch_enabled')) {
                $table->boolean('is_batch_enabled')->default(false)->after('is_active');
            }

            if (! Schema::hasColumn('products', 'has_expiry')) {
                $table->boolean('has_expiry')->default(false)->after('is_batch_enabled');
            }

            if (! Schema::hasColumn('products', 'has_mrp')) {
                $table->boolean('has_mrp')->default(false)->after('has_expiry');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $toDrop = [];

            if (Schema::hasColumn('products', 'has_mrp')) {
                $toDrop[] = 'has_mrp';
            }

            if (Schema::hasColumn('products', 'has_expiry')) {
                $toDrop[] = 'has_expiry';
            }

            if (Schema::hasColumn('products', 'is_batch_enabled')) {
                $toDrop[] = 'is_batch_enabled';
            }

            if (! empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
