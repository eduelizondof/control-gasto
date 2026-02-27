<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConceptController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\EnsureUserBelongsToGroup;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Groups
    Route::resource('groups', GroupController::class)->except(['show']);

    // Invitations (user-scoped)
    Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('invitations/{group}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('invitations/{group}/reject', [InvitationController::class, 'reject'])->name('invitations.reject');

    // Group-scoped invitation sending
    Route::get('groups/{group}/invite', [InvitationController::class, 'create'])->name('groups.invite');
    Route::post('groups/{group}/invite', [InvitationController::class, 'store'])->name('groups.invite.store');

    // Group-scoped resources
    Route::prefix('groups/{group}')
        ->middleware(EnsureUserBelongsToGroup::class)
        ->group(function () {
            Route::resource('accounts', AccountController::class)->except(['show']);
            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('concepts', ConceptController::class)->except(['show']);
            Route::resource('transactions', TransactionController::class)->except(['show']);
            Route::resource('budgets', BudgetController::class)->except(['show'])
                ->parameter('budgets', 'budget');
            Route::post('budgets/{budget}/items', [BudgetController::class, 'addItem'])->name('budgets.add-item');
            Route::patch('budgets/{budget}/items/{item}', [BudgetController::class, 'updateItem'])->name('budgets.update-item');
            Route::delete('budgets/{budget}/items/{item}', [BudgetController::class, 'deleteItem'])->name('budgets.delete-item');
            Route::resource('debts', DebtController::class)->except(['show']);
            Route::resource('reminders', ReminderController::class)->except(['show']);
        });
});

require __DIR__ . '/auth.php';
