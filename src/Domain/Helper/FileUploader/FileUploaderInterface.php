<?php

namespace Obblm\Core\Domain\Helper\FileUploader;

interface FileUploaderInterface
{
    public function uploadIfExists($object, $file, $field);
}
