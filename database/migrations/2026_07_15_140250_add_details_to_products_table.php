<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('category_id')->constrained('units')->nullOnDelete();
            $table->decimal('cost_price', 15, 2)->after('unit_id')->default(0);
            $table->renameColumn('price', 'selling_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('selling_price', 'price');
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'cost_price']);
        });
    }
};
