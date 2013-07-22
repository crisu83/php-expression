php-expression
==============

A modest library for safe evaluation of PHP expressions.

## Why would I want that?

Sometimes you cannot avoid using eval in php, e.g. if you have a rules engine that uses php expressions and its business rules. When you need to use eval you want a safe environment to do so, that's where php-expression comes in. Using php-expression you can check the syntax, safely test your expressions before evaluating them and catch exceptions that may occur. By default php-expression does not allow to use any language constructs or functions, but you can configure it to allow anything you want.

## How do I use it?

```php
<?php

// Require the library files.
// Alternatively you can require it using Composer (http://getcomposer.org/).
require(__DIR__ . '/path/to/php-expression/Expression.php');
require(__DIR__ . '/path/to/php-expression/Exception/Fatal.php');
require(__DIR__ . '/path/to/php-expression/Exception/NotSafe.php');
require(__DIR__ . '/path/to/php-expression/Exception/Runtime.php');
require(__DIR__ . '/path/to/php-expression/Exception/Syntax.php');

// Define an example class that we will use in this example.
class Test
{
  public function foobar()
  {
    return 'foobar';
  }
}

// This is code that we will run through the expression object.
$code = '$test = new Test; return $test->foobar();';

// Create a new expression for the code above.
$exp = new \Crisu83\PhpExpression\Expression($code);

// Allow the 'new' keyword to be used in the expression.
$exp->setAllowedKeywords(array('new', 'return'));

// Allow the class 'Test' to be used in the expression.
$exp->setAllowedClassNames(array('Test'));

// Allow the method 'foobar' to be called in the expression.
$exp->setAllowedFunctions(array('foobar'));

// Evaluate the code and catch any exceptions that may occur.
$result = null;
try {
  $result = $exp->evaluate();
} catch (Exception $e) {
  // In this example we just print the error message.
  echo $e->getMessage();
}

// Output the result which is 'foobar'.
echo $result;
```
