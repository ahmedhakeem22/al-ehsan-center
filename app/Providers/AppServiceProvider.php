<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\HijriDateService;
use Livewire\Livewire; 
class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(HijriDateService::class, function ($app) {
      return new HijriDateService();
    });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //
  }
}
