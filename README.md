# JsonQueryBuilder

### 1. **select()**

```php
$queryBuilder = new JsonQueryBuilder($data);

$result = $queryBuilder->select(['id', 'name'])->get();
