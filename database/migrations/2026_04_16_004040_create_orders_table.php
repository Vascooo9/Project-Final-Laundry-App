<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();         // e.g. LDR-20240101-001
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Karyawan input
            $table->enum('delivery_type', ['pickup', 'delivery'])->default('pickup');
            $table->text('delivery_address')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->date('estimated_done');                   // Perkiraan selesai
            $table->enum('status', [
                'pending',      // Baru masuk
                'processing',   // Sedang dicuci
                'done',         // Selesai
                'picked_up'     // Sudah diambil
            ])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'transfer'])->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};