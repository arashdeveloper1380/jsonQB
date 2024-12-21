<?php

use Core\Loaders\JsonApiApiLoader;
use Core\JsonQueryBuilder;

require 'vendor/autoload.php';

$data = jsQB()
    ->from('https://api.escuelajs.co/api/v1/products')
//    ->aggregate('price','count');
//    ->filter( function ($item){
//        return $item['price'] > 50 && $item['category']['name'] === 'Shoes';
//    })
//    ->limit(2)
//    ->get();
//    ->pluck('title');
//    ->distinct('title')->get();
    ->distinct('category.name')->get();

dump($data);