<?php

use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

// Redirect Root to Login / Dashboard
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Area Anggota (Member)
    Route::middleware(['approved', 'role:member'])->group(function () {
        Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');
        Route::get('/my-badge', [MemberController::class, 'badge'])->name('member.badge');
        Route::post('/my-badge/regenerate', [MemberController::class, 'regenerateToken'])->name('member.badge.regenerate');
        Route::get('/my-history', [MemberController::class, 'history'])->name('member.history');
        Route::get('/profile', [MemberController::class, 'showProfile'])->name('member.profile');
        Route::post('/profile', [MemberController::class, 'updateProfile'])->name('member.profile.update');
    });

    // Area Pengurus (Admin & Sekretaris)
    Route::prefix('admin')->name('admin.')->middleware(['role:admin,secretary'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/sessions', function () {
            return view('admin.sessions.index');
        })->name('sessions.index');

        // Manajemen Anggota & Approval
        Route::get('/members', [AdminMemberController::class, 'index'])->name('members.index');
        Route::get('/members/pending', [AdminMemberController::class, 'pending'])->name('members.pending');
        Route::post('/members/{id}/approve', [AdminMemberController::class, 'approve'])->name('members.approve');
        Route::post('/members/{id}/reject', [AdminMemberController::class, 'reject'])->name('members.reject');

        Route::get('/reports', function () {
            return view('admin.reports.index');
        })->name('reports.index');
    });
});
