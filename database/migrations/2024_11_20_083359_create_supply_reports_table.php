<?php

use App\Enum\SupplyReportAction;
use App\Models\Supply;
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
        Schema::create('supply_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Supply::class)->constrained()->cascadeOnDelete();
            $table->string('handler');
            $table->integer('quantity');
            $table->integer('quantity_returned')->default(0);
            $table->text('remarks')->nullable();
            $table->date('date_acquired');
            $table->enum('action', SupplyReportAction::values());
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_reports');
    }
};
