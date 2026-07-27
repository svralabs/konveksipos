<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Expense extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function (Expense $expense) {
            // Debit: Biaya Operasional (6101)
            \App\Models\GeneralLedger::create([
                'transaction_date' => $expense->expense_date,
                'chart_of_account_id' => \App\Models\ChartOfAccount::where('account_code', '6101')->first()->id,
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'description' => 'Pengeluaran: ' . $expense->description,
                'debit' => $expense->amount,
                'credit' => 0,
            ]);

            // Kredit: Kas & Bank (1101)
            \App\Models\GeneralLedger::create([
                'transaction_date' => $expense->expense_date,
                'chart_of_account_id' => \App\Models\ChartOfAccount::where('account_code', '1101')->first()->id,
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'description' => 'Pembayaran Pengeluaran: ' . $expense->description,
                'debit' => 0,
                'credit' => $expense->amount,
            ]);
            
            // Also deduct from user wallet if they are tracking saldo using laravel-wallet
            if ($expense->user_id) {
                $user = \App\Models\User::find($expense->user_id);
                if ($user) {
                    $user->withdraw((int) round($expense->amount * 100), [
                        'description' => 'Pengeluaran: ' . $expense->description,
                        'expense_id'  => $expense->id,
                    ]);
                }
            }
        });
    }

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function categories(): array
    {
        return [
            'gaji'       => 'Gaji Karyawan',
            'listrik'    => 'Listrik & Utilitas',
            'sewa'       => 'Sewa Ruko/Tempat',
            'transport'  => 'Transport & Pengiriman',
            'peralatan'  => 'Peralatan & Perlengkapan',
            'marketing'  => 'Promosi & Marketing',
            'lain-lain'  => 'Lain-lain',
        ];
    }
}
