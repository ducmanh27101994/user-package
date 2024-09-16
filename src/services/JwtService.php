<?php

namespace FmcExample\UserPackage\services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class JwtService
{
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = env('JWT_SECRET_KEY');
    }

    public function createToken(array $payload)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expirationTime;

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function decodeToken($jwt)
    {
        try {
            return JWT::decode($jwt, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
