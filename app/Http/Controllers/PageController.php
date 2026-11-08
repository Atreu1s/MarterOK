<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contacts(): View
    {
        // Лаба 1: массив данных передаётся в шаблон методом view.
        $contacts = [
            'Адрес' => config('site.address'),
            'Телефон' => config('site.phone'),
            'Почта' => config('site.email'),
            'Время работы' => config('site.hours'),
        ];

        return view('pages.contacts', compact('contacts'));
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }
}
