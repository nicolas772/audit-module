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
        Schema::create('course_audit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('tenant_id');
            $table->uuid('object_id'); // courses.id
            $table->unsignedSmallInteger('type');
            $table->jsonb('diffs')->nullable();
            $table->string('transaction_hash')->nullable();
            $table->string('blame_id')->nullable();
            $table->string('blame_user')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Solo created_at, no es necesario updated_at

            $table->index(['tenant_id','created_at']);
            $table->index(['tenant_id','type','created_at']);
            $table->index(['tenant_id','object_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_audit');
    }
};
