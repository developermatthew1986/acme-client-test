<?php

namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(string $productCode)
    {
        parent::__construct("Product with code '{$productCode}' not found in catalogue.");
    }
}
