<?php

namespace Core\JsonQueryBuilder\Contracts;

interface IJsonQueryBuilder{

    /**
     * @param array $fields
     * @return $this
     */
    public function select(array $fields) : self;

    /**
     * @param string $resource
     * @return $this
     */
    public function from(string $resource) : self;

    /**
     * @param string $key
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    public function where(string $key, string $operator, mixed $value) : self;

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function find(string $key, mixed $value) : self;

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit) : self;

    /**
     * @return array
     */
    public function get() : array;

    /**
     * @return int
     */
    public function count() : int;

    /**
     * @return bool
     */
    public function exists() : bool;

    /**
     * @return array|null
     */
    public function first() : ? array;

    /**
     * @param string $key
     * @param string $value
     * @return array|null
     */
    public function search(string $key, string $value) : ? self;

    /**
     * @param string $key
     * @param string $pattern
     * @return $this
     */
    public function like(string $key, string $pattern): self;

    /**
     * @return $this
     */
    public function reset(): self;

    /**
     * @return string
     */
    public function toJson(): string;

    /**
     * @return array
     */
    public function fetch(): array;

    /**
     * @param int $page
     * @param int $per_page
     * @return array
     */
    public function paginate(int $page, int $per_page) : array;

    /**
     * @param callable $callable
     * @return $this
     */
    public function filter(callable $callable) : self;

    /**
     * @param string $key
     * @param string $function
     * @return mixed
     */
    public function aggregate(string $key, string $function): mixed;

    /**
     * @param string $field
     * @return array
     */
    public function pluck(string $field): array;

    /**
     * @param string $key
     * @return $this
     */
    public function distinct(string $key): self;

}