<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'wholesale' ? 'warning' : 'primary')
                    ->formatStateUsing(fn (string $state): string => $state === 'wholesale' ? 'Grosir' : 'Ritel'),
                TextColumn::make('phone')
                    ->label('No. HP')
                    ->searchable(),
                TextColumn::make('max_credit_limit')
                    ->label('Limit Kredit')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('outstanding_debt')
                    ->label('Piutang Aktif')
                    ->money('IDR')
                    ->getStateUsing(fn ($record) => $record->getTotalOutstandingDebt()),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('pay_debt')
                    ->label('Bayar Cicilan')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Nominal Pembayaran (Rp)')
                            ->numeric()
                            ->required()
                            ->minValue(100),
                        \Filament\Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer Bank',
                            ])
                            ->required()
                            ->default('cash'),
                        \Filament\Forms\Components\Textarea::make('note')
                            ->label('Catatan / Keterangan')
                            ->placeholder('contoh: Bayar cicilan sebagian / Lunas'),
                    ])
                    ->action(function (\App\Models\Customer $record, array $data): void {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                            $amount = (float) $data['amount'];
                            $amountInt = (int) round($amount * 100);

                            // 1. Record in wallet (withdraw to reduce the positive debt balance)
                            $record->withdraw($amountInt, [
                                'description' => 'Pembayaran cicilan piutang via ' . $data['payment_method'] . '. ' . ($data['note'] ?? ''),
                            ]);

                            // 2. Also record revenue to the cashier/user wallet
                            if (auth()->check()) {
                                auth()->user()->deposit($amountInt, [
                                    'description' => 'Terima cicilan piutang dari ' . $record->name,
                                ]);
                            }

                            // 3. Update orders under 'piutang' status, starting from oldest order
                            $orders = $record->orders()
                                ->where('status', 'piutang')
                                ->orderBy('created_at', 'asc')
                                ->get();

                            $remainingPayment = $amount;
                            foreach ($orders as $order) {
                                if ($remainingPayment <= 0) break;

                                // Since we don't have a partial paid tracking column on orders,
                                // let's just mark the order as completed if the remaining payment covers the total.
                                // If it doesn't cover, we keep the status as piutang.
                                // In the future, we could have a paid_amount tracking on orders, but let's check migration.
                                // Oh! The orders table has paid_amount! Let's view orders table migration.
                                // Yes! orders table migration has: $table->decimal('subtotal', 15, 2); $table->decimal('discount', 15, 2)->default(0); $table->decimal('total', 15, 2);
                                // Wait, the migration we viewed earlier did not show 'paid_amount', it showed 'subtotal', 'discount', 'total', 'payment_method', 'status'.
                                // Oh, the PRD model description table says: "transactions: grand_total, paid_amount, payment_status". But our local schema uses 'orders' with total, status.
                                // Let's check database/migrations/2026_07_15_131709_create_orders_table.php again. It doesn't have paid_amount, but we can update it or just clear the whole order.
                                // Let's check if the remaining payment is >= order->total. If so, mark as completed.
                                if ($remainingPayment >= $order->total) {
                                    $remainingPayment -= $order->total;
                                    $order->update([
                                        'status' => 'completed',
                                        'payment_method' => $data['payment_method'],
                                    ]);
                                } else {
                                    // Partial payment: for simplicity we can deduct from the order total OR we can keep it as is.
                                    // Let's deduct from the order total by updating the total, or since it's database audit trail,
                                    // we can just keep the order outstanding and decrease the remaining payment to 0.
                                    // Let's reduce order total to reflect the remaining debt on this invoice!
                                    $order->decrement('total', $remainingPayment);
                                    $remainingPayment = 0;
                                }
                            }
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran Berhasil')
                            ->body('Cicilan piutang senilai Rp ' . number_format($data['amount'], 0, ',', '.') . ' telah dicatat.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->getTotalOutstandingDebt() > 0),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
