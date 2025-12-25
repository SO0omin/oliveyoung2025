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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->integer('detail_category_id')->nullable();
            $table->string('name',50)->nullable();
            $table->integer('price')->nullable();
            $table->integer('jaego')->nullable();
            $table->string('pic',255)->nullable();
            $table->unsignedBigInteger('company_id')->nulllable();

            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
