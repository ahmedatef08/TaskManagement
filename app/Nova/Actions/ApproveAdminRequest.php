<?php

namespace App\Nova\Actions;

use App\Models\AdminRequest;
use App\Notifications\AdminRequestReviewed;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class ApproveAdminRequest extends Action
{
    use Queueable;

    public function handle(ActionFields $fields, Collection $models)
    {
        /** @var AdminRequest $req */
        foreach ($models as $req) {
            if ($req->status !== 'pending') {
                continue;
            }

            // 1) approve
            $req->status = 'approved';
            $req->reviewed_by = FacadesAuth::user()->id;
            $req->reviewed_at = now();
            $req->save();

            if ($req->type === 'delete_task') {
                $task = $req->requestable;
                if ($task) {
                    $task->delete();
                }
            }

            $req->user->notify(new AdminRequestReviewed($req));
        }

        return Action::message('Selected requests approved.');
    }

    public function authorizedToRun(\Illuminate\Http\Request $request, $model)
    {
        return (bool) $request->user()?->is_admin;
    }
}

