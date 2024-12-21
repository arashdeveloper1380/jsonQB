<?php

namespace Core\JsonQueryBuilder\Contracts;

interface IJsonApiLoader{

    /**
     * @param string $endpoint
     * @return array
     */
    public function fetchData(string $endpoint) : array;
}