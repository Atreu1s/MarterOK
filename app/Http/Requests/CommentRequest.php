<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()->is_blocked;
    }

    public function rules(): array
    {
        return ['body' => ['required', 'string', 'min:3', 'max:3000']];
    }
}
