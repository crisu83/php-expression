php-expression
==============

A modest library for safe evaluation of PHP expressions.

## Usage

```php
<?php

// Require the library files.
require(__DIR__ . '/path/to/php-expression/Expression.php');
require(__DIR__ . '/path/to/php-expression/Exception/Fatal.php');
require(__DIR__ . '/path/to/php-expression/Exception/NotSafe.php');
require(__DIR__ . '/path/to/php-expression/Exception/Runtime.php');
require(__DIR__ . '/path/to/php-expression/Exception/Syntax.php');

// Define an example function that we will evaluate.
function foobar() {
  return 'foobar';
}

// This is code that we will run through the expression object.
$code = 'return foobar();';

// Create a new expression for the code above.
$exp = new \Crisu83\PhpExpression\Expression($code);

// By default no functions are allowed to be run when evaluating the expression
// so we need to explicitly allow the expression to run the 'foobar' function.
$exp->setAllowedFunctions(array('foobar'));

// Evaluate the code and catch any exceptions that occur.
$result = null;
try {
  $result = $exp->evaluate();
} catch (Exception $e) {
  // Do whatever you want with the exception, in this example we will just ignore it.
}

// Output the result which is 'foobar'.
echo $result;

```
