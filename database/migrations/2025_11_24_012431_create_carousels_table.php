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
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();

            $table->string('title');        // 캐로셀 제목
            $table->text('content')->nullable(); // 설명 또는 내용
            $table->string('link_url')->nullable(); // 링크 URL 또는 라우트 이름
            $table->string('pic')->nullable();      // 이미지 경로
            $table->unsignedBigInteger('event_id')->nullable(); // 연결된 이벤트

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousels');
    }
};
