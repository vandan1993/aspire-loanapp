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
        Schema::create('loan', function (Blueprint $table) {
            $table->id();
            $table->string('loan_ref_number')->unique();
            $table->integer('user_id');
            $table->decimal('loan_amount' , 12,4)->default(0)->unsigned();
            $table->integer('term')->unsigned();
            $table->enum('status' , ['PENDING' , 'APPROVED' , 'PAID']);
            $table->date('loan_creation_date');
            $table->decimal('loan_outstanding_amount' , 12,4)->default(0)->unsigned();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan');
    }
};
