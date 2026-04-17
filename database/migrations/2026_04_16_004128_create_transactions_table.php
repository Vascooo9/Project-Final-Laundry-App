<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'transfer']);
            $table->string('reference_number')->nullable();
            $table->timestamp('paid_at');
            $table->foreignId('received_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};