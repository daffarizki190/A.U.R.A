<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('action'); // created, updated, deleted
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // Enable RLS for Supabase Security
        DB::statement('ALTER TABLE "activity_logs" ENABLE ROW LEVEL SECURITY;');
        DB::statement('ALTER TABLE "activity_logs" FORCE ROW LEVEL SECURITY;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
