<?php
namespace App\Http\Middleware;

use App\Traits\HttpResponse;
use Closure;
use Exception;

class AdminMiddleware
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
            $admin = auth()->guard('admin')->user();

             if (!empty($admin) && $admin->tokenCan('role:admin')) {
                        return $next($request);
            }else{
               return $this->errorMsg([] , 'Not Authorized' , 401);
            }

        }catch(Exception $e){
                return $this->errorMsg([] , 'Not Authorized' , 401);
        }
    }
}
