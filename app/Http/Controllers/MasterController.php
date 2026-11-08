<?php

namespace App\Http\Controllers;

use App\Models\Master;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['service' => ['nullable', Rule::in(array_keys(config('repair.services')))], 'q' => ['nullable', 'string', 'max:80'], 'sort' => ['nullable', 'in:experience,price']]);
        $query = Master::where('is_active', true)->when($filters['service'] ?? null, fn ($query, $value) => $query->whereJsonContains('services', $value))
            ->when($filters['q'] ?? null, fn ($query, $value) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$value.'%')->orWhere('district', 'like', '%'.$value.'%')));
        $query = ($filters['sort'] ?? 'experience') === 'price' ? $query->orderBy('base_price') : $query->orderByDesc('experience_years');

        return view('masters.index', ['masters' => $query->orderBy('id')->paginate(6)->withQueryString(), 'filters' => $filters]);
    }

    public function show(Master $master): View
    {
        abort_unless($master->is_active, 404);

        return view('masters.show', compact('master'));
    }
}
