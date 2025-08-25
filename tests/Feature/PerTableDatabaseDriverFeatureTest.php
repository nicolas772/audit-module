<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserAudit;
use App\Models\Tenant;
use Database\Seeders\TenantSeeder;

class PerTableDatabaseDriverFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ejecutar seeder para asegurar que existan tenants
        $this->seed(TenantSeeder::class);
        // Setear currentTenantID con el primer tenant creado en la bbdd
        app()->instance('currentTenantId', Tenant::firstOrFail()->id);
    }

    /**
     * create action audit
     */
    public function test_it_audits_user_creation()
    {
        // Creat Usuario y autenticarlo para prueba
        $loggedUser = User::factory()->create();
        $this->actingAs($loggedUser);

        // Obtener Current Tenant ID
        $currentTenantId = Tenant::firstOrFail()->id;

        // Setear Hash
        $testHash = 'create-hash';
        request()->attributes->set('tx_hash', $testHash);

        // Crear Usuario
        $user = User::factory()->create();

        $this->assertDatabaseHas('users_audit', [
            'tenant_id' => $currentTenantId,
            'object_id' => $user->uuid,
            'type' => 1,
            'transaction_hash' => $testHash,
            'blame_id' => $loggedUser->uuid,
            'blame_user' => $loggedUser->full_name,
        ]);

        // Se obtiene registro de auditoría
        $audit = UserAudit::where('object_id', $user->uuid)->where('type', 1)->latest()->first();

        // Para creación, no deben haber campos en old_values pero si en new_values
        $this->assertEmpty($audit->diffs['old_values']);
        $this->assertNotEmpty($audit->diffs['new_values']);
    }

    /**
     * update action audit
     */
    public function test_it_audits_user_updating()
    {
        // Creat Usuario y autenticarlo para prueba
        $loggedUser = User::factory()->create();
        $this->actingAs($loggedUser);

        // Obtener Current Tenant ID
        $currentTenantId = Tenant::firstOrFail()->id;

        // Setear Hash
        $testHash = 'update-hash';
        request()->attributes->set('tx_hash', $testHash);

        // Crear Usuario y luego modificarlo
        $user = User::factory()->create();
        $oldNameUser = $user->full_name;

        //Update a nuevo usuario
        $newNameUser = 'Nombre actualizado';
        $user->update([
            'full_name' => $newNameUser,
        ]);

        $this->assertDatabaseHas('users_audit', [
            'tenant_id' => $currentTenantId,
            'object_id' => $user->uuid,
            'type' => 2,
            'transaction_hash' => $testHash,
            'blame_id' => $loggedUser->uuid,
            'blame_user' => $loggedUser->full_name,
        ]);

        // Se obtiene registro de auditoría
        $audit = UserAudit::where('object_id', $user->uuid)->where('type', 2)->latest()->first();
        
        // Para update, deben haber cambios tanto en old como new
        $this->assertNotEmpty($audit->diffs['old_values']);
        $this->assertNotEmpty($audit->diffs['new_values']);

        // Se valida que el antiguo y nuevo nombre se guarde correctamente en diffs
        $this->assertEquals($oldNameUser, $audit->diffs['old_values']['full_name']);
        $this->assertEquals($newNameUser, $audit->diffs['new_values']['full_name']);
    }

    /**
     * delete action audit
     */
    public function test_it_audits_user_deletion()
    {
        // Creat Usuario y autenticarlo para prueba
        $loggedUser = User::factory()->create();
        $this->actingAs($loggedUser);

        // Obtener Current Tenant ID
        $currentTenantId = Tenant::firstOrFail()->id;

        // Setear Hash
        $testHash = 'delete-hash';
        request()->attributes->set('tx_hash', $testHash);

        // Crear Usuario
        $user = User::factory()->create();

        // Eliminar a nuevo usuario
        $user->delete();

        $this->assertDatabaseHas('users_audit', [
            'tenant_id' => $currentTenantId,
            'object_id' => $user->uuid,
            'type' => 3,
            'transaction_hash' => $testHash,
            'blame_id' => $loggedUser->uuid,
            'blame_user' => $loggedUser->full_name,
        ]);

        // Se obtiene registro de auditoría
        $audit = UserAudit::where('object_id', $user->uuid)->where('type', 3)->latest()->first();
        
        // Para delete, deben haber valores en old_values pero no en new_values
        $this->assertNotEmpty($audit->diffs['old_values']);
        $this->assertEmpty($audit->diffs['new_values']);
    }
}
