<?php

use App\Enum\MissingStatus;
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
        Schema::create('missing_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowed_equipment_id')->nullable()->constrained('borrowed_equipment')->cascadeOnDelete();
            $table->foreignIdFor(Equipment::class)->constrained()->cascadeOnDelete();
            $table->enum('status', MissingStatus::values());
            $table->integer('quantity');
            $table->integer('quantity_found')->default(0);
            $table->text('description')->nullable();
            $table->string('reported_by');
            $table->date('reported_date');
            $table->text('remarks')->nullable();
            $table->boolean('is_condemned')->default(false);
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_equipment');
    }
};
