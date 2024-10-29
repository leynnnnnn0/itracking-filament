<?php

use App\BorrowStatus;
use App\Models\Equipment;
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
        Schema::create('borrowed_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Equipment::class)->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('borrower_first_name');
            $table->string('borrower_last_name');
            $table->string('borrower_email')->nullable();
            $table->string('borrower_phone_number')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->integer('quantity_returned')->default(0);
            $table->integer('quantity_missing')->default(0);
            $table->integer('total_quantity_returned')->default(0);
            $table->integer('total_quantity_missing')->default(0);
            $table->enum('status', BorrowStatus::values())->default(BorrowStatus::BORROWED->value);
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowed_equipment');
    }
};
