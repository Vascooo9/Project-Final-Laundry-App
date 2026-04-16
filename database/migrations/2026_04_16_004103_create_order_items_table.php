<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('restrict');
            $table->decimal('quantity', 8, 2);               // kg atau jumlah item
            $table->decimal('price_per_unit', 10, 2);        // Harga saat transaksi
            $table->decimal('subtotal', 10, 2);
            $table->string('item_note')->nullable();          // e.g. "Seprai motif bunga"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
