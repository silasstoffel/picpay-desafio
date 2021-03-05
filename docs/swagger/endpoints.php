<?php

####### Auth #######
/**
 * @OA\Post(
 *     path="/auth",
 *     summary="Efetua autenticação",
 *     description="Efetua autenticação da sua conta na plataforma YouPay.",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         description="Auth",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Login")
 *     ),
 *     @OA\Response(
 *       response="200",
 *       description="Autenticação efetuada com sucesso.",
 *       @OA\JsonContent(ref="#/components/schemas/ContaAutenticadaResponse")
 *     ),
 *     @OA\Response(
 *       response="401",
 *       description="Sem autorização.",
 *       @OA\JsonContent(ref="#/components/schemas/DefaultErrorResponse")
 *     ),
 *     @OA\Response(
 *       response="400",
 *       description="Detalhamento do erro.",
 *       @OA\JsonContent(ref="#/components/schemas/DefaultErrorResponse")
 *     )
 * )
 */

####### Contas #######

/**
 * @OA\Post(
 *     path="/v1/contas",
 *     summary="Cria conta",
 *     description="Cria uma conta na plataforma YouPay.",
 *     tags={"Contas"},
 *     @OA\RequestBody(
 *         description="Auth",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Conta")
 *     ),
 *     @OA\Response(
 *       response="201",
 *       description="Dados da conta criada",
 *       @OA\JsonContent(ref="#/components/schemas/Conta")
 *     ),
 *     @OA\Response(
 *       response="400",
 *       description="Detalhamento do erro.",
 *       @OA\JsonContent(ref="#/components/schemas/DefaultErrorResponse")
 *     )
 * )
 */
