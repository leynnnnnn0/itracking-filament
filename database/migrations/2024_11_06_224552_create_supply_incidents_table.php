<?php

use App\Enum\SupplyIncidentStatus;
use App\Models\Supply;
use App\SupplyIncidents;
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
        Schema::create('supply_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Supply::class)->constrained()->cascadeOnDelete();
            $table->enum('type', SupplyIncidents::values());
            $table->integer('quantity');
            $table->text('remarks')->nullable();
            $table->date('incident_date');
            $table->enum('status', SupplyIncidentStatus::values())->default(SupplyIncidentStatus::ACTIVE->value);
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_incidents');
    }
};
