<?php
namespace Wpails\Config;
function routes() :array{
  return [
    ['slug'=>'', 'redirect'=>'/hello/world'],
    ['slug'=>'hello', 'is_namespace'=>true,
      'ancestors'=>[
        ['slug'=>'world', 'title'=>'Hello World!'],
      ]
    ],
  ];
}
