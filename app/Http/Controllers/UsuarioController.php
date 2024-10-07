<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class UsuarioController extends Controller
{

    public function index()
    {

    }

    public function store(Request $request)
    {
        $verificar = Usuarios::where('login', $request->login)->first();
        if(!$verificar){
            $token = bin2hex(random_bytes(16)); 
            $senha = md5($request->senha);
            Usuarios::insert(['login' => $request->login, 'email' => $request->email, 'senha' => $senha, 'token' => $token]);
            return response()->json(['mensagem' => 'success']);
        }
        else{
            return response()->json(['mensagem' => 'error']);
        }
    }

    public function login(Request $request){
        $senha = md5($request->senha);
        $verificar = Usuarios::where('login', $request->login)->where('senha', $senha)->first();
        if($verificar){
            $key = env('JWT_SECRET');
            $payload = [
                'iss' => 'Laravel', 
                'sub' => $verificar->token,
                'iat' => time(), 
                'exp' => time() + 60 * 60 
            ];
            
            $token = JWT::encode($payload, $key, 'HS256');
            
            return response()->json(['mensagem' => 'success', 'token' => $token]);
        } else {
            return response()->json(['mensagem' => 'error']);
        }
    }

    public function verificar(Request $request)
    {
        $token = $request->token;
    
        if ($token) {
            try {
                $decoded = JWT::decode($token, new Key(Config::get('jwt.secret'), 'HS256'));
    
                return response()->json(['mensagem' => 'Token válido', 'dados' => (array) $decoded]);
            } catch (ExpiredException $e) {
                return response()->json(['mensagem' => 'Token expirado'], 401);
            } catch (\Exception $e) {
                return response()->json(['mensagem' => 'Token inválido: ' . $e->getMessage()], 401);
            }
        } else {
            return response()->json(['mensagem' => 'Token não fornecido'], 401);
        }
    }

    public function show(string $id)
    {

    }

    public function update(Request $request, string $id)
    {

    }

    public function destroy(string $id)
    {

    }
}
