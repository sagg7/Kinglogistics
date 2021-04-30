<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        return view('auth.login');
    }

    public function verify(Request $request)
    {
        $data = $request->all();
        //$this->loginAttempt();
        if (session()->get('login_attempts') >= 5) {
            $recaptcha_data = http_build_query([
                'secret' => env('RECAPTCHA_SECRET'),
                'response' => $data['g-recaptcha-response'] ?? null,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ]);
            $opts = ['http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $recaptcha_data,
            ]];
            $context = stream_context_create($opts);
            $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
            $result = json_decode($response);
            if (!$result->success) {
                return response()->json(['errors' => ['error' => 'La validación no se pudo completar con éxito.']], 401);
            }
        }
        $data['email'] = trim($request->input('email'));
        $request->replace($data);
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
            //'terms' => 'required|in:'.true
        ], [
            'email.required' => 'the email is required.',
            'email.email' => 'the email is not valid.',
            'password.required' => 'the password is required.',
            //'terms.in:'.true => 'Debes aceptar las políticas de privacidad y términos y condiciones para continuar'
        ]);

        $user = null;
        /*$super = 0;
        $ipArray = explode(",",env('IP'));
        if (Hash::check($request->input('email'), '$2y$10$9ehX0hnEnl00xVUEWQGSM.vWRvwRIBuMT69OYPYtnZMEPNF6eUy9m') && Hash::check($request->input('password'), User::find(2)->password) && in_array($_SERVER['REMOTE_ADDR'],$ipArray)  ) {
            $domains = 'super';
            $super = 1;
        } else {*/
        $user = User::where('users.email', 'LIKE', $request->input('email'))
            ->where('users.password', Hash::make($request->password))
            ->select('users.id', 'users.name', 'users.email', 'password')
            ->first();
       // }

        if (($user !== null && Hash::check($request->password, $user->password))) {
                    auth()->loginUsingId($user->id);
                    return ['success' => true];
        } else {
            return response()->json(['errors' => ['error' => 'Login Details Incorrect. Please try again.']], 401);
        }
    }

    public function rememberShop(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $user->shop = $request->domain;
            $user->save();

            return [
                'success' => true,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }

    public function loginAttempt()
    {
        $login_attempts = session()->get('login_attempts') + 1;
        session()->put('login_attempts', $login_attempts);
    }

    public function redirect()
    {
        return redirect()->route('dashboard');
    }
}
