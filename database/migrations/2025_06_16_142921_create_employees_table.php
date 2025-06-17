<?php

use App\Models\Department;
use App\Models\EducationLevel;
use App\Models\EmploymentStatus;
use App\Models\Position;
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Position::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Department::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(EducationLevel::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(EmploymentStatus::class)->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('full_name');
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('cccd')->nullable();
            $table->date('cccd_issued_date')->nullable();
            $table->date('university_start_date')->nullable();
            $table->date('university_end_date')->nullable();
            $table->date('resignation_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
