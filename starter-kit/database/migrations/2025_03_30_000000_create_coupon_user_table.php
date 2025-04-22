<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('coupon_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->string('line_udid');   // เพื่อบันทึกว่า user นี้ (ระบุด้วย line_udid)
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            // เพิ่ม foreign key ถ้าต้องการ
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('coupon_user');
    }
};
