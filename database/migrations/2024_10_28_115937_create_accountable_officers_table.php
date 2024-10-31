<?php

use App\Enum\Gender;
use App\Models\Office;
use App\Models\Position;
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
        Schema::create('accountable_officers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignIdFor(Position::class)->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', Gender::values());
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('remarks')->nullable();
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accountable_officers');
    }
};
