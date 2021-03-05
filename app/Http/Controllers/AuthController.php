<?php

namespace App\Http\Controllers;

use DomainException;
use Exception;
use Illuminate\Http\Request;
use YouPay\Relacionamento\Aplicacao\Conta\Autenticador;
use YouPay\Relacionamento\Dominio\Conta\ContaAutenticavel;
use YouPay\Relacionamento\Infra\Conta\GeradorToken;
use YouPay\Relacionamento\Infra\Conta\GerenciadorSenha;
use YouPay\Relacionamento\Infra\Conta\RepositorioContaAutenticavel;

class AuthController extends Controller
{

    public function store(Request $request)
    {
        try {
            $auth = new Autenticador(
                new RepositorioContaAutenticavel,
                new GeradorToken(env('JWT_SECRET')),
                new GerenciadorSenha
            );

            $contaAuth = $auth->autenticar($request->login, $request->password);

            return $this->responseSuccess(
                $this->criarResposta($contaAuth), 200
            );

        } catch (DomainException $e) {
            return $this->response400(['error' => true, 'message' => $e->getMessage()], 401);
        } catch (Exception $e) {
            return $this->responseAppError('Nao foi possível efetivar o processo de autenticação.');
        }
    }

    private function criarResposta(ContaAutenticavel $contaAuth)
    {
        $conta = $contaAuth->getConta();
        return [
            'conta'  => [
                'id'         => $conta->getId(),
                'titular'    => $conta->getTitular()
            ],
            'token' => $contaAuth->getToken(),
        ];
    }
}
