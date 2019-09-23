<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Repositories\UserRepository;
use Modules\User\Entities\UserDocument;
use Modules\User\Services\CreateService as UserCreate;
use Auth, Log, Validator, Hash, DB, Storage, Str;

class RegisterController extends Controller
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function __invoke(Request $request, UserCreate $userCreate)
    {
        $data = $request->only('name', 'email', 'password', 'phone', 'selfie', 'identity', 'identity_type');

        $validator = Validator::make($data, [
            'email'         => 'required|string|email|max:255|unique:user',
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20|unique:user',
            'password'      => 'required|string|min:8|max:255',
            'selfie'        => 'required|mimeTypes:image/png,image/jpeg|max:1024',
            'identity'      => 'required|mimeTypes:image/png,image/jpeg|max:1024',
            'identity_type'      => 'required|string'
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

        DB::beginTransaction();

        try
        { 
            // Store user data
            $userCreate->handle($data, 'user');

            DB::commit();

            return response()->json([
                'status' => 'OK',
                'code' => 'SUCCESS',
                'timestamps' => now()->timestamp,
                'message' => null
            ]);
        }
        catch ( \Exception $e )
        {
            DB::rollback();

            return response()->json([
                'status' => 'ERROR',
                'code' => 'SYSTEM',
                'timestamps' => now()->timestamp,
                'message' => 'Terjadi kesalahan dengan aplikasi.',
                'data' => null
            ], 500);
        }
    }

    public function validation(Request $request)
    {
        $data = $request->only('name', 'email', 'password', 'phone');

        $validator = Validator::make($data, [
            'email'         => 'required|string|email|max:255|unique:user',
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20|unique:user',
            'password'      => 'required|string|min:8|max:255',
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

        return response()->json([
            'status' => 'OK',
            'code' => 'SUCCESS',
            'timestamps' => now()->timestamp,
            'message' => null
        ]);
    }
}