<?php

namespace response;

class Response {
    
    public static function Return200($response, $data) {
        $response = $response->withStatus(200);
        $response->getBody()->write(json_encode([
            'status' => 200,
            'message' => 'Ok',
            'data' => $data
        ]));

        return $response;
    }

    public static function Return201($response, $data) {
        $response = $response->withStatus(201);
        $response->getBody()->write(json_encode([
            'status' => 201,
            'message' => 'Created',
            'data' => $data
        ]));

        return $response;
    }

    public static function Return400($response, $data) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode([
            'status' => 400,
            'message' => 'Bad Request',
            'data' => $data
        ]));

        return $response;
    }

    public static function Return401($response, $data) {
        $response = $response->withStatus(401);
        $response->getBody()->write(json_encode([
            'status' => 401,
            'message' => 'Unauthorized',
            'data' => $data
        ]));

        return $response;
    }
}