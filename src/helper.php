<?php

if(!function_exists('jsQB')){
    function jsQB(): \Core\JsonQueryBuilder{
        $loader = new \Core\Loaders\JsonApiApiLoader();
        return new \Core\JsonQueryBuilder($loader);
    }
}
