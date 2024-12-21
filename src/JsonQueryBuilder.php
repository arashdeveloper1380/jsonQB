<?php

namespace Core;

use Core\Helpers\ConditionEvaluator;
use Core\JsonQueryBuilder\Contracts\IJsonQueryBuilder;
use Core\Loaders\JsonApiApiLoader;

final class JsonQueryBuilder implements IJsonQueryBuilder {

    protected JsonApiApiLoader $loader;
    protected array $query = [];
    protected string $endpoint;

    public function __construct(JsonApiApiLoader $loader){
        $this->loader = $loader;
    }

    public function select(array $fields): IJsonQueryBuilder{
        $this->query['select'] = $fields;
        return $this;
    }

    public function from(string $resource): IJsonQueryBuilder{
        $this->endpoint = $resource;
        return $this;
    }

    public function where(string $key, string $operator, mixed $value): IJsonQueryBuilder{
        $this->query['where'][] = compact('key', 'operator', 'value');
        return $this;
    }

    public function find(string $key, mixed $value): IJsonQueryBuilder{
        return $this->where($key, '=', $value);
    }

    public function limit(int $limit): IJsonQueryBuilder{
        $this->query['limit'] = $limit;
        return $this;
    }


    public function get(): array{
        $data = $this->loader->fetchData($this->endpoint);
        return $this->applyQuery($data);
    }

    public function count(): int{
        return count($this->get());
    }

    public function exists(): bool{
        return $this->count() > 0;
    }

    public function first(): ?array{
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }

    public function search(string $key, string $value) : self{
        $this->query['search'][] = compact('key', 'value');
        return $this;
    }

    public function like(string $key, string $pattern): self {
        $escapedPattern = str_replace('%', '.*', preg_quote($pattern, '/'));
        $this->query['like'][] = [
            'key'   => $key,
            'value' => $escapedPattern,
        ];
        return $this;
    }

    public function filter(callable $callable) : self{
        $this->query['filter'][] = $callable;
        return $this;
    }

    public function aggregate(string $key, string $function): mixed {
        $data = $this->get();
        return $this->applyAggregate($data, $key, $function);
    }

    public function pluck(string $field): array {
        return array_column($this->get(), $field);
    }

    public function distinct(string $key): self {
        $this->query['distinct'] = $key;
        return $this;
    }

    public function reset(): IJsonQueryBuilder{
        $this->query = [];
        return $this;
    }

    public function toJson(): string{
        return json_encode($this->get());
    }

    public function fetch(): array{
        return $this->get();
    }

    public function paginate(int $page, int $per_page): array{
        $data = $this->get();
        $offset = ($page -1) * $per_page;

        return array_slice($data, $offset, $page);
    }

    protected function applyQuery(array $data): array{
        if(!empty($this->query['select'])){
            $data = $this->applySelect($data);
        }

        if (!empty($this->query['where'])) {
            $data = $this->applyWhere($data);
        }

        if (!empty($this->query['distinct'])) {
            $data = $this->applyDistinct($data);
        }

        if (!empty($this->query['search'])) {
            $data = $this->applySearch($data);
        }

        if (!empty($this->query['like'])) {
            $data = $this->applyLike($data);
        }

        if(!empty($this->query['filter'])){
            $data = $this->applyFilter($data);
        }

        if (!empty($this->query['limit'])) {
            $data = $this->applyLimit($data);
        }

        return array_values($data);
    }

    private function applySelect(array $data): array{
        $selectedKeys = array_flip($this->query['select']);

        return array_map(static function ($item) use ($selectedKeys) {
            return array_intersect_key($item, $selectedKeys);
        }, $data);
    }

    private function applyWhere(array $data): array{
        return array_filter($data, function ($item) {
            foreach ($this->query['where'] as $condition) {
                if (!ConditionEvaluator::evaluate($item, $condition['key'], $condition['operator'], $condition['value'])) {
                    return false;
                }
            }
            return true;
        });
    }

    private function applyLimit(array $data): array{
        return array_slice($data, 0, $this->query['limit']);
    }

    private function applySearch(array $data) : array{

        foreach ($this->query['search'] as $search) {
            $data = array_filter($data, static function ($item) use ($search) {
                $fieldValue = $item[$search['key']] ?? null;

                if (is_array($fieldValue)) {
                    foreach ($fieldValue as $value) {
                        if (stripos($value, $search['value']) !== false) {
                            return true;
                        }
                    }
                    return false;
                }

                return is_string($fieldValue) && stripos($fieldValue, $search['value']) !== false;
            });
        }
        return $data;
    }

    private function applyLike(array $data) : array {
        foreach ($this->query['like'] as $search) {
            $data = array_filter($data, static function ($item) use ($search) {
                $fieldValue = $item[$search['key']] ?? null;

                if ($fieldValue === null) {
                    return false;
                }

                if (is_array($fieldValue)) {
                    foreach ($fieldValue as $value) {
                        if (is_string($value) && preg_match('/^' . $search['value'] . '$/i', $value)) {
                            return true;
                        }
                    }
                    return false;
                }

                return is_string($fieldValue) && preg_match('/^' . $search['value'] . '$/i', $fieldValue);
            });
        }
        return $data;
    }

    private function applyFilter(array $data) : array {
        foreach ($this->query['filter'] as $callback){
            return array_filter($data, $callback);
        }
        return $data;
    }

    private function applyAggregate(array $data, string $key, string $function) : mixed{
        $values = array_column($data, $key);

        return match (strtolower($function)) {
            'sum' => array_sum($values),
            'avg' => array_sum($values) / count($values),
            'max' => max($values),
            'min' => min($values),
            'count' => count($values),
            default => null,
        };
    }

    private function applyDistinct(array $data): array {
        $fieldPath = explode('.', $this->query['distinct'] ?? '');
        if (empty($fieldPath)) {
            return $data;
        }

        $uniqueValues = [];
        foreach ($data as $item) {
            $value = $this->getNestedValue($item, $fieldPath);
            if ($value !== null && !in_array($value, $uniqueValues, true)) {
                $uniqueValues[] = $value;
            }
        }

        return array_map(static fn($value) => [$fieldPath[count($fieldPath) - 1] => $value], $uniqueValues);
    }

    private function getNestedValue(array $item, array $path) {
        foreach ($path as $key) {
            if (!is_array($item) || !array_key_exists($key, $item)) {
                return null;
            }
            $item = $item[$key];
        }
        return $item;
    }
}