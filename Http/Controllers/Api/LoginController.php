<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Repositories\UserRepository;
use Auth, Log, Validator;

class LoginController extends Controller
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function __invoke(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status' => 'ERROR',
                'code' => 'VALIDATION',
                'timestamps' => now()->timestamp,
                'message' => $validator->errors()
            ]);
        }

        if ( Auth::attempt($credentials) ) 
        {
            try
            {
                $user = $this->user->findByEmail(array_get($credentials, 'email'));

                $createToken = $user->createToken($request->get('accessTokenName', 'default'));

                $user['token'] = $createToken->accessToken;
                $user['expired_at'] = $createToken->token->expires_at->timestamp;

                // array_forget($user, ['email_verify']);

                return response()->json([
                    'status' => 'OK',
                    'code' => 'SUCCESS',
                    'timestamps' => now()->timestamp,
                    'message' => null,
                    'data' => $user
                ]);
            }
            catch ( \Exception $e )
            {
                Log::error('Api\UserController::login: ' . $e->getMessage());

                return response()->json([
                    'status' => 'ERROR',
                    'code' => 'SYSTEM',
                    'timestamps' => now()->timestamp,
                    'message' => 'Terjadi kesalahan dengan server.'
                ]);
            }
        }

        return response()->json([
            'status' => 'ERROR',
            'code' => 'AUTHENTICATION_FAILED',
            'timestamps' => now()->timestamp,
            'message' => __('Otorisasi gagal'),
        ]);
    }
}