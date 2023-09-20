<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\HttpResponse;

class Handler extends ExceptionHandler
{
    use HttpResponse;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
          
        });

        $this->reportable(function (AuthenticationException $e , Request $request) {
            //
            if ($request->is('api/*')) {
                
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }

            if($request->expectsJson()){
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }

        });

    }
    
}
