<?php

namespace Api\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Aelion\Http\Response\JsonResponse;
use Aelion\Http\Response\HttpResponseStatus;

class Middleware {
    private $secretKey;

    public function __construct() {
        $this->secretKey = $_ENV['JWT_KEY'];
    }

    public function handle($request, $next) {
        $authHeader = $request->getHeader('Authorization');
        
        if (!$authHeader)
            return $this->unauthorizedResponse();

        $token = str_replace('Bearer ', '', $authHeader);
        
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $request->user = $decoded;
            return $next($request);
        } catch(\Exception $e) {
            return $this->unauthorizedResponse();
        }
    }

    private function unauthorizedResponse() {
        $response = new JsonResponse();
        $response->setStatus(HttpResponseStatus::Unauthorized);
        $response->setPayload(['message' => 'Unauthorized']);
        return $response;
    }
}