<?php

use App\Http\Controllers\API\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function(){


    Route::post('upload-test', [\App\Http\Controllers\API\UploadTestController::class, 'upload']);

    /** Guest Requests .. */
    Route::post('/user/register', [\App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('/user/send-reset-link', [\App\Http\Controllers\API\AuthController::class, 'sendResetLinkEmail']);

    /** Utility Requests */
    Route::get('/utilities/get-countries', [\App\Http\Controllers\API\UtilitiesController::class, 'getCountries']);
    Route::get('/utilities/get-interests', [\App\Http\Controllers\API\UtilitiesController::class, 'getInterests']);



    /** Authenticated ... */
    Route::middleware('auth:api')->group(function(){
        // settings
        Route::post('settings', [SettingController::class, 'store']);
        Route::post('settings/change-password', [SettingController::class, 'changePassword']);
        Route::post('settings/update-profile', [SettingController::class, 'update']);

        Route::post('/utilities/interests', [\App\Http\Controllers\API\UtilitiesController::class, 'storeInterests']);
        //Route::post('/user/email-phone-verify', [\App\Http\Controllers\API\UserController::class, 'EmailMobileVerifiy']);

        Route::post('/email/verification-send-OTP', [\App\Http\Controllers\API\AuthController::class, 'sendEmailVerificationCode'])
            ->middleware('throttle:6,1');
        Route::post('/email/verify', [\App\Http\Controllers\API\AuthController::class, 'emailVerify']);

        Route::post('/user/logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);
        Route::post('/user/change-password', [\App\Http\Controllers\API\AuthController::class, 'changePassword']);
        Route::post('/user/set-fcm-token', [\App\Http\Controllers\API\AuthController::class, 'setToken']);

        // Delete Account
        Route::delete('/user/delete-account', [\App\Http\Controllers\API\AuthController::class, 'deleteAccount']);
        // admin disable and enable user
        Route::delete('/user/disable-user/{user_id}', [\App\Http\Controllers\API\AuthController::class, 'disableUser']);
        Route::post('/user/enable-user/{user_id}', [\App\Http\Controllers\API\AuthController::class, 'enableUser']);
        
        Route::get('/user/get-interests', [\App\Http\Controllers\API\UserController::class, 'getInterests']);
        Route::post('/user/update-interests', [\App\Http\Controllers\API\UserController::class, 'updateInterests']);

        Route::get('/user/general-info', [\App\Http\Controllers\API\UserController::class, 'getGeneralInfo']);
        Route::post('/user/update-general-info', [\App\Http\Controllers\API\UserController::class, 'updateGeneralInfo']);

        Route::get('/user/get-top-accounts', [\App\Http\Controllers\API\UserController::class, 'getTopAccounts']);


        Route::post('/interactions/follow/{user_id}', [\App\Http\Controllers\API\UserInteractionController::class, 'followAccount']);
        Route::post('/interactions/unfollow/{user_id}', [\App\Http\Controllers\API\UserInteractionController::class, 'unfollowAccount']);

        Route::get('/book/{book_id}', [\App\Http\Controllers\API\BookController::class, 'show']);
        Route::post('/book/{book_id}/update', [\App\Http\Controllers\API\BookController::class, 'update']);
        Route::post('/book/store', [\App\Http\Controllers\API\BookController::class, 'store']);
        Route::get('/books', [\App\Http\Controllers\API\BookController::class, 'index']);
        Route::post('/book/{book_id}/media-manager/{mode}', [\App\Http\Controllers\API\BookController::class, 'attachDetachMedia']);

        Route::post('/media/destroy/{media_uuid}', [\App\Http\Controllers\API\MediaController::class, 'destroy']);
        Route::post('/media/upload', [\App\Http\Controllers\API\MediaController::class, 'upload']);
        Route::get('/media', [\App\Http\Controllers\API\MediaController::class, 'index']);

        Route::get('/user/categories', [\App\Http\Controllers\API\UserCategoryController::class, 'index'])->name('api.get.user-categories');
        Route::post('/user/categories', [\App\Http\Controllers\API\UserCategoryController::class, 'store']);

        Route::prefix('friendship')->group(function(){
            Route::post('/friend-request/{user_id}/send', [\App\Http\Controllers\API\FriendshipController::class, 'sendFriendRequest']);
            Route::post('/friend-request/{sender_id}/accept', [\App\Http\Controllers\API\FriendshipController::class, 'acceptFriendRequest']);
            Route::post('/friend-request/{sender_id}/deny', [\App\Http\Controllers\API\FriendshipController::class, 'denyFriendRequest']);
            Route::get('/friend-requests', [\App\Http\Controllers\API\FriendshipController::class, 'getFriendRequests']);
            Route::post('/{user_id}/unfriend', [\App\Http\Controllers\API\FriendshipController::class, 'unfriend']);
            Route::get('/friends', [\App\Http\Controllers\API\FriendshipController::class, 'getFriends']);
            Route::get('/blocked', [\App\Http\Controllers\API\FriendshipController::class, 'getBlocked']);
            Route::post('/{user_id}/block', [\App\Http\Controllers\API\FriendshipController::class, 'blockUser']);
            Route::post('/{user_id}/unblock', [\App\Http\Controllers\API\FriendshipController::class, 'unblockUser']);
            Route::get('/{user_id}/follow-list', [\App\Http\Controllers\API\FriendshipController::class, 'getFollowersList']);
            Route::get('/{user_id}/friends-list', [\App\Http\Controllers\API\FriendshipController::class, 'getFriendsList']);
        });

        Route::get('moods', [\App\Http\Controllers\API\PostController::class, 'getMoods']);
        Route::prefix('posts')->group(function(){
            Route::post('/', [\App\Http\Controllers\API\PostController::class, 'store']);
            Route::get('/', [\App\Http\Controllers\API\PostController::class, 'index']);
            Route::get('/feed', [\App\Http\Controllers\API\PostController::class, 'feed']);
            Route::delete('/{story_id}', [\App\Http\Controllers\API\PostController::class, 'destroy']);
            Route::get('/{post_id}', [\App\Http\Controllers\API\PostController::class, 'show']);
            Route::post('/{post_id}/react', [\App\Http\Controllers\API\PostController::class, 'react']);
            Route::get('/{post_id}/reactions', [\App\Http\Controllers\API\PostController::class, 'getReactions']);



            Route::post('/{post_id}/comments', [\App\Http\Controllers\API\CommentController::class, 'store']);
            Route::get('/{post_id}/comments', [\App\Http\Controllers\API\CommentController::class, 'index']);
            Route::get('/{post_id}/comments/{comment_id}', [\App\Http\Controllers\API\CommentController::class, 'show']);
            Route::delete('/{post_id}/comments/{comment_id}/delete', [\App\Http\Controllers\API\CommentController::class, 'destroy']);
            Route::delete('/comments/replies/{reply_id}/delete', [\App\Http\Controllers\API\CommentController::class, 'deleteReply']);
            Route::post('/{post_id}/comments/{comment_id}/reply', [\App\Http\Controllers\API\CommentController::class, 'reply']);
            Route::post('/{post_id}/comments/{comment_id}/react', [\App\Http\Controllers\API\CommentController::class, 'react']);
            Route::get('/{post_id}/comments/{comment_id}/reactions', [\App\Http\Controllers\API\CommentController::class, 'getReactions']);

        });

        Route::prefix('stories')->group(function(){
            Route::post('/', [\App\Http\Controllers\API\StoryController::class, 'store']);
            Route::get('/', [\App\Http\Controllers\API\StoryController::class, 'index']);
            Route::get('/feed', [\App\Http\Controllers\API\StoryController::class, 'feed']);
            Route::delete('/{story_id}', [\App\Http\Controllers\API\StoryController::class, 'destroy']);
            Route::get('/{story_id}', [\App\Http\Controllers\API\StoryController::class, 'show']);
            Route::post('/{story_id}/react', [\App\Http\Controllers\API\StoryController::class, 'react']);
            Route::get('/{story_id}/reactions', [\App\Http\Controllers\API\StoryController::class, 'getReactions']);
            Route::post('/{story_id}/mark-viewed', [\App\Http\Controllers\API\StoryController::class, 'markViewed']);


        });

        Route::prefix('chat')->group(function(){
            Route::post('/auth', [\App\Http\Controllers\API\ChatController::class, 'auth']);
            Route::post('/send-message', [\App\Http\Controllers\API\ChatController::class, 'send']);
            Route::post('/fetch-messages', [\App\Http\Controllers\API\ChatController::class, 'fetch']);
            Route::post('/make-seen', [\App\Http\Controllers\API\ChatController::class, 'seen']);
            Route::post('/delete-conversation', [\App\Http\Controllers\API\ChatController::class, 'deleteConversation']);
            Route::post('/delete-message', [\App\Http\Controllers\API\ChatController::class, 'deleteMessage']);
            Route::get('/download/{fileName}', [\App\Http\Controllers\API\ChatController::class, 'download']);
            Route::get('/get-contacts', [\App\Http\Controllers\API\ChatController::class, 'getContacts']);
            Route::post('/set-active-status', [\App\Http\Controllers\API\ChatController::class, 'setActiveStatus']);
            Route::get('/get-shared-files', [\App\Http\Controllers\API\ChatController::class, 'sharedPhotos']);
            Route::post('/star', [\App\Http\Controllers\API\ChatController::class, 'favorite']);
            Route::get('/favorites', [\App\Http\Controllers\API\ChatController::class, 'getFavorites']);
        });

        Route::prefix('hash-tags')->group(function(){
            Route::get('/trend', [\App\Http\Controllers\API\TagsController::class, 'trend']);
            Route::get('/{hash_tag}/posts', [\App\Http\Controllers\API\TagsController::class, 'posts']);
        });

        Route::prefix('notifications')->group(function(){
            Route::get('/', [\App\Http\Controllers\API\NotificationController::class, 'index']);
            Route::post('/make-seen', [\App\Http\Controllers\API\NotificationController::class, 'seen']);
            Route::delete('/clear', [\App\Http\Controllers\API\NotificationController::class, 'clear']);
        });

        Route::prefix('profile')->group(function (){
            Route::get('/{user_id}', [\App\Http\Controllers\API\ProfileController::class, 'getGeneralInfo']);
            Route::get('/{user_id}/posts', [\App\Http\Controllers\API\ProfileController::class, 'posts']);
            Route::get('/{user_id}/books', [\App\Http\Controllers\API\ProfileController::class, 'books']);
        });

        Route::prefix('favorite')->group(function(){
            Route::get('/{item_type}', [\App\Http\Controllers\API\FavoriteController::class, 'index']);
            Route::post('/{item_id}/{item_type}', [\App\Http\Controllers\API\FavoriteController::class, 'favorite']);
            Route::get('/{user_id}/{item_type}', [\App\Http\Controllers\API\FavoriteController::class, 'indexFavouriteByUserId']);
        });

        Route::prefix('report')->group(function(){
            Route::post('/{item_type}/{item_id}', [\App\Http\Controllers\API\ReportController::class, 'report']);
        });

        Route::prefix('search')->group(function(){
            Route::post('/hashtags', [\App\Http\Controllers\API\SearchController::class, 'hashtags']);
            Route::post('/users', [\App\Http\Controllers\API\SearchController::class, 'users']);
            Route::post('/posts', [\App\Http\Controllers\API\SearchController::class, 'posts']);
        });

        Route::prefix('agora')->group(function(){
            Route::post('/get-token', [\App\Http\Controllers\API\AgoraController::class, 'getToken']);
        });

    });





});
