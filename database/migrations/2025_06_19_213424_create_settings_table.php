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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();     // Tên công ty
            $table->string('logo')->nullable();             // Logo (đường dẫn ảnh)
            $table->string('favicon')->nullable();          // Icon trang
            $table->string('address')->nullable();          // Địa chỉ
            $table->string('hotline')->nullable();          // Số điện thoại
            $table->string('email')->nullable();            // Email liên hệ
            $table->text('copyright')->nullable();        // Chân trang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
