<?php

namespace Obblm\Core\Tests\Helper;

use Obblm\Core\Exception\NotFoundEntrypointException;
use Obblm\Core\Helper\AssetPackager;
use PHPUnit\Framework\TestCase;

class AssetPackagerTest extends TestCase
{
    protected $packager;

    protected function setUp(): void
    {
        $this->packager = new AssetPackager();
    }

    public function testLoad()
    {
        $this->packager->addDirectory(__DIR__ . '/assert-test/success');

        $fullEntrypoint = $this->packager->getEntryPoint('assert-test-entry');
        $noCssEntrypoint = $this->packager->getEntryPoint('assert-test-no-css');
        $noJsEntrypoint = $this->packager->getEntryPoint('assert-test-no-js');

        // assert-test-entry has css and js
        $expacted = [
            "js" => [
                "/js/expected.1234.js",
                "/js/expected.5678.js"
            ],
            "css"=> [
                "/css/expected.1234.css"
            ]
        ];
        $this->assertEquals($expacted, $fullEntrypoint);
        $this->assertEquals($expacted['css'], $this->packager->getCssEntry('assert-test-entry'));
        $this->assertEquals($expacted['js'], $this->packager->getJsEntry('assert-test-entry'));
        $this->assertArrayHasKey('css', $fullEntrypoint);
        $this->assertArrayHasKey('js', $fullEntrypoint);

        // assert-test-no-css has css and NO js
        $expacted = [
            "css"=> [
                "/css/expected.4567.css"
            ]
        ];
        $this->assertEquals($expacted, $noJsEntrypoint);
        $this->assertEquals($expacted['css'], $this->packager->getCssEntry('assert-test-no-js'));
        $this->assertArrayHasKey('css', $noJsEntrypoint);
        $this->assertArrayNotHasKey('js', $noJsEntrypoint);
        // "/css/expected.4567.css"

        // assert-test-no-css has js and NO css
        $expacted = [
            "js" => [
                "/js/expected.91011.js",
            ],
        ];
        $this->assertEquals($expacted, $noCssEntrypoint);
        $this->assertEquals($expacted['js'], $this->packager->getJsEntry('assert-test-no-css'));
        $this->assertArrayHasKey('js', $noCssEntrypoint);
        $this->assertArrayNotHasKey('css', $noCssEntrypoint);

        // "/js/expected.91011.js"
    }

    public function testExceptionOnManifestLoad()
    {
        //Loading manifest failed
        try {
            $this->packager->addDirectory(__DIR__ . '/assert-test/fail-load-manifest');
        } catch (\Exception $e) {
            if ($e) {
                $this->assertInstanceOf(\RuntimeException::class, $e, "Loading manifest failed");
            } else {
                $this->fail('exception not expected : ' . get_class($e));
            }
        }
    }

    public function testExceptionOnEntrypointsLoad()
    {
        //Loading entrypoint failed
        try {
            $this->packager->addDirectory(__DIR__ . '/assert-test/fail-load-entrypoint');
        } catch (\Exception $e) {
            if ($e) {
                $this->assertInstanceOf(\RuntimeException::class, $e, "Loading entrypoint failed");
            } else {
                $this->fail('exception not expected : ' . get_class($e));
            }
        }
    }

    public function testExceptionOnFalseEntrypoint()
    {
        $this->packager->addDirectory(__DIR__ . '/assert-test/success');
        try {
            $this->packager->getEntryPoint('test-false');
        } catch (\Exception $e) {
            if ($e) {
                $this->assertInstanceOf(NotFoundEntrypointException::class, $e, "False key exception test");
            } else {
                $this->fail('exception not expected : ' . get_class($e));
            }
        }
    }
}
