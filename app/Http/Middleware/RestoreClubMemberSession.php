<?php

namespace App\Http\Middleware;

use App\Services\Club\ClubMemberSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestoreClubMemberSession
{
    public function handle(Request $request, Closure $next): Response
    {
        ClubMemberSession::restoreIntoSession();

        return $next($request);
    }
}
