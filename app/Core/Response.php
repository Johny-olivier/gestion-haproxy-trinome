<?php

namespace App\Core;

class Response {
    private array $data;
    private int $statusCode;

    public function __construct(array $data, int $statusCode = 200) {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    /**
     * Crée une réponse JSON standardisée.
     */
    public static function json(mixed $data = null, int $statusCode = 200, string $message = 'Opération effectuée avec succès.' ): self {
        return new self([
            'success' => $statusCode >= 200 && $statusCode < 300,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Envoie la réponse HTTP au navigateur.
     */
    public function send(): void {
        http_response_code($this->statusCode);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $this->data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Retourne le code HTTP de la réponse.
     */
    public function getStatusCode(): int {
        return $this->statusCode;
    }
}