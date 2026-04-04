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
        Schema::create('berita_acaras', function (Blueprint $table) {
            $table->id();
            $table->string('ba_number')->unique();
            $table->string('ba_type');
            $table->date('incident_date');
            $table->string('customer_name');
            $table->string('license_plate')->nullable();
            $table->text('chronology');
            $table->enum('status', ['Draft', 'Submitted', 'Processed', 'Done', 'Rejected'])->default('Draft');
            $table->foreignId('pic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_acaras');
    }
};
