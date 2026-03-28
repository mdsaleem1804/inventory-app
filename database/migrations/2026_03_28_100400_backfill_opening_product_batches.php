<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $products = DB::table('products')->select('id', 'cost_price')->get();

        foreach ($products as $product) {
            $hasBatch = DB::table('product_batches')
                ->where('product_id', $product->id)
                ->exists();

            if ($hasBatch) {
                continue;
            }

            $stockIn = (int) DB::table('stock_movements')
                ->where('product_id', $product->id)
                ->where('type', 'IN')
                ->sum('quantity');

            $stockOut = (int) DB::table('stock_movements')
                ->where('product_id', $product->id)
                ->where('type', 'OUT')
                ->sum('quantity');

            $remaining = $stockIn - $stockOut;

            if ($remaining <= 0) {
                continue;
            }

            DB::table('product_batches')->insert([
                'product_id' => $product->id,
                'batch_number' => 'OPEN-' . now()->format('YmdHis') . '-' . $product->id,
                'quantity' => $remaining,
                'remaining_quantity' => $remaining,
                'cost_price' => $product->cost_price ?? 0,
                'expiry_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('product_batches')
            ->where('batch_number', 'like', 'OPEN-%')
            ->delete();
    }
};
