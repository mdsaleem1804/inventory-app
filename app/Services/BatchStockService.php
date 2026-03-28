<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class BatchStockService
{
    public function createInboundBatch(
        Product $product,
        int $quantity,
        float $costPrice,
        int $userId,
        string $reference,
        ?string $notes = null,
        ?string $expiryDate = null,
        ?float $mrp = null
    ): ProductBatch {
        $batch = ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => $this->generateBatchNumber($product),
            'quantity' => $quantity,
            'remaining_quantity' => $quantity,
            'cost_price' => $costPrice,
            'mrp' => $mrp,
            'expiry_date' => $expiryDate,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        StockMovement::create([
            'product_id' => $product->id,
            'product_batch_id' => $batch->id,
            'type' => 'IN',
            'quantity' => $quantity,
            'reference' => $reference,
            'notes' => $notes,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return $batch;
    }

    public function consumeFifo(
        Product $product,
        int $requiredQuantity,
        int $userId,
        string $reference,
        ?string $notes = null,
        bool $enforceExpiry = false,
        ?int $preferredBatchId = null
    ): Collection {
        $batchQuery = ProductBatch::query()
            ->where('product_id', $product->id)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('created_at')
            ->orderBy('id')
            ->lockForUpdate();

        if ($enforceExpiry) {
            $batchQuery->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now()->toDateString());
            });
        }

        $batches = $batchQuery->get();

        $available = (int) $batches->sum('remaining_quantity');

        if ($available < $requiredQuantity) {
            throw ValidationException::withMessages([
                'items' => ["Insufficient batch stock for {$product->name}. Available: {$available}."],
            ]);
        }

        $remaining = $requiredQuantity;
        $allocations = collect();

        if ($preferredBatchId) {
            $preferredBatch = $batches->firstWhere('id', $preferredBatchId);

            if (! $preferredBatch) {
                throw ValidationException::withMessages([
                    'items' => ["Selected batch is not available for {$product->name}."],
                ]);
            }

            $deductQty = min($remaining, (int) $preferredBatch->remaining_quantity);
            $allocations->push($this->deductFromBatch($preferredBatch, $product, $deductQty, $userId, $reference, $notes));
            $remaining -= $deductQty;

            $batches = $batches->where('id', '!=', $preferredBatchId)
                ->values();
        }

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $deductQty = min($remaining, (int) $batch->remaining_quantity);
            $allocations->push($this->deductFromBatch($batch, $product, $deductQty, $userId, $reference, $notes));

            $remaining -= $deductQty;
        }

        return $allocations;
    }

    private function deductFromBatch(ProductBatch $batch, Product $product, int $deductQty, int $userId, string $reference, ?string $notes): array
    {
        $batch->remaining_quantity -= $deductQty;
        $batch->updated_by = $userId;
        $batch->save();

        $lineCost = $deductQty * (float) $batch->cost_price;

        StockMovement::create([
            'product_id' => $product->id,
            'product_batch_id' => $batch->id,
            'type' => 'OUT',
            'quantity' => $deductQty,
            'reference' => $reference,
            'notes' => $notes,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return [
            'batch' => $batch,
            'quantity' => $deductQty,
            'cost_price' => (float) $batch->cost_price,
            'mrp' => $batch->mrp !== null ? (float) $batch->mrp : null,
            'line_cost' => $lineCost,
        ];
    }

    private function generateBatchNumber(Product $product): string
    {
        $base = 'BTH-' . now()->format('YmdHis') . '-' . $product->id;
        $counter = 1;

        do {
            $batchNumber = $base . '-' . str_pad((string) $counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        } while (ProductBatch::query()->where('batch_number', $batchNumber)->exists());

        return $batchNumber;
    }
}
