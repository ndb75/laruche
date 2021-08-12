<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\Stock;

class StockTest extends TestCase
{
    private $stock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stock = new Stock;
    }

    public function testGetFilename(): void
    {
        $value = "filename.csv";
        $response = $this->stock->setFilename($value);

        self::assertInstanceOf(Stock::class, $response);
        self:self::assertEquals($value, $this->stock->getFilename());
    }
}