<?php

namespace YouPay\Relacionamento\Infra\Conta;

use App\Models\Conta as ModelConta;
use YouPay\Relacionamento\Dominio\Conta\Conta;
use YouPay\Relacionamento\Dominio\Conta\ContaAutenticavel;
use YouPay\Relacionamento\Dominio\Conta\RepositorioContaAutenticavelInterface;

class RepositorioContaAutenticavel implements RepositorioContaAutenticavelInterface
{
    public function buscarPeloLogin($login): ?ContaAutenticavel
    {
        $conta = ModelConta::where('cpfcnpj', $login)
            ->orWhere('email', $login)
            ->first();

        if ($conta) {
            $c = Conta::criarInstanciaComArgumentosViaString(
                $conta->titular,
                $conta->email,
                $conta->cpfcnpj,
                $conta->hash,
                $conta->criado_em,
                $conta->id,
                $conta->celular
            );
            $contaAutenticavel = new ContaAutenticavel($c, '');
            return $contaAutenticavel;
        }
        return null;
    }
}
