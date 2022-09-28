<?php

namespace App\Providers;
use App\AdminNotify;
use App\Category;
use App\Menu;
use App\Notification;
use App\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Composer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      View::share('menu', Menu::all());
      view::share('menu_categories', Category::get());
      view::share('product_menu', Product::where('status', '!=', '1')->get());
      view::share('unread_notify', AdminNotify::where('is_read', 0)->latest()->take(10)->get());
      view::share('read_notify', AdminNotify::where('is_read', 1)->latest()->take(2)->get());
    }
}
