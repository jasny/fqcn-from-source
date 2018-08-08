<?php

namespace Jasny\FQCN\Tests;

use Jasny\FQCN\FQCNIterator;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use ArrayIterator;

/**
 * @covers \Jasny\FQCN\FQCNIterator
 */
class FQCNIteratorTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    /**
     * @var array
     */
    protected $files;

    public function setUp()
    {
        $scripts = [
            'uno.php' => "<?php class Uno { }",
            'dos.php' => "<?php class Dos { }",
            'tres.php' => "<?php class Tres { }",
            "script.php" => "<?php echo 'Hello world';",
            'foo-bar.php' => "<?php namespace Foo; class Bar {}"
        ];

        $this->root = vfsStream::setup('root', null, $scripts);
        $this->files = array_map(function($filename) {
            return 'vfs://root/' . $filename;
        }, array_keys($scripts));
    }

    public function testIterate()
    {
        $result = [];
        $iterator = new FQCNIterator(new ArrayIterator($this->files));

        foreach ($iterator as $file => $class) {
            $result[$file] = $class;
        }

        $expected = [
            'vfs://root/uno.php' => 'Uno',
            'vfs://root/dos.php' => 'Dos',
            'vfs://root/tres.php' => 'Tres',
            'vfs://root/foo-bar.php' => 'Foo\\Bar'
        ];

        $this->assertSame($expected, $result);
    }

    /**
     * @depends testIterate
     */
    public function testIterateRewind()
    {
        $result = [];
        $iterator = new FQCNIterator(new ArrayIterator($this->files));

        foreach ($iterator as $file => $class) {
            // Ignore
        }

        foreach ($iterator as $file => $class) {
            $result[$file] = $class;
        }

        $expected = [
            'vfs://root/uno.php' => 'Uno',
            'vfs://root/dos.php' => 'Dos',
            'vfs://root/tres.php' => 'Tres',
            'vfs://root/foo-bar.php' => 'Foo\\Bar'
        ];

        $this->assertSame($expected, $result);
    }

    public function testGetInnerIterator()
    {
        $inner = new ArrayIterator([]);
        $iterator = new FQCNIterator($inner);

        $this->assertSame($inner, $iterator->getInnerIterator());
    }
}
