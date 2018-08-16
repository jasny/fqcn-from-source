<?php

declare(strict_types=1);

namespace Jasny\FQCN;

/**
 * extracting a class name from a PHP source file
 */
class FQCNReader
{
    /**
     * Get the fully qualified class name from file
     *
     * @param string $file
     * @return string|null
     */
    public function getClass(string $file): ?string
    {
        $fp = fopen($file, 'r');
        $buffer = '';

        while (!isset($class) && !feof($fp)) {
            $buffer .= fread($fp, 512);

            if (strpos($buffer, '{') === false) {
                continue;
            }

            $tokens = token_get_all($buffer);
            $class = $this->getFqcnFromTokens($tokens);
        }

        return $class ?? null;
    }

    /**
     * Walk through the tokens to find the fully qualified classname
     *
     * @param array $tokens
     * @return string|null
     */
    protected function getFqcnFromTokens(array $tokens): ?string
    {
        $class = null;
        $namespace = '';

        for ($i = 0, $n = count($tokens); $i < $n; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                $namespace = $this->getNamespaceFromTokens($tokens, $i);
            }

            if ($tokens[$i][0] === T_CLASS) {
                $class = $namespace . $this->getClassFromTokens($tokens, $i);
                break;
            }
        }

        return $class;
    }

    /**
     * Get the namespace after finding a T_NAMESPACE token.
     *
     * @param array $tokens
     * @param int   $pos
     * @return string
     */
    protected function getNamespaceFromTokens(array $tokens, int $pos): string
    {
        $namespace = '';

        for ($j = $pos + 1, $m = count($tokens); $j < $m; $j++) {
            if ($tokens[$j][0] === T_STRING) {
                $namespace .= $tokens[$j][1] . '\\';
            } elseif ($tokens[$j] === '{' || $tokens[$j] === ';') {
                break;
            }
        }

        return $namespace;
    }

    /**
     * Get the class name after finding a T_CLASS token.
     *
     * @param array $tokens
     * @param int   $pos
     * @return string
     */
    protected function getClassFromTokens(array $tokens, int $pos): string
    {
        $class = '';

        for ($j = $pos + 1, $m = count($tokens); $j < $m; $j++) {
            if ($tokens[$j] === '{') {
                $class = $tokens[$pos + 2][1];
                break;
            }
        }

        return $class;
    }
}
