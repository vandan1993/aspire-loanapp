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
        Schema::create('repayment', function (Blueprint $table) {
            $table->id();
            $table->string('repayment_ref_number')->unique();
            $table->integer('loan_id');
            $table->string('loan_ref_number');
            $table->decimal('repayment_amount' , 12,4);
            $table->date('repayment_date');
            $table->enum('status' , ['PENDING' , 'PAID']);  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayment');
    }
};
