<?php

namespace Obblm\Core\Tests\Domain\Helper;

use Obblm\Core\Domain\Helper\FileTeamUploader;
use Obblm\Core\Tests\Fixtures\TeamBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileTeamUploaderTest extends KernelTestCase
{
    /** @var FileTeamUploader */
    private $uploader;

    public function setUp(): void
    {
        static::bootKernel();
        $this->uploader = static::getContainer()
            ->get(FileTeamUploader::class);
    }

    public function testUploader()
    {
        self::assertInstanceOf(FileTeamUploader::class, $this->uploader);

        var_dump($this->uploader);

        $team = TeamBuilder::for()->build();
        var_dump($team);
        $this->uploader->uploadIfExists($team, 'logo');
    }
}
