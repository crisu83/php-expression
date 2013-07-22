<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// Register the composer autoloader..
require(__DIR__ . '/../vendor/autoload.php');

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