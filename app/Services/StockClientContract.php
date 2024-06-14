<?php

namespace App\Services;

interface StockClientContract
{
    public function getStockInformation(string $code): string;

    public function getFileName(string $code): string;
}
