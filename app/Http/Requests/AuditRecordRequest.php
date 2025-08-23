<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tables' => ['required', 'array'],
            'tables.*' => ['string', 'in:users_audit,courses_audit,course_enrollments_audit'], // pendiente, validar con AuditTableMap
            'type' => 'nullable|in:created,updated,deleted',
            'object_id' => 'nullable|uuid',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
