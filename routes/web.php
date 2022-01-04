<?php

use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BuildNumberController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Auth::routes();
Route::group(['namespace' => 'Auth'], function () {
    Route::get('/password', [ChangePasswordController::class, 'index']);
    Route::post('/password', [ChangePasswordController::class, 'changePassword'])->name('change_password');
    Route::get('/password_remind', [ForgotPasswordController::class, 'index'])->name('password_remind');
    Route::post('/password_remind', [ForgotPasswordController::class, 'password']);
    Route::get('reset_password/{token}', [ResetPasswordController::class, 'index'])->name('reset_password');
    Route::post('reset_password', [ResetPasswordController::class, 'reset'])->name('reset_password.update');
});
Route::group(['middleware' => 'first-login'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::delete('apps/{app}/members/{member}', [ApplicationController::class, 'removeMember'])->name('apps.remove-member');
    Route::get('apps/{app}/build_number_version/{buildNumberVersion?}', [ApplicationController::class, 'show'])->name('apps.show');
    Route::resource('apps', ApplicationController::class)->except('show');
    Route::post('apps/invite', [ApplicationController::class, 'invite']);
    Route::get('members/search', [MemberController::class, 'search'])->name('members.search');
    Route::resource('members', MemberController::class)->except(['update']);
    Route::post('members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::get('apps/{app}/build_numbers', [BuildNumberController::class, 'show'])->name('apps.build_numbers.show');
    Route::delete('apps/{app}/delete-old-versions', [BuildNumberController::class, 'deleteOldVersions'])->name('apps.delete-old-versions');
    Route::delete('apps/build_numbers/{buildNumber}', [BuildNumberController::class, 'destroy'])->name('apps.build_number.destroy');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::group(['prefix' => 'builds', 'as' => 'build'], function () {
        Route::get('/{id}/download', [BuildNumberController::class, 'download'])->name('.download');
        Route::get('/{id}/plist-download', [BuildNumberController::class, 'plistDownload'])->name('.plist-download');
    });
});
Route::get('lang/{lang}', [LangController::class, 'changeLanguage'])->name('lang');


Route::get('downloads/{name}', [BuildNumberController::class, 'downloadOneStack']);
