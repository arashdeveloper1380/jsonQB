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
     * @param array $values
     * @return $this
     */
    public function whereNotIn(string $key, array $values): self;
    /**
     * @param string $key
     * @param array $range
     * @return $this
     */
    public function whereBetween(string $key, array $range): self;

    /**
     * @param string $key
     * @param array $range
     * @return $this
     */
    public function whereNotBetween(string $key, array $range): self;

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
    public function first() : self | array;

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
     * @param int $perPage
     * @return array
     */
    public function paginate(int $page, int $perPage): array;

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
     * @param string $key
     * @return array
     */
    public function pluck(string $key): array;

    /**
     * @param string $key
     * @return $this
     */
    public function groupBy(string $key): self;

    /**
     * @param string $key
     * @return $this
     */
    public function distinct(string $key): self;

    /**
     * @param string $aggregate
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    public function having(string $aggregate, string $operator, mixed $value): self;

    /**
     * @return mixed
     */
    public function latest(): mixed;

    /**
     * @return mixed
     */
    public function randomFirst() : mixed;

    /**
     * @return array|null
     */
    public function randomGet() : ? array;

    /**
     * @param string $time
     * @return array|null
     */
    public function oldest(string $time) : ? array;

    /**
     * @param string $time
     * @return array|null
     */
    public function newest(string $time) : ? array;

    /**
     * @return bool
     */
    public function dostExists() : bool;

    /**
     * @param int $count
     * @return $this
     */
    public function skip(int $count): self;

    /**
     * @return array|null
     */
    public function keys() : ? array;

    /**
     * @param string $key
     * @param string $direction
     * @return $this
     */
    public function sort(string $key, string $direction = 'asc') : self;

    /**
     * @param callable $callback
     * @return $this
     */
    public function transform(callable $callback) : self;

    /**
     * @param string $key
     * @param array $values
     * @return $this
     */
    public function whereIn(string $key, array $values) : self;

    /**
     * @param callable $callback
     * @return $this
     */
    public function tap(callable $callback): self;




}