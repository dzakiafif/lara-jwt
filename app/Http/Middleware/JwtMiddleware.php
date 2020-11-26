<?php
namespace App\Http\Middleware;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {    
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $status     = 401;
                $message    = 'This token is invalid. Please Login';
                return response()->json(compact('status','message'),401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                try
                {
                  $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                  $user = JWTAuth::setToken($refreshed)->toUser();
                  $request->headers->set('Authorization','Bearer '.$refreshed);
                }catch (JWTException $e){
                    return response()->json([
                        'code'   => 103,
                        'message' => 'Token cannot be refreshed, please Login again'
                    ]);
                }
            }else{
                $message = 'Authorization Token not found';
                return response()->json(compact('message'), 404);
            }
        }
        return $next($request);
    }
}

