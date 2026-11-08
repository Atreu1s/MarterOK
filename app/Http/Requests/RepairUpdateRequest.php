<?php

namespace App\Http\Requests;

use App\Models\RepairBooking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class RepairUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::authorize('moderate')->allowed();
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(array_keys(RepairBooking::STATUSES))], 'master_id' => ['nullable', 'integer', Rule::exists('masters', 'id')->where('is_active', true)], 'quoted_price' => ['nullable', 'integer', 'min:1', 'max:1000000'], 'public_message' => ['nullable', 'string', 'max:2000'], 'internal_note' => ['nullable', 'string', 'max:4000']];
    }
}
