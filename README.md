# JsonQueryBuilder

### 1. **select()**

```php
$queryBuilder = new JsonQueryBuilder($data);

OR
//use helper function
jsQB()->metods()...

$result = $queryBuilder->select(['id', 'name'])->get();

[
  {
    "id": 1,
    "name": "حسین نیکو"
  },
  {
    "id": 2,
    "name": "فاطمه رضایی"
  }
]

