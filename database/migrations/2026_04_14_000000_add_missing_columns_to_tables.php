<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'face_registered')) {
                $table->boolean('face_registered')->default(0);
            }
        });

        // Attendance Sessions Table
        Schema::table('attendance_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_sessions', 'classroom_lat')) {
                $table->decimal('classroom_lat', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('attendance_sessions', 'classroom_lng')) {
                $table->decimal('classroom_lng', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('attendance_sessions', 'radius_meters')) {
                $table->integer('radius_meters')->default(30);
            }
        });

        // Attendance Records Table
        Schema::table('attendance_records', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_records', 'student_lat')) {
                $table->decimal('student_lat', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('attendance_records', 'student_lng')) {
                $table->decimal('student_lng', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('attendance_records', 'distance_meters')) {
                $table->float('distance_meters')->nullable();
            }
            if (!Schema::hasColumn('attendance_records', 'liveness_passed')) {
                $table->boolean('liveness_passed')->default(0);
            }
            if (!Schema::hasColumn('attendance_records', 'rejection_reason')) {
                $table->string('rejection_reason')->nullable();
            }
            // Modify enum for status to support rejected, wait doing raw DB statement is safer for enums usually, but let's just make it string if schema builder doesn't support changing enum easily.
            // Actually, doctrine/dbal is needed to change column type. 
            // Better to just rename the column or drop and recreate if it's empty. Since it's a new system, I'll let Laravel handle it if possible.
        });
        
        // Change enum via DB statement since change() for enums is finicky
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendance_records MODIFY COLUMN status ENUM('present', 'rejected') DEFAULT 'present'");
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_id', 'face_registered']);
        });

        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropColumn(['classroom_lat', 'classroom_lng', 'radius_meters']);
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['student_lat', 'student_lng', 'distance_meters', 'liveness_passed', 'rejection_reason']);
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendance_records MODIFY COLUMN status ENUM('present') DEFAULT 'present'");
        });
    }
};
