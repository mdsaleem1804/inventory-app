<?php

namespace App\Models;

use App\Models\Concerns\TracksAuditFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, TracksAuditFields;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category_id',
        'price',
        'cost_price',
        'unit',
        'minimum_stock',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function getCurrentStockAttribute(): int
    {
        if (array_key_exists('stock_in_total', $this->attributes) || array_key_exists('stock_out_total', $this->attributes)) {
            $in = (int) ($this->attributes['stock_in_total'] ?? 0);
            $out = (int) ($this->attributes['stock_out_total'] ?? 0);

            return $in - $out;
        }

        if ($this->relationLoaded('stockMovements')) {
            $in = (int) $this->stockMovements->where('type', 'IN')->sum('quantity');
            $out = (int) $this->stockMovements->where('type', 'OUT')->sum('quantity');

            return $in - $out;
        }

        $in = (int) $this->stockMovements()->where('type', 'IN')->sum('quantity');
        $out = (int) $this->stockMovements()->where('type', 'OUT')->sum('quantity');

        return $in - $out;
    }
}
