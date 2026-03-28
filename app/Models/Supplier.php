<?php

namespace App\Models;

use App\Models\Concerns\TracksAuditFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, TracksAuditFields;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
