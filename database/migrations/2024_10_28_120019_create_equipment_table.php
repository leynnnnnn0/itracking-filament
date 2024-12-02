<?php

use App\Enum\EquipmentStatus;
use App\Enum\Unit;
use App\Models\AccountableOfficer;
use App\Models\Fund;
use App\Models\OperatingUnitProject;
use App\Models\OrganizationUnit;
use App\Models\PersonalProtectiveEquipment;
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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnel')->cascadeOnDelete();
            $table->foreignId('accountable_officer_id')->constrained('personnel')->cascadeOnDelete();
            $table->foreignIdFor(Fund::class)->constrained();
            $table->foreignIdFor(OperatingUnitProject::class)->constrained();
            $table->foreignIdFor(OrganizationUnit::class)->constrained();
            $table->string('property_number')->unique();
            $table->integer('quantity');
            $table->integer('quantity_borrowed')->default(0);
            $table->integer('quantity_available')->default(0);
            $table->integer('quantity_missing')->default(0);
            $table->integer('quantity_condemned')->default(0);
            $table->string('unit');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('date_acquired');
            $table->string('estimated_useful_time');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', EquipmentStatus::values());
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
