<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\GeneralLedger;

class FinancialJournalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        ChartOfAccount::create(['account_code' => '1101', 'name' => 'Kas & Bank', 'type' => 'asset']);
        ChartOfAccount::create(['account_code' => '6101', 'name' => 'Biaya Operasional', 'type' => 'expense']);
    }

    public function test_expense_creation_generates_auto_journal()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user);

        // Ensure user has wallet balance since Expense withdraws from wallet (amount * 100)
        $user->deposit(150000000);

        $expense = Expense::create([
            'expense_date' => now(),
            'amount' => 150000,
            'category' => 'operasional',
            'description' => 'Listrik Bulan Ini',
            'user_id' => $user->id,
        ]);

        // Assert Expense created
        $this->assertDatabaseHas('expenses', [
            'amount' => 150000,
            'description' => 'Listrik Bulan Ini'
        ]);

        // Assert Journal created: Debit Biaya Operasional (6101), Credit Kas (1101)
        $this->assertDatabaseHas('general_ledgers', [
            'chart_of_account_id' => ChartOfAccount::where('account_code', '6101')->first()->id,
            'debit' => 150000,
            'credit' => 0,
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
        ]);

        $this->assertDatabaseHas('general_ledgers', [
            'chart_of_account_id' => ChartOfAccount::where('account_code', '1101')->first()->id,
            'debit' => 0,
            'credit' => 150000,
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
        ]);

        // Assert user wallet has been deducted
        $this->assertEquals(135000000, $user->balanceInt); // 150,000,000 - 15,000,000
    }
}
