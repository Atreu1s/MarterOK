<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// Лабы 1–2: публичные страницы, данные из JSON и галерея.
Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/galery/{index}', [MainController::class, 'galery'])->whereNumber('index')->name('galery');

Route::get('/masters', [MasterController::class, 'index'])->name('masters.index');
Route::get('/masters/{master}', [MasterController::class, 'show'])->name('masters.show');

// Лабы 4–5: просмотр статей доступен всем.
Route::resource('articles', ArticleController::class)
    ->only(['index', 'show'])
    ->whereNumber('article');

// Лабы 3 и 6: регистрация и вход только для гостей.
Route::middleware('guest')->group(function (): void {
    Route::get('/signin', [AuthController::class, 'create'])->name('register');
    Route::post('/signin', [AuthController::class, 'registration'])->middleware('throttle:8,1')->name('registration');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:6,1')->name('authenticate');
});

// Лаба 6: всё ниже доступно только после входа.
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])
        ->whereUuid('notification')
        ->name('notifications.read');

    // Лабы 5 и 7: CRUD статей, права проверяет ArticlePolicy.
    Route::resource('articles', ArticleController::class)
        ->except(['index', 'show'])
        ->whereNumber('article');

    // Лаба 9: комментарии и их модерация.
    Route::post('/articles/{article}/comments', [CommentController::class, 'store'])
        ->whereNumber('article')
        ->middleware('throttle:10,1')
        ->name('comments.store');
    Route::get('/moderation', [CommentController::class, 'index'])->name('comments.moderation');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');
    Route::post('/moderation/bulk', [CommentController::class, 'bulk'])->name('comments.bulk');

    // Дополнительно: заявки на ремонт.
    Route::get('/account', [RepairController::class, 'account'])->name('account');
    Route::get('/request', [RepairController::class, 'create'])->name('repairs.create');
    Route::post('/request', [RepairController::class, 'store'])->middleware('throttle:10,1')->name('repairs.store');
    Route::whereNumber(['repair', 'index'])->group(function (): void {
        Route::get('/repairs/{repair}', [RepairController::class, 'show'])->name('repairs.show');
        Route::get('/repairs/{repair}/photos/{index}', [RepairController::class, 'photo'])->name('repairs.photo');
        Route::patch('/repairs/{repair}/cancel', [RepairController::class, 'cancel'])->name('repairs.cancel');
        Route::patch('/repairs/{repair}/approve', [RepairController::class, 'approveQuote'])->name('repairs.approve');
    });

    // Дополнительно: рабочее место модератора.
    Route::prefix('workspace')->name('workspace')->controller(WorkspaceController::class)->group(function (): void {
        Route::get('/', 'index');
        Route::get('/repairs', 'repairs')->name('.repairs');
        Route::patch('/repairs/{repair}', 'updateRepair')->name('.repairs.update');
        Route::get('/masters', 'masters')->name('.masters');
        Route::get('/masters/create', 'createMaster')->name('.masters.create');
        Route::post('/masters', 'storeMaster')->name('.masters.store');
        Route::get('/masters/{master}/edit', 'editMaster')->name('.masters.edit');
        Route::put('/masters/{master}', 'updateMaster')->name('.masters.update');
    });
});
