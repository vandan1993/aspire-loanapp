<?php
namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use Closure;
use Exception;

class UserMiddleware
{
    use HttpResponse;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try{
            $user = auth()->guard('users')->user();

             if (!empty($user) && $user->tokenCan('role:user')) {
                        return $next($request);
            }else{
               return $this->errorMsg([] , 'Not Authorized' , 401);
            }

        }catch(Exception $e){
                return $this->errorMsg([] , 'Not Authorized' , 401);
        }

    }
}
