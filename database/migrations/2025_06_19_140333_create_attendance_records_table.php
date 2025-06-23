<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id'); // Bắt buộc (nên không nullable)
            $table->date('date'); // Bắt buộc (ngày chấm công)

            $table->time('check_in')->nullable(); // Có thể null nếu chưa chấm công
            $table->time('check_out')->nullable(); // Có thể null nếu chưa ra
            $table->decimal('working_hours', 5, 2)->default(0); // Có thể tính tự động, để mặc định 0

            $table->enum('status', ['present', 'late', 'absent', 'leave', 'remote', 'holiday'])
                ->default('absent'); // Có giá trị mặc định là 'absent'

            $table->text('note')->nullable(); // Ghi chú có thể không cần
            $table->unsignedBigInteger('created_by')->nullable(); // Cho phép null

            $table->timestamps();

            // Một nhân viên chỉ được có 1 bản ghi trong 1 ngày
            $table->unique(['employee_id', 'date']);

            // Ràng buộc khóa ngoại
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
