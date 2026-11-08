<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::authorize($this->route('article') ? 'update' : 'create', $this->route('article') ?? Article::class)->allowed();
    }

    public function rules(): array
    {
        return ['title' => ['required', 'string', 'max:255'], 'excerpt' => ['required', 'string', 'max:500'], 'body' => ['required', 'string', 'max:50000'], 'category' => ['required', 'string', 'max:80'], 'status' => ['required', 'in:draft,published'], 'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072']];
    }
}
