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
        Schema::table('monthly_workdays', function (Blueprint $table) {
            $table->string('file')->nullable()->after('salary'); // thêm sau cột salary (nếu có)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_workdays', function (Blueprint $table) {
            $table->dropColumn('file');
        });
    }
};
