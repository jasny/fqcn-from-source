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

    /**
     * Test case reproduces a bug that may happen due to buffering issues
     */
    public function testGetClassNsWarningIssue()
    {
        $source = <<<PHP
<?php
/*
 * ggggggggggggggggggggggggggggggggggggggggggggggggggggggg
 * ggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg
 * gggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg
 * gggggggggggggggggggggggggggg
 * ggggggggggggggggggg
 */

namespace Slenderman\Core\Services;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Slenderman\Services\Contracts\ServiceProviderInterface;
use const APP_ROOT;
use function DI\add;
use function DI\get;

/**
 * Contains settings that are for the whole app that do not change based on the mode
 *
 * @author Aesonus <corylcomposinger at gmail.com>
 */
class AppSettingsService implements ServiceProviderInterface
{
    /**
     * Log handling settings
     */
    const LOG_HANDLER_SETTINGS = 'log_handler_settings';

    /**
     * Log handler settings for errors in html format
     */
    const LOG_HANDLER_SETTINGS_HTML = 'html_errors';

    /**
     * Log handler settings for errors in text line format
     */
    const LOG_HANDLER_SETTINGS_TEXT = 'text_errors';

    /**
     * The stream definition for a log handler
     */
    const LOG_HANDLER_STREAM = 'stream';
}
?>
PHP;
        $this->root->addChild(vfsStream::newFile('test.php')->setContent($source));

        $this->assertEquals('Slenderman\\Core\\Services\\AppSettingsService', $this->fqcnReader->getClass('vfs://root/test.php'));
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
