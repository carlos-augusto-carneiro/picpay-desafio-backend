<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="API PicPay Desafio",
 * description="Documentação da API do Desafio PicPay Backend",
 * @OA\Contact(
 * email="carlosaugustoestudante1202@gmail.com"
 * )
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT",
 * description="Insira seu token JWT aqui para acessar as rotas protegidas"
 * )
 */
abstract class Controller
{
    //
}
