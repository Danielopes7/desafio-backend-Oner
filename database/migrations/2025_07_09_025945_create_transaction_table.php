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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payer_id')->nullable()->constrained('users');
            $table->foreignId('payee_id')->constrained('users');
            $table->enum('type', ['transfer', 'withdraw', 'deposit', 'refund']);
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->boolean('is_refunded')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
