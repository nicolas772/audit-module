<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AuditRecordRequest;
use Illuminate\Support\Facades\Log;
use App\Services\AuditRecordService;

class AuditRecordController extends Controller
{
    public function __construct(private AuditRecordService $service) {}
    /**
     * Display a listing of the resource.
     */
    public function index(AuditRecordRequest $request): JsonResponse
    {
        try {
            $records = $this->service->getAuditRecords($request);
            return response()->json($records);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            Log::error('Error en AuditRecordController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Ocurrió un error al obtener los registros de auditoría.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
