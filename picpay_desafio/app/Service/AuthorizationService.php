<?php

namespace App\Service;

use App\Entity\User;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
class AuthorizationService
{
    private const CACHE_KEY="circuite_breaker_auth";
    private const FAILURE_LIMIT= 5;
    private const TIMEOUT_SECONDS=30;
    public function authorize(): bool
    {
        if (Cache::has(self::CACHE_KEY . '_open')) {
            $failureCount = Cache::get(self::CACHE_KEY);
                throw new Exception('Authorization service is temporarily unavailable. Please try again later.');
        }
        try{
            /** @var Response $response */
            /*
            $response = Http::retry(5, 100)
                        ->timeout(5)
                        ->withoutVerifying()
                        ->get('https://util.devi.tools/api/v2/authorize');

            if ($response->failed() ||
                $response->json('status') === 'fail' ||
                $response->json('message') !== 'Autorizado') {
                throw new Exception('Não autorizado pelo serviço externo.');
            }*/

            Cache::forget(self::CACHE_KEY . '_failures');
            return true;
        } catch (Exception $e) {
            $this->handleFailure();
            throw new Exception("Falha ao consultar o serviço autorizador.");
        }
    }

    private function handleFailure(): void
    {
        $failures = Cache::increment(self::CACHE_KEY . '_failures');
        if ($failures >= self::FAILURE_LIMIT) {
            Cache::put(self::CACHE_KEY . '_open', true, now()->addSeconds(self::TIMEOUT_SECONDS));
        }
    }
}
