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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名前');
            $table->string('name_kana')->comment('ふりがな');
            $table->integer('age')->comment('年齢');
            $table->string('email')->comment('メールアドレス');
            $table->string('stream_email')->nullable()->comment('配信用メールアドレス');
            $table->integer('company_id')->comment('会社ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
