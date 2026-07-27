<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Customer extends Model implements Wallet
{
    use HasFactory, HasWallet, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    protected $guarded = [];

    protected $casts = [
        'max_credit_limit' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isWholesale(): bool
    {
        return $this->type === 'wholesale';
    }

    public function getTotalOutstandingDebt(): float
    {
        return (float) $this->orders()
            ->where('status', 'piutang')
            ->sum('total');
    }

    public function isOverCreditLimit(float $newAmount = 0): bool
    {
        if ($this->max_credit_limit <= 0) return false;
        return ($this->getTotalOutstandingDebt() + $newAmount) > $this->max_credit_limit;
    }
}
