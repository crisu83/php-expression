php-expression
==============

Modest library for cleaning up evaluated PHP expressions.

## Usage

```
<?php

// Require the library files.
require(__DIR__ . '/path/to/php-expression/Expression.php');
require(__DIR__ . '/path/to/php-expression/Exception/Fatal.php');
require(__DIR__ . '/path/to/php-expression/Exception/NotSafe.php');
require(__DIR__ . '/path/to/php-expression/Exception/Runtime.php');
require(__DIR__ . '/path/to/php-expression/Exception/Syntax.php');

function foobar() {
  return 'foobar';
}

$exp = new \Crisu83\PhpExpression\Expression();
$exp->setAllowedFunctions(array('asd'));
$result = $exp->evaluate();

echo $result; // outputs 'foobar'.

```
