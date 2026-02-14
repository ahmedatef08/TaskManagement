<?php

namespace App\Providers;

use App\Nova\Admin;
use App\Nova\AdminRequest;
use App\Nova\Category;
use App\Nova\Task;
use App\Nova\User;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Facades\Gate as FacadesGate;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot(): void
{
    parent::boot();
    Nova::auth(function ($request) {
        return (bool) $request->user()?->is_admin;
    });
    Nova::withBreadcrumbs();
    Nova::mainMenu(function($menu){
        return[
            MenuSection::make('Admins',[
                MenuItem::resource(Admin::class),
                MenuItem::resource(AdminRequest::class),
            ])->icon('shield-check')->collapsable(),

            MenuSection::make('Users',[
                MenuItem::resource(User::class),
            ])->icon('user-group'),

            MenuSection::make('Management',[
                MenuItem::resource(Task::class),
                MenuItem::resource(Category::class),
            ])->icon('clipboard-list'),
        ];
    });
}

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes(default: true)
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate(): void
    {
        FacadesGate::define('viewNova', function ($user) {
            return in_array($user->email, [
                'ahmed@test.com'
            ]);
        });
    }
    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards(): array
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools(): array
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
