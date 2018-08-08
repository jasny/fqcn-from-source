<?php

namespace Jasny\FQCN\Tests;

use Jasny\FQCN\FQCNReader;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * @covers \Jasny\FQCN\FQCNReader
 */
class FQCNReaderTest extends TestCase
{
    /**
     * @var FQCNReader
     */
    protected $fqcnReader;

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    public function setUp()
    {
        $this->fqcnReader = new FQCNReader();

        $this->root = vfsStream::setup();
    }

    public function testGetClass()
    {
        $source = <<<PHP
<?php

declare(strict_types=1);

class Fab
{
}
PHP;
        $this->root->addChild(vfsStream::newFile('test.php')->setContent($source));

        $this->assertEquals('Fab', $this->fqcnReader->getClass('vfs://root/test.php'));
    }

    public function testGetClassNs()
    {
        $source = <<<PHP
<?php

declare(strict_types=1);

namespace Foo;

use stdClass;
use Colors\Blue;

class Bar
{
   function hello(Blue \$blue)
   {
   }
}
PHP;
        $this->root->addChild(vfsStream::newFile('test.php')->setContent($source));

        $this->assertEquals('Foo\\Bar', $this->fqcnReader->getClass('vfs://root/test.php'));
    }

    public function testGetClassLongComment()
    {
        $docblock = "\n" . str_repeat(" * I am another line of comment\n", 100);

        $source = <<<PHP
<?php

declare(strict_types=1);

namespace Foo;

use stdClass;
use Colors\Blue;

/**$docblock*/
class Bar
{
   function hello(Blue \$blue)
   {
   }
}
PHP;
        $this->root->addChild(vfsStream::newFile('test.php')->setContent($source));

        $this->assertEquals('Foo\\Bar', $this->fqcnReader->getClass('vfs://root/test.php'));
    }

    public function testGetClassScript()
    {
        $source = <<<PHP
<?php

echo "Hello world";
PHP;
        $this->root->addChild(vfsStream::newFile('test.php')->setContent($source));

        $this->assertNull($this->fqcnReader->getClass('vfs://root/test.php'));
    }

    public function testGetClassScriptNS()
    {
        $source = <<<PHP
<?php

namespace Foo;

echo "Hello world";
PHP;
        $this->root->addChild(vfsStream::newFile('test.php')->setContent($source));

        $this->assertNull($this->fqcnReader->getClass('vfs://root/test.php'));
    }

    public function testGetClassTextFile()
    {
        $this->root->addChild(vfsStream::newFile('test.php')->setContent("Hello world"));

        $this->assertNull($this->fqcnReader->getClass('vfs://root/test.php'));
    }
}
