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
        Schema::create('asset_findings', function (Blueprint $table) {
            $table->id();
            $table->string('finding_code')->unique();
            $table->date('finding_date');
            $table->string('location');
            $table->string('asset_type');
            $table->text('description');
            $table->string('photo')->nullable();
            $table->string('reporter')->nullable();
            $table->enum('status', ['Open', 'On Progress', 'Pending', 'Done'])->default('Open');
            $table->foreignId('pic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('estimated_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_findings');
    }
};
