<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) return;

        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        $expenseCategories = array_keys(Expense::categories());

        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
            
            Expense::create([
                'user_id' => $users->random()->id,
                'category' => $expenseCategories[array_rand($expenseCategories)],
                'description' => 'Biaya operasional ' . $date->format('F Y'),
                'amount' => rand(100000, 5000000),
                'expense_date' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
