<?php

namespace Core\Loaders;

use Core\Exceptions\JsonQueryBuilderException;
use Core\JsonQueryBuilder\Contracts\IJsonApiLoader;

class JsonApiApiLoader implements IJsonApiLoader {

    /**
     * @param string $endpoint
     * @return array
     * @throws \JsonException
     */
    public function fetchData(string $endpoint): array{

        $response = @file_get_contents($endpoint);

        if($response === false){
            throw new JsonQueryBuilderException("Failed to fetch data from API: $endpoint");
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonQueryBuilderException("Error decoding JSON: " . json_last_error_msg());
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonQueryBuilderException("Error decoding JSON: " . json_last_error_msg());
        }

        return $data;
    }
}