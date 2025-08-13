<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $table->uuid('tenant_id')->nullable()->after('uuid');
            }
        });

        // Unico e índice para FK a otras tablas
        Schema::table('users', function (Blueprint $table) {
            $table->unique('uuid', 'users_uuid_unique');
        });

        // FK a tenants e indexacion tenant-created_at
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete(); #Null on delete para no perder registros historicos
            $table->index(['tenant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropIndex(['tenant_id','created_at']);
                $table->dropColumn('tenant_id');
            }
            if (Schema::hasColumn('users', 'uuid')) {
                $table->dropUnique('users_uuid_unique');
                $table->dropColumn('uuid');
            }
        });
    }
};
