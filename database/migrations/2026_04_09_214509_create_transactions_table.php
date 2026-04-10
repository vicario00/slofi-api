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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->string('description');
            $table->string('merchant')->nullable();
            $table->date('transacted_at');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('transfer_pair_id')->nullable();
            $table->foreign('transfer_pair_id')->references('id')->on('transactions')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'transacted_at']);
            $table->index(['payable_type', 'payable_id']);
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
