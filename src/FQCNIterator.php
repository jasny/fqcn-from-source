<?php

declare(strict_types=1);

namespace Jasny\FQCN;

use Iterator;
use OuterIterator;

/**
 * Iterate over source files to get fully qualified class names.
 */
class FQCNIterator implements OuterIterator
{
    /**
     * @var Iterator
     */
    protected $iterator;

    /**
     * @var FQCNReader
     */
    protected $reader;

    /**
     * @var string|null
     */
    protected $currentFile;

    /**
     * @var string|null
     */
    protected $current;

    /**
     * FQCNIterator constructor.
     *
     * @param Iterator   $iterator
     * @param FQCNReader $reader
     */
    public function __construct(Iterator $iterator, FQCNReader $reader = null)
    {
        $this->iterator = $iterator;
        $this->reader = $reader ?? new FQCNReader();

        $this->read();
    }

    /**
     * Read the files until the next FQCN is found
     *
     * @return void
     */
    protected function read(): void
    {
        do {
            $file = $this->iterator->current();
            $class = isset($file) ? $this->reader->getClass($file) : null;

            if ($class !== null) {
                break;
            }

            $this->iterator->next();
            $valid = $this->iterator->valid();
        } while ($valid);

        $this->currentFile = isset($class) ? $file : null;
        $this->current = $class;
    }


    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return string
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void
     */
    public function next()
    {
        $this->iterator->next();
        $this->read();
    }

    /**
     * Return the key of the current element.
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->currentFile;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return isset($this->currentFile);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
        $this->currentFile = null;
        $this->current = null;

        $this->iterator->rewind();

        $this->read();
    }

    /**
     * Returns the inner iterator for the current entry.
     *
     * @return Iterator
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }
}
