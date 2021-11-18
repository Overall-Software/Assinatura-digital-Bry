<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\BadResponseException;
use Mockery\Exception;


class BryAssinaturaService
{
    private $clientSecret;
    private $clientId;
    private $endpoint;

    public function __construct()
    {
        $this->clientSecret = env("BRY_ASSINATURA_CLIENT_SECRET");
        $this->clientId = env("BRY_ASSINATURA_CLIENT_ID");
        $this->endpoint = env("BRY_ASSINATURA_ENDPOINT");
    }

    /**
     * Gerar o access token
     * @return array|mixed
     */
    private function tokenGenerate()
    {
        try {
            $client = new Client();
            $header = [
                "Content-Type" => "application/x-www-form-urlencoded"
            ];
            $request = $client->post("https://cloud.bry.com.br/token-service/jwt", [
                "headers" => $header,
                "form_params" => [
                    "grant_type" => "client_credentials",
                    "client_id" => $this->clientId,
                    "client_secret" => $this->clientSecret,
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            return $response;
        } catch (BadResponseException $e) {
            $response = ["error" => true, "message" => $e->getMessage()];
            return $response;
        }
    }

    public function initializeAssign($pdf, $certificado)
    {
        try {
            $client = new Client();
            $header = [
                "Authorization" => $this->tokenGenerate()['access_token'],
                "expect" => ""
            ];
            $request = $client->post("{$this->endpoint}/fw/v1/pdf/pkcs1/assinaturas/acoes/inicializar", [
                "headers" => $header,
                "multipart" => [
                    [
                        "name" => "documento",
                        "contents" => Psr7\Utils::tryFopen($pdf, 'r'),
                    ],
                    [
                        "name" => "dados_inicializar",
                        "contents" => json_encode(
                            [
                                "perfil" => "CARIMBO",
                                "algoritmoHash" => "SHA256",
                                "formatoDadosEntrada" => "Base64",
                                "formatoDadosSaida" => "Base64",
                                "certificado" => $certificado,
                                "nonces" => ["PDF1"],
                            ]
                        ),
                    ],
                    [
                        "name" => "configuracao_texto",
                        "contents" => json_encode(
                            [
                                "incluirCN" => true,
                                "incluirCPF" => true,
                                "incluirEmail" => true,
                                "texto" => "Documento assinado digitalmente\nPara validação acesse o site assinaturadigital.iti.gov.br",
                            ]
                        ),
                    ],
                    [
                        "name" => "configuracao_imagem",
                        "contents" => json_encode(
                            [
                                "altura" => 20,
                                "largura" => 70,
                                "posicao" => "INFERIOR_DIREITO",
                                "pagina" => "PRIMEIRA",
                            ]
                        ),
                    ],
                ],
            ]);
            $response = json_decode($request->getBody(), true);
            return $response;
        } catch (Exception $e) {
            info($e);
            throw $e;
        }
    }

    public function finalizeAssign($assinatura, $nonce)
    {
        try {
            $client = new Client();
            $header = [
                "Authorization" => $this->tokenGenerate()['access_token'],
                "Content-Type" => "application/json"
            ];
            $request = $client->post("{$this->endpoint}/fw/v1/pdf/pkcs1/assinaturas/acoes/finalizar", [
                "headers" => $header,
                "json" => [
                    "nonce" => $nonce,
                    "formatoDeDados" => "Base64",
                    "assinaturasPkcs1" => $assinatura,
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            return $response;
        } catch (BadResponseException $e) {
            $response = ["error" => true, "message" => $e->getMessage()];
            return $response;
        }
    }


}
