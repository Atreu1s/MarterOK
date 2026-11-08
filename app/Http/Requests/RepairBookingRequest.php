<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepairBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()->is_blocked;
    }

    public function rules(): array
    {
        return [
            'submission_key' => ['required', 'uuid'],
            'service' => ['required', Rule::in(array_keys(config('repair.services')))],
            'master_id' => ['nullable', 'integer', Rule::exists('masters', 'id')->where('is_active', true)],
            'device' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:15', 'max:4000'],
            'contact_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'regex:/^\+?[0-9]{10,15}$/'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'consent' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('phone'))) {
            $this->merge(['phone' => str_replace([' ', '(', ')', '-'], '', $this->input('phone'))]);
        }
    }

    public function messages(): array
    {
        return ['description.min' => 'Опишите неисправность хотя бы в 15 символах.', 'phone.regex' => 'Введите от 10 до 15 цифр телефона с кодом страны.', 'consent.accepted' => 'Нужно согласие на обработку заявки.', 'master_id.exists' => 'Этот мастер сейчас не принимает заявки.', 'photos.*.max' => 'Каждая фотография должна быть не больше 4 МБ.'];
    }
}
