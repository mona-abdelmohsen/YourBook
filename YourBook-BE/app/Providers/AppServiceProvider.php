<?php

namespace App\Providers;

use App\Repository\PostRepository\PostRepository;
use App\Repository\PostRepositoryInterface;
use App\Repository\StoryRepository\StoryRepository;
use App\Repository\StoryRepositoryInterface;
use App\Repository\UserRepository\UserRepository;
use App\Repository\UserRepositoryInterface;
use App\Services\AuthService\AuthService;
use App\Services\AuthService\EmailVerificationService;
use App\Services\AuthServiceInterface;
use App\Services\CommentServiceInterface;
use App\Services\Dropzone\DropzoneService;
use App\Services\DropzoneInterface;
use App\Services\EmailVerificationServiceInterface;
use App\Validators\MediaValidator;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{

    public array $services;
    public array $repositories;

    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->services = [
            CommentServiceInterface::class => CommentService::class,
            AuthServiceInterface::class => AuthService::class,
            DropzoneInterface::class => DropzoneService::class,
            EmailVerificationServiceInterface::class => EmailVerificationService::class,
        ];

        $this->repositories = [
            UserRepositoryInterface::class => UserRepository::class,
            PostRepositoryInterface::class => PostRepository::class,
            StoryRepositoryInterface::class => StoryRepository::class,
        ];

        /** Because I have changed some logic in vendor code */
        $loader = AliasLoader::getInstance();
        $loader->alias('MinuteOfLaravel\MediaValidator\Traits\MediaFile', 'App\Traits\MediaFile');

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // App::setLocale('ar');
        // // Un-guard All Models, allow mass assignment.
        // Model::unguard();

        $supportedLocales = ['en', 'ar'];
        $locale = request()->header('Accept-Language', 'en'); // Default to 'en'
        $locale = in_array($locale, $supportedLocales) ? $locale : 'en';
        App::setLocale($locale);
        
        // Register All Services..
        foreach ($this->services as $interface => $toClass) {
            $this->app->bind($interface, $toClass);
        }
        // Register All Repositories ...
        foreach ($this->repositories as $interface => $toClass) {
            $this->app->bind($interface, $toClass);
        }

        // Passport Auth Expiration...
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));


    }
}
