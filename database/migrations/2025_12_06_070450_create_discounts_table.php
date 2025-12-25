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
        Schema::create('discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->integer('sale_price');  // 세일가 직접 입력
            $table->decimal('discount_percent', 5, 2)->nullable(); // 퍼센트 자동 계산용 (선택)
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('is_active')->default(1);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')->on('items')
                ->onDelete('cascade');

            // 인덱스
            $table->index('item_id');
            $table->index(['is_active', 'start_at', 'end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
