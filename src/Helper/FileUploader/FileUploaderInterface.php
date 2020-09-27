<?php

namespace Obblm\Core\Helper\FileUploader;

interface FileUploaderInterface
{
    public function uploadIfExists($object, $file, $field);
}
