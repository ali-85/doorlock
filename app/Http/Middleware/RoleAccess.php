<?php

namespace App\Http\Middleware;

use App\Models\RoleHasMenu;
use App\Models\Submenu;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->segment(1).'/'.$request->segment(2);
        $submenu = Submenu::where('submenuUrl', $uri)->first();
        $data = RoleHasMenu::where([
            'role_id' => Auth::user()->role_id,
            'submenu_id' => $submenu->id
        ])->first();
        if ($data) {
            return $next($request);
        } else {
            return abort(403);
        }
    }
}
