<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::prefix(LaravelLocalization::setLocale())
    ->middleware(['localeCookieRedirect'])
    ->group(function () {
        require __DIR__ . '/auth.php';

        Route::middleware('auth')->group(function () {
            Route::resource('categories', CategoryController::class)->only([
                'index',
                // 'create',
                'store',
            ]);

            Route::middleware('can:see-category,category')->group(function () {
                Route::resource(
                    'categories',
                    CategoryController::class
                )->except(['index', 'create', 'store']);

                // projects
                // Route::get('c/{category}/projects', [
                //     ProjectController::class,
                //     'index',
                // ])->name('categories.show');
                Route::resource(
                    'c/{category}/projects',
                    ProjectController::class
                )->only([
                    // 'index',
                    // 'create',
                    'store',
                ]);

                Route::resource(
                    'c/{category}/projects',
                    ProjectController::class
                )->only(['edit', 'update', 'destroy']);
            });

            Route::prefix('c/{category}')
                ->controller(ProjectController::class)
                ->name('projects.')
                ->group(function () {
                    Route::get('projects/{project}', 'show')->name('show');

                    Route::post('/{project}/invite', 'invite')
                        ->name('invite')
                        ->can('update', 'project');
                    Route::delete('/{project}/invite', 'refuse')
                        ->name('refuse')
                        ->can('view', 'project');
                });

            Route::controller(ProjectController::class)
                ->name('projects.')
                ->prefix('/projects')
                ->group(function () {
                    Route::get('create/{category?}', 'create')
                        ->name('create')
                        ->can('see-category-if-present,category');

                    // add last because it will catch anything
                    Route::get('{project_tab}', 'index')->name('index');
                });

            // tasks
            Route::middleware('can:view,project')
                ->prefix('c/{category}/projects/{project}')
                ->group(function () {
                    Route::resource('/tasks', TodoController::class)->only([
                        'index',
                        'store',
                        'update',
                        'destroy',
                    ]);

                    // complete
                    Route::put('/tasks/{task}/toggle', [
                        TodoController::class,
                        'toggle',
                    ])->name('tasks.toggle');
                });
        });
    });
