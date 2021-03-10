<?php

use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Carteira\RepositorioCarteira;
use YouPay\Operacao\Servicos\Carteira\AutorizadorTransferencia;

class CarteiraTest extends TestCase
{

    private Conta $contaLojista;
    private Conta $contaPessoa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->criarContaLojista();
        $this->criarContaUsuarioComum();
    }

    public function testLogistaNaoPodeFazerTransferencia()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Esta conta não pode efetivar transferência.');
        $this->expectExceptionCode(400);

        $carteira = $this->contaLojista->getCarteira();
        /** @var AutorizadorTransferencia $autorizador */
        $autorizador = $this->criarMockAutorizadorTransferencia();
        $carteira->transferir($this->contaLojista, $this->contaPessoa, 10.00, $autorizador);
    }

    public function testNaoPodeFazerTransferenciaParaPropriaConta()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('A transfêrencia precisa ser entre contas diferentes.');
        $this->expectExceptionCode(400);

        $carteira = $this->contaPessoa->getCarteira();
        /** @var AutorizadorTransferencia $autorizador */
        $autorizador = $this->criarMockAutorizadorTransferencia();
        $carteira->transferir($this->contaPessoa, $this->contaPessoa, 10.00, $autorizador);
    }

    public function testPrecisaTransferiarNormalmente()
    {
        $saldoContaOrigem = 100.00;
        $saldoContaDestino = 200.00;
        $valorTransferencia = 50.00;

        $respositorio = $this->criarMockRepositorioCarteira();
        $respositorio->method('carregarSaldoCarteira')
            ->will($this->onConsecutiveCalls($saldoContaDestino, $saldoContaOrigem));

            /** @var RepositorioCarteira $respositorio */
        $carteira = new Carteira($saldoContaOrigem, $respositorio);
        /** @var AutorizadorTransferencia $autorizador */

        $autorizador = $this->criarMockAutorizadorTransferencia();

        $carteira->transferir(
            $this->contaPessoa,
            $this->contaLojista,
            $valorTransferencia,
            $autorizador
        );

        $this->assertEquals(
            ($saldoContaOrigem - $valorTransferencia),
            $carteira->getSaldo()
        );
    }

    public function testNaoPodeTransferiarSeNaoTiverSaldoSuficiente()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Saldo insuficente para transferência.');
        $this->expectExceptionCode(400);

        $saldoContaOrigem = 49.99;
        $saldoContaDestino = 200.00;
        $valorTransferencia = 50.00;

        $respositorio = $this->criarMockRepositorioCarteira();
        $respositorio->method('carregarSaldoCarteira')
            ->will($this->onConsecutiveCalls($saldoContaDestino, $saldoContaOrigem));

            /** @var RepositorioCarteira $respositorio */
        $carteira = new Carteira($saldoContaOrigem, $respositorio);
        /** @var AutorizadorTransferencia $autorizador */

        $autorizador = $this->criarMockAutorizadorTransferencia();

        $carteira->transferir(
            $this->contaPessoa,
            $this->contaLojista,
            $valorTransferencia,
            $autorizador
        );
    }

    private function criarContaLojista()
    {
        $this->contaLojista = Conta::criarInstanciaComArgumentosViaString(
            'Lojista-001',
            'lojista-001@youpay.com.br',
            '87.736.756/0001-81',
            '123456',
            '2021-03-05 16:30:00',
            'dd49a9d1-fa61-41aa-b72d-b2b0911018dd',
            '27988776655'
        );
        /** @var RepositorioCarteira $repositorio */
        $repositorio = $this->criarMockRepositorioCarteira();
        $carteira    = new Carteira(1000.00, $repositorio);
        $this->contaLojista->vincularCarteira($carteira);
    }

    private function criarContaUsuarioComum()
    {
        $this->contaPessoa = Conta::criarInstanciaComArgumentosViaString(
            'Pessoa-001',
            'pessoa-001@youpay.com.br',
            '132.197.150-87',
            '654321',
            '2021-03-05 16:30:00',
            'a30f3e90-e793-4687-9667-a8b8c8d3364e',
            '27911223344'
        );
        /** @var RepositorioCarteira $repositorio */
        $repositorio = $this->criarMockRepositorioCarteira();
        $carteira    = new Carteira(100.00, $repositorio);
        $this->contaPessoa->vincularCarteira($carteira);
    }

    private function criarMockRepositorioCarteira()
    {
        $repositorio = $this->createMock(RepositorioCarteira::class);
        $repositorio->method('iniciarTransacao')->willReturn(null);
        $repositorio->method('finalizarTransacao')->willReturn(null);
        $repositorio->method('desfazerTransacao')->willReturn(null);
        $repositorio->method('armazenarMovimentacao')->willReturn(null);
        $repositorio->method('atualizarSaldoCarteira')->willReturn(null);
        //$repositorio->method('carregarSaldoCarteira')->willReturn(0.00);
        return $repositorio;
    }

    private function criarMockAutorizadorTransferencia($autorizado = true)
    {
        $autorizador = $this->createMock(AutorizadorTransferencia::class);
        $autorizador->method('autorizado')->willReturn($autorizado);
        return $autorizador;
    }

}
