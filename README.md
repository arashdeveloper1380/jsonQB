# JsonQueryBuilder

```php
$queryBuilder = new JsonQueryBuilder($data);

OR
//use helper function
jsQB()->metods()...

$result = $queryBuilder->select(['id', 'name'])->get();

result:
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


$result = $queryBuilder->where('age', '>', 30);
// Output: data older than 30

$queryBuilder->where('age', '>', 30)->where('status', '=', 'active');
// Output: data whose age is greater than 30 and whose status is active.

$queryBuilder->where('age', '>', 30)->orWhere('status', '=', 'active');
// Output: data that is either older than 30 or their status is active.

$queryBuilder->search('name', 'arash');
// Output: The data contained in the name field is 'arash'.

$queryBuilder->limit(10);
// Output: only the first 10 records.

$queryBuilder->skip(5);
// Output: Data starts at record number 6.

$queryBuilder->groupBy('category');
// Output: The data is grouped by the category field.

$totalAge = $queryBuilder->aggregate('age', 'sum'); 
// Output: Sum of ages.

$averageAge = $queryBuilder->aggregate('age', 'avg');
// Output: avg of ages.

$data = $queryBuilder->get();
// Output: Returns data as an array or object

$json = $queryBuilder->toJson();
// Output: The data is converted to JSON format.

$pageData = $queryBuilder->paginate(1, 10);
// Output: first page data with 10 records.

$queryBuilder->orderBy('name', 'asc');
// Output: The data is sorted in ascending order based on the name field.

$queryBuilder->groupBy('category')->having('count(*)', '>', 5);
// Output: Groups whose number is more than 5.

$queryBuilder->join('orders', 'users.id', '=', 'orders.user_id');
// Output: the data that is connected from the two tables users and orders.

$queryBuilder->distinct()->select(['email']);
// Output: only unique emails.

$queryBuilder->groupBy('category')->havingRaw('COUNT(*) > 5');
// Output: Groups whose number is more than 5.

$queryBuilder->whereIn('id', [41,45])->get();
//The IN operator allows you to specify multiple values in a WHERE clause

$queryBuilder->sort('price', 'desc')->get();
//sort value

$queryBuilder->keys();
//get keys of values

$queryBuilder->newest('creationAt');
$queryBuilder->oldest('creationAt');
// get data with sortng date

$queryBuilder->randomGet()
//get random data

$queryBuilder->randomFirst()
//get first random data

//example use chain methods
$data = $queryBuilder
    ->select(['id', 'name', 'email'])
    ->where('age', '>', 30)
    ->orWhere('status', '=', 'active')
    ->search('name', 'John')
    ->limit(10)
    ->skip(5)
    ->groupBy('category')
    ->orderBy('name', 'asc')
    ->get();
