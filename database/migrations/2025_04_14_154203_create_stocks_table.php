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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('externalId');
            $table->string('refOEM')->nullable();
            $table->float('priceOEM')->nullable();
            $table->string('description');
            $table->integer('externalMakeId');
            $table->integer(('externalModelId'));
            $table->integer('externalPartId');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
