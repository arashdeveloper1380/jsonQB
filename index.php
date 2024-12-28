<?php

use Core\Loaders\JsonApiApiLoader;
use Core\JsonQueryBuilder;

require 'vendor/autoload.php';

$data = jsQB()
    ->from('https://api.escuelajs.co/api/v1/products')

    ->get();
//    ->whereNotIn('id', [16, 19])
//->whereNotBetween('price', [20,30])
//    ->whereBetween('price', [20,30])
//    ->tap(function ($query){
//        $query->limit(20);
//    })
//    ->get();
//    ->tap(function ($query){
//        echo "get products";
//    })
//    ->get();
//    ->whereIn('id', [41,45])->get();
//    ->transform(function ($product) {
//        $product['title'] = strtolower($product['title']);
//        return $product;
//    })
//    ->get();
//    ->sort('price', 'desc')->get();
//    ->keys();
//    ->skip(5)->limit(2)->get();
//    ->newest('creationAt');
//    ->oldest('creationAt');
//    ->randomGet();
//    ->randomFirst();
//    ->paginate(2,3);
//    ->aggregate('price','count');
//    ->filter( function ($item){
//        return $item['price'] > 50 && $item['category']['name'] === 'Shoes';
//    })
//    ->limit(2)
//    ->get();
//    ->pluck('title');
//    ->distinct('title')->get();
//    ->distinct('category.name')->get();
//    ->groupBy('category.name')
//    ->having('count', '>', 2)
//    ->first()->value('id');

dump($data);

//foreach ($data as $item){
//    print_r("ID is : " . $item['id']);
//}