<?php

namespace App\Nova;
use App\Models\User;
use Laravel\Nova\Resource;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\UiAvatar;

class Admin extends Resource
{
    public static $model = User::class;

    public static $title = 'name';

    public static $search = ['id', 'name', 'email'];

    public static function label()
    {
        return 'Admins';
    }

    public static function singularLabel()
    {
        return 'Admin';
    }
    public static $displayInNavigation = true;

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            UiAvatar::make()->maxWidth(50),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254'),

            Boolean::make('Is Admin', 'is_admin')->readonly(),

            HasMany::make('Tasks'),
            HasMany::make('Categories'),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('is_admin', true);
    }

    public static function authorizedToViewAny(\Illuminate\Http\Request $request)
    {
        return (bool) $request->user()?->is_admin;
    }
}
