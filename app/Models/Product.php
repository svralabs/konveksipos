<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function stockLedgers()
    {
        return $this->hasMany(StockLedger::class);
    }

    public function isLowStock(): bool
    {
        return $this->min_stock > 0 && $this->stock <= $this->min_stock;
    }

    /**
     * Log stock movement and update stock level.
     */
    public function adjustStock(int $qty, string $type, string $note = '', ?int $userId = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        DB::transaction(function () use ($qty, $type, $note, $userId, $referenceType, $referenceId) {
            $stockBefore = $this->stock;
            $this->increment('stock', $qty);
            $this->refresh();

            StockLedger::create([
                'product_id'     => $this->id,
                'user_id'        => $userId ?? auth()->id(),
                'type'           => $type,
                'qty'            => $qty,
                'stock_before'   => $stockBefore,
                'stock_after'    => $this->stock,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'note'           => $note,
            ]);
        });
    }
}
