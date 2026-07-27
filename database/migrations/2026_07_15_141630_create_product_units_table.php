<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->boolean('is_base')->default(false); // satuan dasar/induk
            $table->decimal('conversion_rate', 15, 4)->default(1); // 1 unit ini = X satuan dasar
            $table->decimal('selling_price_retail', 15, 2)->default(0);    // harga jual eceran
            $table->decimal('selling_price_wholesale', 15, 2)->default(0); // harga jual grosir
            $table->timestamps();

            $table->unique(['product_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
