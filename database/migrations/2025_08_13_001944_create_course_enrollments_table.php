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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('user_id');   // -> users.uuid
            $table->uuid('course_id'); // -> courses.id
            $table->date('enrolled_at')->nullable();
            $table->boolean('isCompleted')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();

            // FK a users.uuid (no a users.id por uso de tabla users por defecto)
            $table->foreign('user_id')->references('uuid')->on('users')->cascadeOnDelete();

            $table->unique(['tenant_id','user_id','course_id']);
            $table->index(['tenant_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
