<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CustomerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});




Route::middleware(['auth', 'setDB'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render(
            'Dashboard'
        );
    })->name('dashboard');
    Route::get('/customers', [CustomerController::class, 'display_info'])->name('customer.users');
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customer.search');





    Route::middleware(['admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'users'])->name('admin.users');
        route::post('/admin', [AdminController::class, 'refresh'])->name('admin.refresh');
        Route::delete('/admin/{user}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
        Route::put('/update-user-role/{userId}', [AdminController::class, 'updateUserRole'])->name('admin.updateUserRole');
    });


    Route::middleware(['employee'])->group(function () {
        Route::get('/employee', [EmployeeController::class, 'icecream_inventory'])->name('employee.icecream');
        Route::post('/employee/update/{id}', [EmployeeController::class, 'updateIcecream'])->name('employee.update');
        Route::post('/employee/add', [EmployeeController::class, 'addIcecream'])->name('employee.add');
        Route::post('/admin/update/{icecream_id}', [EmployeeController::class, 'updateStock'])->name('employee.update');
        Route::post('/admin/delete/{id}', [EmployeeController::class, 'deleteIcecream'])->name('employee.delete');



        // Route::get('/librarian-dashboard/search', [LibrarianController::class, 'search'])->name('librarian.search');
    });
});










//route for data fetch





























Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
