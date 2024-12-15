<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
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
    // Route::get('/user-dashboard', [UserController::class, 'display_info'])->name('user-dashboard');
    // Route::get('/user-dashboard/search', [UserController::class, 'search'])->name('user.search');



    
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'users'])->name('admin.users');
        Route::delete('/admin/{user}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
        Route::put('/update-user-role/{userId}', [AdminController::class, 'updateUserRole'])->name('admin.updateUserRole');
        Route::get('/admin/dashboard', [AdminController::class, 'totalIcecreams'])->name('admin.dashboard');





        // Route::get('/admin-dashboard', [AdminController::class, 'users'])->name('admin-dashboard');
        // Route::put('/admin-users/{user}', [AdminController::class, 'updateUserRole'])->name('admin.updateUserRole');
   });


    Route::middleware(['employee'])->group(function () {
        Route::get('/employee',[EmployeeController::class, 'icecream_inventory'])->name('employee.icecream');
        Route::put('/employee/update/{id}', [EmployeeController::class, 'updateIcecream'])-> name ('employee.update');
        Route::post('/employee/add', [EmployeeController::class, 'addIcecream'])->name('employee.add');



        // Route::get('/librarian-dashboard', [LibrarianController::class, 'display_info'])->name('librarian-dashboard');
        // Route::get('/librarian-dashboard/search', [LibrarianController::class, 'search'])->name('librarian.search');
    });


    
});













Route::get('/customer', function () {
    return Inertia::render('Customer');
})->name('customer');

//route for data fetch





























Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
