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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('address_zipcode', 20)->nullable();
            $table->string('address_street', 255)->nullable();
            $table->string('address_number', 20)->nullable();
            $table->string('address_complement', 255)->nullable();
            $table->string('address_district', 255)->nullable();
            $table->string('address_city', 255)->nullable();
            $table->string('address_state', 2)->nullable();
            $table->integer('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
