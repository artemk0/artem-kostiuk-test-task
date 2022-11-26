<?php

declare(strict_types=1);

namespace App\Client;

use App\Exceptions\CanNotGetResponseFrom3rdParty;

class SimpleJsonHttpClient
{
    public function sendRequest(string $uri, ?array $streamContextOptions = null): array
    {
        if ($streamContextOptions !== null) {
            $streamContextOptions = stream_context_create($streamContextOptions);
        }

        $response = $this->getResponse($uri, false, $streamContextOptions);

        if (!$response) {
            throw new CanNotGetResponseFrom3rdParty('Empty response');
        }

        $result = json_decode($response, true);
        if ($result === null) {
            throw new CanNotGetResponseFrom3rdParty('Can not parse JSON. Error: ' . json_last_error_msg());
        }

        return $result;
    }

    public function getResponse(
        string $filename,
        bool $use_include_path = false,
        $context = null,
        int $offset = 0,
        ?int $length = null
    ) {
        return file_get_contents($filename, $use_include_path, $context, $offset, $length);
    }
}
