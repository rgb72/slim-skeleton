<?php

namespace App\Helpers\JsonWebToken;

use Firebase\JWT\JWT;

class JsonWebToken {

    private $key;

    private $token;

    private $lifetime = 2 * 60;

    private $expired_at;

    private $error_message;

    public function __construct($key) {
        $this->token = (object) [];
        $this->setKey($key);
    }

    public function __set($property, $value) {
        $this->token->{$property} = $value;
    }

    public function encode() {
        $token = (object) array_merge((array) $this->token, (array) $this->getDefaultToken());
        return JWT::encode($token, $this->key);
    }

    public function decode($jwt) {
        try {
            $this->token = JWT::decode($jwt, $this->key, ['HS256']);
            return $this->token;
        } catch (\Exception $exception) {
            $this->error_message = $exception->getMessage();
            return false;
        }
    }

    protected function getDefaultToken() {
        return [
            'iat' => \Carbon\Carbon::now()->timestamp,
            'nbf' => \Carbon\Carbon::now()->timestamp,
            'exp' => $this->getExpiredAt()->timestamp
        ];
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function getKey() {
        return $this->key;
    }

    public function setLifetime($lifetime) {
        if(!is_int($lifetime))
            throw new \InvalidArgumentException('parameter must be integer. Input was: '.$lifetime);

        $this->lifetime = $lifetime;
    }

    public function getLifetime() {
        return $this->lifetime;
    }

    public function setExpiredAt($expired_at) {
        if($expired_at instanceOf \Carbon\Carbon === false)
            throw new \InvalidArgumentException('parameter must be instance of \Carbon\Carbon.');

        $this->expired_at = $expired_at;
    }

    public function getExpiredAt() {
        return $this->expired_at ?: \Carbon\Carbon::now()->addMinutes($this->lifetime);
    }

    public function getErrorMessage() {
        return $this->error_message;
    }

}
