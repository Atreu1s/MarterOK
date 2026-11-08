<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::authorize('moderate')->allowed();
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:100'], 'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('masters')->ignore($this->route('master'))], 'title' => ['required', 'string', 'max:160'], 'bio' => ['required', 'string', 'min:30', 'max:4000'], 'services' => ['required', 'array', 'min:1', 'max:6'], 'services.*' => ['required', 'distinct', Rule::in(array_keys(config('repair.services')))], 'experience_years' => ['required', 'integer', 'min:1', 'max:50'], 'base_price' => ['required', 'integer', 'min:0', 'max:1000000'], 'district' => ['required', 'string', 'max:100'], 'schedule' => ['required', 'string', 'max:160'], 'is_active' => ['required', 'boolean']];
    }
}
