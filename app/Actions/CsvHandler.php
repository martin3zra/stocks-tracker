<?php

namespace App\Actions;

class CsvHandler
{

    public function __construct(private string $filename)
    {

    }

    public function map(): array
    {
        //Read and map CSV from the downloaed file
        return array_map('str_getcsv', file($this->filename));
    }

    public function deleteFile(): void
    {
        //This removes downloaded the file
        if(file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
}
