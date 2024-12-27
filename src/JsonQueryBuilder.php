<?php

namespace Core;

use Core\Exceptions\JsonQueryBuilderException;
use Core\Helpers\ConditionEvaluator;
use Core\JsonQueryBuilder\Contracts\IJsonQueryBuilder;
use Core\Loaders\JsonApiApiLoader;

final class JsonQueryBuilder implements IJsonQueryBuilder {

    protected JsonApiApiLoader $loader;
    protected array $query = [];
    protected string $endpoint;

    private bool $isRequestValue = false;

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

    public function whereIn(string $key, array $values) : self{
        $this->query['where_in'][] = compact('key', 'values');
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

    public function skip(int $count): self {
        $this->query['skip'] = $count;
        return $this;
    }

    public function dostExists() : bool{
        return !$this->exists();
    }

    public function first(): self {
        $this->limit(1);
        $result = $this->get();

        $this->query['first'] = $result[0] ?? null;

        return $this;
    }

    public function value(string $key): mixed {
        $data = $this->query['first'] ?? null;
        return $data[$key] ?? null;
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

    public function pluck(string $key): array {
        return array_column($this->get(), $key);
    }

    public function groupBy(string $key): self {
        $this->query['group_by'] = $key;
        return $this;
    }

    public function distinct(string $key): self {
        $this->query['distinct'] = $key;
        return $this;
    }

    public function having(string $aggregate, string $operator, mixed $value): self {
        $this->query['having'][] = compact('aggregate', 'operator', 'value');
        return $this;
    }

    public function latest(): mixed {
        $data = $this->get();
        return end($data);
    }

    public function oldest(string $time) : ? array{
        $data = $this->get();

        if (!isset($data[0][$time])) {
            throw new JsonQueryBuilderException("Field '{$time}' does not exist in the data.");
        }

        usort($data, static function ($a, $b) use ($time) {
            return strtotime($a[$time]) - strtotime($b[$time]);
        });

        return reset($data);
    }

    public function newest(string $time) : ? array{
        $data = $this->get();

        if (!isset($data[0][$time])) {
            throw new JsonQueryBuilderException("Field '{$time}' does not exist in the data.");
        }

        usort($data, static function ($a, $b) use ($time) {
            return strtotime($b[$time]) - strtotime($a[$time]);
        });

        return reset($data);
    }

    public function randomFirst() : mixed{
        $data = $this->get();
        $rand = array_rand($data);

        return $data[$rand];
    }

    public function randomGet() : ? array {
        $data = $this->get();
        shuffle($data);
        return $data;
    }

    public function reset(): IJsonQueryBuilder{
        $this->query = [];
        return $this;
    }

    public function keys() : ? array{
        $data = $this->get();
        $keys = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $keys = array_merge($keys, array_keys($item));
            }
        }

        return array_unique($keys);
    }

    public function sort(string $key, string $direction = 'asc') : self {
        $this->query['sort'] = [
            'key'       => $key,
            'direction' => strtolower($direction) === 'desc' ? SORT_DESC : SORT_ASC
        ];
        return $this;
    }

    public function transform(callable $callback) : self{
        $this->query['transform'] = $callback;
        return $this;
    }

    public function tap(callable $callback): self{
        $callback($this);
        return $this;
    }


    public function toJson(): string{
        return json_encode($this->get());
    }

    public function fetch(): array{
        return $this->get();
    }

    public function paginate(int $page, int $perPage): array {
        $data = $this->get();
        $total = count($data);
        $offset = ($page - 1) * $perPage;

        $pagedData = array_slice($data, $offset, $perPage);

        return [
            'data' => $pagedData,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ],
        ];
    }

    protected function applyQuery(array $data): array{
        if(!empty($this->query['select'])){
            $data = $this->applySelect($data);
        }

        if (!empty($this->query['where'])) {
            $data = $this->applyWhere($data);
        }

        if (!empty($this->query['where_in'])) {
            $data = $this->applyWhereIn($data);
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

        if(!empty($this->query['group_by'])){
            $data = $this->applyGroupBy($data);
        }

        if(!empty($this->query['having'])){
            $data = $this->applyHaving($data);
        }

        if (!empty($this->query['skip'])) {
            $data = $this->applySkip($data);
        }

        if(!empty($this->query['sort'])){
            $data = $this->applySort($data);
        }

        if(!empty($this->query['transform'])){
            $data = $this->applyTransform($data);
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

            foreach ($this->query['where_in'] ?? [] as $condition) {
                if (!in_array($item[$condition['key']] ?? null, $condition['values'], true)) {
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

    private function applyGroupBy(array $data): array {
        $fieldPath = explode('.', $this->query['group_by']);
        $grouped = [];

        foreach ($data as $item) {
            $key = $this->getNestedValue($item, $fieldPath);
            if ($key !== null) {
                $grouped[$key][] = $item;
            }
        }

        return $grouped;
    }

    private function applyHaving(array $data): array {
        $result = [];
        foreach ($data as $groupKey => $groupItems) {
            foreach ($this->query['having'] as $condition) {
                $field = $condition['aggregate'];
                $value = match ($field) {
                    'count' => count($groupItems),
                    'sum' => array_sum(array_column($groupItems, $condition['aggregate'])),
                    'avg' => count($groupItems) > 0
                        ? array_sum(array_column($groupItems, $condition['aggregate'])) / count($groupItems)
                        : 0,
                    'max' => max(array_column($groupItems, $condition['aggregate'])),
                    'min' => min(array_column($groupItems, $condition['aggregate'])),
                    'distinct_count' => count(array_unique(array_column($groupItems, $condition['aggregate']))),
                    'exists' => !empty($groupItems),
                    default => null,
                };
                if (!ConditionEvaluator::evaluate(['value' => $value], 'value', $condition['operator'], $condition['value'])) {
                    continue 2;
                }
            }
            $result[$groupKey] = $groupItems;
        }
        return $result;
    }

    public function applySkip(array $data) : ? array{
        $skipCount = $this->query['skip'] ?? 0;
        return array_slice($data, $skipCount);
    }

    public function applySort(array $data) : ? array {
        if (isset($this->query['sort'])) {

            $key = $this->query['sort']['key'];
            $direction = $this->query['sort']['direction'];

            usort($data, function($a, $b) use ($key, $direction) {
                $valueA = $a[$key] ?? null;
                $valueB = $b[$key] ?? null;

                if ($valueA === null && $valueB === null) {
                    return 0;
                }
                if ($valueA === null) {
                    return 1;
                }
                if ($valueB === null) {
                    return -1;
                }

                return $direction === SORT_DESC ? $valueB <=> $valueA : $valueA <=> $valueB;
            });
        }

        return $data;
    }

    public function applyTransform(array $data) : ? array{

        if (isset($this->query['transform']) && is_callable($this->query['transform'])) {
            $callback = $this->query['transform'];

            $data = array_map(static function ($item) use ($callback) {
                return $callback($item);
            }, $data);
        }

        return $data;
    }

    public function applyWhereIn(array $data) : ? array{
        return array_filter($data, function ($item) {

            foreach ($this->query['where_in'] ?? [] as $condition) {
                if (!in_array($item[$condition['key']] ?? null, $condition['values'], true)) {
                    return false;
                }
            }
            return true;
        });
    }

    private function isTimestamps(string $timestamp) : bool{
        try {
            $dateTime = new \DateTime($timestamp);
            return $dateTime->format('c') === $timestamp;
        } catch (\Exception $e) {
            return false;
        }
    }

}