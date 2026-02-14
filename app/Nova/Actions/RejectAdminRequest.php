<?php
namespace App\Nova\Actions;

use App\Models\AdminRequest;
use App\Notifications\AdminRequestReviewed;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class RejectAdminRequest extends Action
{
    use Queueable;

    public function handle(ActionFields $fields, Collection $models)
    {
        /** @var AdminRequest $req */
        foreach ($models as $req) {
            if ($req->status !== 'pending') {
                continue;
            }

            $req->status = 'rejected';
            $req->reviewed_by = Auth::user()->id;
            $req->reviewed_at = now();
            $req->save();

            $req->user->notify(new AdminRequestReviewed($req));
        }

        return Action::message('Selected requests rejected.');
    }

    public function authorizedToRun(\Illuminate\Http\Request $request, $model)
    {
        return (bool) $request->user()?->is_admin;
    }
}

