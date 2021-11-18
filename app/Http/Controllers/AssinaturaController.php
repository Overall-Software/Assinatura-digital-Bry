<?php

namespace App\Http\Controllers;

use App\Services\BryAssinaturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AssinaturaController
{

    public function view () {
        return Inertia::render('Home');
    }

    public function iniciarAssinatura (Request $request)
    {
        $dados = $request->all();

        $bry = new BryAssinaturaService();

        $pdf = file_get_contents($request->file('pdf'));

        $pdf = Storage::put('tmp', $request->file('pdf'));
        $path = Storage::path($pdf);

        try {
            $assinatura = $bry->initializeAssign(
                $path,
                $dados['cert_content']
            );

            $retorno = [
                "nonce" => $assinatura['nonce'],
                "formatoDadosEntrada" => $assinatura['formatoDadosEntrada'],
                "formatoDadosSaida" => $assinatura['formatoDadosSaida'],
                "assinaturas" => [
                    [
                        "algoritmoHash" => $assinatura['algoritmoHash'],
                        "nonce" => $assinatura['assinaturasInicializadas'][0]['nonce'],
                        "hashes" => [$assinatura['assinaturasInicializadas'][0]['messageDigest']]
                    ]
                ],
            ];

            return response()->json($retorno);

        } catch (\Exception $e) {
            info($e);
            return response()->json($e, 500);
        }

    }

    public function finalizarAssinatura (Request $request)
    {
        $dados = $request->only(['assinaturas', 'nonce']);

        $assinatura = [
            [
                "cifrado" => $dados['assinaturas'][0]['hashes'][0],
                "nonce" => $dados['assinaturas'][0]['nonce'],
            ]
        ];

        $bry = new BryAssinaturaService();
        $assinatura = $bry->finalizeAssign($assinatura, $dados['nonce']);

        $pdf = $assinatura['documentos'][0]['links'][0]['href'];

        return response()->json($pdf);
    }
}
