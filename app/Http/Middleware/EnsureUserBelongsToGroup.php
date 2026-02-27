<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToGroup
{
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');

        if (!$group) {
            abort(404);
        }

        $user = $request->user();

        if (!$user->groups()->where('groups.id', $group->id)->exists()) {
            abort(403, 'No tienes acceso a este grupo.');
        }

        // Share the group with all views
        view()->share('currentGroup', $group);

        return $next($request);
    }
}
