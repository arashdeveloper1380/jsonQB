<?php

it('can fetch all data', function (){
    $result = jsQB()
        ->from('https://api.escuelajs.co/api/v1/products')
        ->count();

    expect($result)->toBe(84);
});