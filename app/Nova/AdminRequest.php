<?php

namespace App\Nova;

use App\Models\AdminRequest as AdminRequestModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\MorphTo;

use function Psy\sh;

class AdminRequest extends Resource
{
    public static $model = AdminRequestModel::class;

    public static $title = 'id';

    public static $search = ['id', 'type', 'status'];

    public static function label()
    {
        return 'Admin Requests';
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('User', 'user', \App\Nova\User::class)->sortable(),

            Text::make('Type')->sortable(),

            Select::make('Status')->options([
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ])->displayUsingLabels()->readonly()->sortable(),

            MorphTo::make('Requestable')->types([
                Task::class,
        ])->readonly(),

            BelongsTo::make('Reviewed By', 'reviewer', \App\Nova\User::class)
                ->nullable()
                ->readonly(),

            DateTime::make('Reviewed At')->readonly(),

            DateTime::make('Created At')->readonly()->sortable(),
        ];
    }

    public static function authorizedToViewAny(Request $request)
    {
        return (bool) $request->user()?->is_admin;
    }

    public function actions(NovaRequest $request)
    {
        return [
            new \App\Nova\Actions\ApproveAdminRequest,
            new \App\Nova\Actions\RejectAdminRequest,
        ];
    }

}
