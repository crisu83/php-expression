<?php
/*
 * This file is part of PhpExpression.
 *
 * (c) 2013 Christoffer Niska
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crisu83\PhpExpression;

use Crisu83\PhpExpression\Exception\Fatal;
use Crisu83\PhpExpression\Exception\NotSafe;
use Crisu83\PhpExpression\Exception\Runtime;
use Crisu83\PhpExpression\Exception\Syntax;

/**
 * Class that represents a php expression.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 */ 
class Expression
{
    /**
     * @var string php code.
     */
    protected $_code;

    /**
     * @var array list of class names that are allowed to be called (defaults to none).
     */
    protected $_allowedClassNames = array();

    /**
     * @var array list of names for function that are allowed to be called (defaults to none).
     */
    protected $_allowedFunctions = array();

    /**
     * @var array list of keywords that are allowed to be used (defaults to none).
     */
    protected $_allowedKeywords = array();

    /**
     * @var array list of reserved keywords in php.
     */
    protected $_keywords = array(
        '__halt_compiler', 
        'abstract', 
        'and', 
        'array', 
        'as', 
        'break', 
        'callable', 
        'case', 
        'catch', 
        'class', 
        'clone', 
        'const', 
        'continue', 
        'declare', 
        'default', 
        'die', 
        'do', 
        'echo', 
        'else', 
        'elseif', 
        'empty', 
        'enddeclare', 
        'endfor', 
        'endforeach', 
        'endif', 
        'endswitch', 
        'endwhile', 
        'eval', 
        'exit', 
        'extends', 
        'final', 
        'for', 
        'foreach', 
        'function', 
        'global', 
        'goto', 
        'if', 
        'implements', 
        'include', 
        'include_once', 
        'instanceof', 
        'insteadof', 
        'interface', 
        'isset', 
        'list', 
        'namespace', 
        'new', 
        'or', 
        'print', 
        'private', 
        'protected', 
        'public', 
        'require', 
        'require_once', 
        'return', 
        'static', 
        'switch', 
        'throw', 
        'trait', 
        'try', 
        'unset', 
        'use', 
        'var', 
        'while', 
        'xor',
    );

    /**
     * Creates a new expression.
     * @param string $this->_code php code.
     */
    public function __construct($code)
    {
        $this->_code = $code;
    }

    /**
     * Checks if the code returns a boolean when evaluated.
     * @return boolean the result.
     */
    public function returnsBoolean()
    {
        return is_bool($this->evaluate());
    }

    /**
     * Checks if the code returns a floating point number when evaluated.
     * @return boolean the result.
     */
    public function returnsFloat()
    {
        return is_float($this->evaluate());
    }

    /**
     * Checks if the code returns an integer when evaluated.
     * @return boolean the result.
     */
    public function returnsInteger()
    {
        return is_int($this->evaluate());
    }

    /**
     * Checks if the code returns a string when evaluated.
     * @return boolean the result.
     */
    public function returnsString()
    {
        return is_string($this->evaluate());
    }

    /**
     * Evaluates the expression using eval().
     * @return mixed the result.
     * @throws Runtime if t
     */
    public function evaluate()
    {
        $this->isSafe();
        $this->checkSyntax();
        return eval($this->_code);
    }

    /**
     * @return boolean whether the code is safe to be evaluated.
     * @throws NotSafe if the code is not considered safe.
     */
    public function isSafe()
    {
        // Make sure that no potentially harmful classes are called in the expression.
        foreach ($this->notAllowedClassNames() as $className)
            if (preg_match("/\s?$className\s*\(?/", $this->_code) > 0)
                throw new NotSafe(sprintf('Code contains class name "%s" and is not considered safe.', $className));
        // Make sure that no potentially harmful keywords are used in the expression.
        foreach ($this->notAllowedKeywords() as $keyword)
            if (preg_match("/\s?$keyword\s*\(?/", $this->_code) > 0)
                throw new NotSafe(sprintf('Code contains keyword "%s" and is not considered safe.', $keyword));
        // Make sure that no potentially harmful functions are called in the expression.
        foreach ($this->notAllowedFunctions() as $functionName)
            if (preg_match("/\s?$functionName\s*\(/", $this->_code) > 0)
                throw new NotSafe(sprintf('Code contains call to function "%s" and is not considered safe.', $functionName));
        return true;
    }

    /**
     * Checks the syntax using the 'php -l' command.
     * @throws Syntax if there is a syntax error in the code.
     */
    protected function checkSyntax()
    {
        $pipes = array();
        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
        $process = proc_open('php -l', $descriptors, $pipes);
        if (!is_resource($process))
            throw new Fatal('Failed to check syntax with error: Failed to locate php.');

        fwrite($pipes[0], '<?php ' . $this->_code);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $return = proc_close($process);

        if ($return !== 0)
            throw new Syntax(sprintf('Failed to run code with error: %s', $error));

        return $return;
    }

    /**
     * @return array list of prohibited class names.
     */
    protected function notAllowedClassNames()
    {
        $classNames = array_diff(get_declared_classes(), $this->_allowedClassNames);
        foreach ($classNames as $index => $className) {
            $classNames[$index] = str_replace("\\", "\\\\",$className);
        }
        return $classNames;
    }

    /**
     * @return array list of prohibited keywords.
     */
    protected function notAllowedKeywords()
    {
        return array_diff($this->_keywords, $this->_allowedKeywords);
    }

    /**
     * @return array list of prohibited function names.
     */
    protected function notAllowedFunctions()
    {
        $definedFunctions = get_defined_functions();
        $allFunctions = array_merge($definedFunctions['internal'], $definedFunctions['user']);
        return array_diff($allFunctions, $this->_allowedFunctions);
    }

    /**
     * @param array $array list of class names.
     */
    public function setAllowedClassNames(array $array)
    {
        $this->_allowedClassNames = $array;
    }

    /**
     * @param array $array list of keywords.
     */
    public function setAllowedKeywords(array $array)
    {
        $this->_allowedKeywords = $array;
    }

    /**
     * @param array $array list of function names.
     */
    public function setAllowedFunctions(array $array)
    {
        $this->_allowedFunctions = $array;
    }
}
