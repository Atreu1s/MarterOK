<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.signin');
    }

    public function registration(RegistrationRequest $request): JsonResponse|RedirectResponse
    {
        $user = User::create($request->safe()->only(['name', 'email', 'password']));
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Регистрация завершена.',
                'user' => $user->only('id', 'name', 'email'),
                'redirect' => route('login'),
            ], 201);
        }

        return redirect()->route('login')->with('success', 'Аккаунт создан. Теперь можно войти.');
    }

    public function login(): View
    {
        return view('auth.login');
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->validated() + ['is_blocked' => false])) {
            throw ValidationException::withMessages(['email' => 'Неверный email или пароль.']);
        }
        $request->session()->regenerate();

        // Токены прошлых сессий, которые закончились без выхода, больше не нужны.
        $user = $request->user();
        $user->tokens()
            ->where('name', 'web-session')
            ->where('created_at', '<', now()->subMinutes((int) config('session.lifetime')))
            ->delete();

        $token = $user->createToken('web-session');
        $request->session()->put('auth_token_id', $token->accessToken->id);

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user?->currentAccessToken() instanceof PersonalAccessToken) {
            $user->currentAccessToken()->delete();
        }
        if ($tokenId = $request->session()->get('auth_token_id')) {
            $user?->tokens()->whereKey($tokenId)->delete();
        }
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::forgetGuards();

        return redirect()->route('home')->with('success', 'Вы вышли из аккаунта.');
    }
}
