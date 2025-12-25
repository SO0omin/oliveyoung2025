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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->string('uid',20)->unique();
            $table->string('pwd',255);
            $table->string('name',20);
            $table->string('tel',11)->nullable();
            $table->enum('type', ['super_admin', 'company_admin', 'staff'])->default('staff');
            $table->unsignedBigInteger('company_id')->nullable();

            $table->timestamps();
            //외래키 연결
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
