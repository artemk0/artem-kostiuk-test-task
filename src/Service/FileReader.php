<?php

namespace App\Service;

class FileReader
{
    public function __construct(private string $filename) {}

    public function readFile(): bool|array
    {
        return file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
