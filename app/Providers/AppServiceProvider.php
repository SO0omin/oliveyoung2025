<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use App\Models\Category;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot()
    {
        View::composer('*', function ($view) {
            $categories = Category::with('subCategories')->orderBy('id', 'asc')->get();
            $view->with('categories', $categories);
        });
    }
}
