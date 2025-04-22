<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_users', function (Blueprint $table) {
            $table->id(); // Primary Key อัตโนมัติ

            // 1. line_udid
            $table->string('line_udid')->nullable();

            // 2. line_pic
            $table->string('line_pic')->nullable();

            // 3. line_name
            $table->string('line_name')->nullable();

            // 4. name
            $table->string('name')->nullable();

            // 5. phone
            $table->string('phone')->nullable();

            // 6. email
            $table->string('email')->nullable();

            // 7. สถานะการยืนยัน otp (ตัวอย่างเป็น boolean ใช้ true/false)
            $table->boolean('otp_verified')->default(false);

            // 8. style (1 คนมีได้หลาย style) - เก็บเป็น JSON
            $table->json('styles')->nullable();

            // 9. type (1 คนมีได้หลาย type) - เก็บเป็น JSON
            $table->json('types')->nullable();

            // 10. ลิ้งข้อมูลแต่ละข้อที่ Quiz
            $table->string('quiz_link')->nullable();

            // 11. สถานะ เปิดใช้/ปิดใช้ (is_active)
            $table->boolean('is_active')->default(true);

            // 12. coin
            $table->integer('coin')->default(0);

            // 13. จำนวนครั้งทำ quiz
            $table->integer('quiz_attempts')->default(0);

            // เพิ่ม timestamps สำหรับเก็บ created_at, updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_users');
    }
}
