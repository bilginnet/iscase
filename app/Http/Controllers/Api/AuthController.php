<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContracts;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $validation = $this->validator($request->all());

        if ($validation->fails()) {
            return response()->json($validation->getMessageBag());
        }

        $user = User::query()->where(['email' => $request->email])->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'api_token' => $user->createToken($request->uuid)->plainTextToken
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     *
     * Açıklama: Custom Request yazılıp onun üzerinden validate edilebilinir.
     */
    private function validator(array $data): ValidatorContracts
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'uuid' => ['required', 'string'],
        ]);
    }
}
