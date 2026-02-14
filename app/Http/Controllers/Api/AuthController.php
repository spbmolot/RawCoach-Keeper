<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Регистрация нового пользователя
     */
    public function register(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
        ];

        if (config('recaptcha.enabled')) {
            $rules['recaptcha_token'] = ['required', new Recaptcha('register')];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        // Назначаем базовую роль
        $user->assignRole('user');

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::channel('auth')->info('API: New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно зарегистрирован',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Авторизация пользователя
     */
    public function login(Request $request): JsonResponse
    {
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];

        if (config('recaptcha.enabled')) {
            $rules['recaptcha_token'] = ['required', new Recaptcha('login')];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::channel('auth')->warning('API: Login failed', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            Log::channel('security')->warning('API: Failed login attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Неверные учетные данные'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        Log::channel('auth')->info('API: User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Успешная авторизация',
            'data' => [
                'user' => $user->load('roles', 'permissions'),
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Выход из системы
     */
    public function logout(Request $request): JsonResponse
    {
        Log::channel('auth')->info('API: User logged out', [
            'user_id' => $request->user()->id,
            'ip' => $request->ip(),
        ]);

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Успешный выход из системы'
        ]);
    }

    /**
     * Сброс пароля - отправка ссылки
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $rules = [
            'email' => ['required', 'email'],
        ];

        if (config('recaptcha.enabled')) {
            $rules['recaptcha_token'] = ['required', new Recaptcha('forgot_password')];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Ссылка для сброса пароля отправлена на email'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось отправить ссылку для сброса пароля'
        ], 400);
    }

    /**
     * Сброс пароля
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Log::channel('auth')->info('API: Password reset completed', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);
            Log::channel('security')->info('API: Password reset completed', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Пароль успешно изменен'
            ]);
        }

        Log::channel('auth')->warning('API: Password reset failed', [
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Не удалось сбросить пароль'
        ], 400);
    }
}
