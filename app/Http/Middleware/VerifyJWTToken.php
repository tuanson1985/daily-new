<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        try {

            $user = JWTAuth::setToken($request->token)->toUser();
            if($user->status != 1){
                return response()->json([
                    'message' => "Tài khoản của bạn đã bị vô hiệu hóa.",
                    'status' => 0
                ],401);
            }
        }
        catch (JWTException $e) {
            if($e instanceof TokenExpiredException) {
                return response()->json([
                    'message' => "Token hết hiệu lực",
                    'status' => 0
                ],401);
            }
            else if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'message' => "Token không hợp lệ.",
                    'status' => 0
                ],401);
            }else{
                return response()->json([
                    'message' => "Token bị thiếu.",
                    'status' => 0
                ],401);
            }
        }
        return $next($request);
    }
}
