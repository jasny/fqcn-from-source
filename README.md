Jasny FQCN Reader
===

[![Build Status](https://travis-ci.org/jasny/fqcn-reader.svg?branch=master)](https://travis-ci.org/jasny/{{library}})
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasny/fqcn-reader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasny/{{library}}/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jasny/fqcn-reader/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasny/{{library}}/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1976d2de-1c9c-4c42-8bcd-420ff78e4e1c/mini.png)](https://insight.sensiolabs.com/projects/1976d2de-1c9c-4c42-8bcd-420ff78e4e1c)
[![BCH compliance](https://bettercodehub.com/edge/badge/jasny/fqcn-reader?branch=master)](https://bettercodehub.com/)
[![Packagist Stable Version](https://img.shields.io/packagist/v/jasny/fqcn-reader.svg)](https://packagist.org/packages/jasny/{{library}})
[![Packagist License](https://img.shields.io/packagist/l/jasny/fqcn-reader.svg)](https://packagist.org/packages/jasny/{{library}})

Library to extract fully qualified class name (FQCN) from a PHP source file.

_Caveat; only considers one class per source file._

Installation
---

    composer require jasny/fqcn-reader

Usage
---

### Single source file

The `FQCNReader` allows extracting a class name from a PHP source file.

```php
use Jasny\FQCN\FQCNReader;

$reader = new FQCNReader();

$class = $reader->getClass("path/to/source.php");
```

### Interator

The `FQCNIterator` is an [`OuterIterator`](http://php.net/manual/en/class.outeriterator.php), meaning it will iterate
over an iterator applying logic. The iterator expects to traverse over source files.

With the file names in an array, use [`ArrayIterator`](http://php.net/ArrayIterator).

```php
use Jasny\FQCN\FQCNIterator;

$sourceFiles = glob('path/to/directory/*.php');
$sourceIterator = new ArrayIteractor($sourceFiles);

$fqcnIterator = new FQCNIterator($sourceIterator);

foreach ($fqcnIteractor as $file => $class) {
   // do something with $class
}
```

Alternatively use SPL Iterators like [`DirectoryIterator`](http://php.net/DirectoryIterator),
[`RecursiveDirectoryIterator`](http://php.net/RecursiveDirectoryIterator) or
[`GlobIterator`](http://php.net/GlobIterator).

```php
use Jasny\FQCN\FQCNIterator;

$directoryIterator = new RecursiveDirectoryIterator('path/to/project/');
$recursiveIterator = new RecursiveIteratorIterator($directoryIterator);
$sourceIterator = new RegexIterator($recursiveIterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);

$fqcnIterator = new FQCNIterator($sourceIterator);

foreach ($fqcnIteractor as $file => $class) {
   // do something with $class
}
```

Files that do not define a class are skipped.
