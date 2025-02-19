<?php 
namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    const TOKEN_EXPIRATION = 60*60*6; // 6 hours

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->login;
        $password = $request->password;

        $nameSurname = preg_split('/(?<=[a-z])(?=[A-Z])/', $login, 2);// @todo polish letters and '-' in surname
        if (count($nameSurname) < 2) {
            return response()->json(['error' => 'Invalid login format'], 400);
        }

        $patient = Patient::where('name', $nameSurname[0])
                  ->where('surname', $nameSurname[1])
                  ->first();

        if (!$patient || $patient->birth_date !== $password) {
            Log::info('Invalid login attempt', ['login' => $login, 'name' => $nameSurname[0], 'surname' => $nameSurname[1], 'password' => $password, 'patient' => $patient ?? null]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('Login OK', ['login' => $login, 'name' => $nameSurname[0], 'surname' => $nameSurname[1], 'password' => $password, 'patient' => $patient ?? null]);
   

        $token = $this->generateJwtToken($patient);
        return response()->json(['token' => $token]);
    }


    
    //    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsIm5hbWUiOiJQaW90ciIsInN1cm5hbWUiOiJLb3dhbHNraSIsImlhdCI6MTczOTkxNjIxMiwiZXhwIjoxNzM5OTM3ODEyfQ.tnHPzTeBJqAb5AgG4QbzNYYKpEt8jtF259RzK1r8ED8"
    
    public function results()
    {  
        $patient = auth()->user();

        Log::info('Results login OK', ['patient' => $patient]);

        $orders = $patient->orders()->with('testResults')->get()->unique('id');

        Log::info('Orders', ['orders' => $orders]);

        $response = [
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'surname' => $patient->surname,
                'sex' => $patient->sex,
                'birthDate' => $patient->birth_date,
            ],
            'orders' => $orders->map(function ($order) {
                $results = $order->testResults->map(function ($result) {
                    return [
                        'name' => $result->name,
                        'value' => $result->value,
                        'reference' => $result->reference,
                    ];
                });

                return [
                    'orderId' => $order->id,
                    'results' => $results,
                ];
            }),
        ];

        return response()->json($response);
    }

    private function generateJwtToken($patient)
    {
        $payload = [
            'sub' => $patient->id,
            'name' => $patient->name,
            'surname' => $patient->surname,
            'iat' => time(),
            'exp' => time() + self::TOKEN_EXPIRATION 
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }
}