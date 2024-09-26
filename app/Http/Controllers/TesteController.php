<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Log;
use libMail\HotmailMailer\HotmailMailer;

class TesteController extends Controller
{
    public function teste()
    {

        $headers = [
            'Content-Type' => 'text/xml; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $soapRequest = file_get_contents('php://input'); // nao precisar utilizar $_POST porque recebe dados nao estruturados
            
            $xml = simplexml_load_string($soapRequest); // transforma em XML


            if (isset($xml->Body->getUsuarioByLogin->login)) {
                $login = (string) $xml->Body->getUsuarioByLogin->login;
            } else {
                $login = null;
            }
 
            $usuario = Usuarios::where('login', $login)->first();

            $soapResponse = $this->generateSoapResponse($usuario);

            return response($soapResponse, 200, $headers);
        }

        // Caso não seja POST SOAP, retorne um erro
        return response('Método não permitido', 405);
    }

     // Resposta do SOAP :)
     private function generateSoapResponse($usuario)
     {
         if ($usuario) {
             $response = "
                 <soap:Envelope xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
                     <soap:Body>
                         <getUsuarioByLoginResponse>
                             <usuario>
                                 <email>{$usuario->email}</email>
                                 <login>{$usuario->login}</login>
                                 <senha>{$usuario->senha}</senha>
                                 <token>{$usuario->token}</token>
                             </usuario>
                         </getUsuarioByLoginResponse>
                     </soap:Body>
                 </soap:Envelope>";
         } else {
             $response = "
                 <soap:Envelope xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
                     <soap:Body>
                         <getUsuarioByLoginResponse>
                             <error>Usuário não encontrado</error>
                         </getUsuarioByLoginResponse>
                     </soap:Body>
                 </soap:Envelope>";
         }
 
         return $response;
     }

}
