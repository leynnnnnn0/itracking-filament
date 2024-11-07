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
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('unit');
            $table->integer('quantity');
            $table->integer('missing')->default(0);
            $table->integer('expired')->default(0);
            $table->integer('used')->default(0);
            $table->integer('recently_added')->default(0);
            $table->integer('total')->default(0);
            $table->date('expiry_date')->nullable();
            $table->boolean('is_consumable')->default(false);
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
