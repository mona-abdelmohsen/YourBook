<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Filament\Resources\ReportResource;

/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('test-uploader', 'uploader');

Route::get('aws', function(){
    $user = \App\Models\Auth\User::get()->first();
    dd($user->addMediaFromUrl('https://ucarecdn.com/edd30a02-8343-46ed-8d6b-4967bcba8673/-/preview/500x500/-/quality/smart/-/format/auto/')
        ->toMediaCollection('images'));

//    dd($user->getMedia("*")[0]->getTemporaryUrl(now()->addMinutes(1)));

    dd($user->getRegisteredMediaCollections());
});

Route::get('/account-setup', \App\Livewire\AccountSetup::class)->middleware('auth')->name('update.account.info');

Route::middleware('auth')->get('/friend', function(){
    $me = auth()->user();
    $user1 = \App\Models\Auth\User::find(1);
//    $user1->befriend($me);
    $me->unblockFriend($user1);

    dd(
        $me->isFriendWith($user1),
        $me->hasFriendRequestFrom($user1),
        $me->getFriendRequests(),
        $me->getFriendsCount()
    );
});

Route::get('/', function () {
    return redirect()->route('login');
//    return view('welcome');
});


Route::middleware(['auth', 'verified', 'phoneVerified'])->group(function () {
    Route::get('/feed', function () {
        return view('user.feed');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.show');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::view('/profile/settings', 'profile.settings')->name('profile.settings');

    Route::post('/uploadHandler', [\App\Http\Controllers\UploadHandlerController::class, 'handler'])->name('upload.handler');

    Route::get('/profile/books', [ProfileController::class, 'books'])->name('profile.books.show');
    Route::post('/book/store', [\App\Http\Controllers\BookController::class, 'store'])->name('book.store');
    Route::get('/book/{id}', [\App\Http\Controllers\BookController::class, 'show'])->name('book.show');
});

/** Utilities */
Route::post('dropzone/upload', [\App\Http\Controllers\Utilities\DropzoneController::class, 'handler'])->name('dropzone.handler');
Route::post('/utilities/email-phone-validator', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'emailPhoneValidator']);

require __DIR__.'/auth.php';

// Overwritten to check if the account is verified
Route::post('/oauth/token', [
    'uses' => '\App\Http\Controllers\API\CustomAccessTokenController@issueUserToken',
    'as' => 'token',
    'middleware' => 'throttle',
]);

// Add the route for bulk delete action in ReportResource
Route::post('/filament/resources/reports/bulkDelete', [ReportResource::class, 'handleBulkDelete'])->name('filament.resources.reports.bulkDelete');
