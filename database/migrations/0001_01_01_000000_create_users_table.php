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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('usertype')->default('user');
            $table->string('departemen')->nullable(); // Menambahkan kolom departemen
            $table->string('unit_work')->nullable();  // Menambahkan kolom unit_work
            $table->string('seksi')->nullable();      // Menambahkan kolom seksi
            $table->string('jabatan')->nullable();    // Kolom jabatan tetap ada
            $table->string('whatsapp_number')->nullable(); // Kolom nomor WhatsApp
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('departemen');
            $table->dropColumn('unit_work');
            $table->dropColumn('seksi');
            $table->dropColumn('jabatan');
            $table->dropColumn('whatsapp_number'); // Menghapus kolom nomor WhatsApp
        });

        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
