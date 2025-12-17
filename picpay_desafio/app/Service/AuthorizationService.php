<?php

namespace App\Service;

use App\Entity\User;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Http\Client\Response;
class AuthorizationService
{
    public function authorize(): bool
    {
        return true;
        /** @var Response $response */
        /*
        $response = Http::withoutVerifying()->get('https://util.devi.tools/api/v2/authorize');

        if ($response->failed()) {
            throw new Exception('Authorization service is unavailable.');
        }

        $data = $response->json();
        if (!isset($data['status']) || $data['status'] !== 'success') {
            throw new Exception('Operation not authorized by external service.');
        }

        return true;*/
    }
}
