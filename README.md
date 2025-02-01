# JsonQueryBuilder

### 1. **select()**

```php
$queryBuilder = new JsonQueryBuilder($data);

OR
//use helper function
jsQB()->metods()...

$result = $queryBuilder->select(['id', 'name'])->get();

