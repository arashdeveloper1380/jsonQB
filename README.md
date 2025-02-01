# JsonQueryBuilder

### 1. **select()**

[
{
  "data": [
    {
      "id": 1,
      "name": "حسین نیکو",
      "email": "hossein.nikoo@example.com",
      "age": 35,
      "status": "active",
      "category": "مدیر",
      "nickname": "حسین قهرمان",
      "favorite_food": "کباب",
      "hobby": "بازی شطرنج"
    },
    {
      "id": 2,
      "name": "فاطمه رضایی",
      "email": "fatemeh.rezaei@example.com",
      "age": 28,
      "status": "inactive",
      "category": "کاربر",
      "nickname": "فاطی",
      "favorite_food": "قورمه سبزی",
      "hobby": "رقص ایرانی"
    },
    {
      "id": 3,
      "name": "محمود موسوی",
      "email": "mahmoud.mousavi@example.com",
      "age": 40,
      "status": "active",
      "category": "مدیر",
      "nickname": "محمود نترس",
      "favorite_food": "باقالی پلو",
      "hobby": "شکار"
    },
    {
      "id": 4,
      "name": "سارا بهرامی",
      "email": "sara.bahrami@example.com",
      "age": 25,
      "status": "active",
      "category": "کاربر",
      "nickname": "ساروی",
      "favorite_food": "آش رشته",
      "hobby": "کارتن تماشا"
    },
    {
      "id": 5,
      "name": "یوسف طاهری",
      "email": "yousef.taheri@example.com",
      "age": 33,
      "status": "inactive",
      "category": "مهمان",
      "nickname": "یوسف فلافل",
      "favorite_food": "دیزی",
      "hobby": "دوچرخه‌سواری"
    },
    {
      "id": 6,
      "name": "مریم احمدی",
      "email": "maryam.ahmadi@example.com",
      "age": 29,
      "status": "active",
      "category": "کاربر",
      "nickname": "مریم جان",
      "favorite_food": "خورش بادمجان",
      "hobby": "خوانندگی"
    }
  ]
}
]

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



