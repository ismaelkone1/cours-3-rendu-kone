<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Product;

class ProductTest extends TestCase
{
    /**
     * @return array<string, array<string,mixed>>
     */
    public function validProductProvider(): array
    {
        return [
            'food_product' => [
                'name' => 'Apple',
                'prices' => ['USD' => 1.99, 'EUR' => 1.79],
                'type' => 'food',
                'expected_tva' => 0.1
            ],
            'tech_product' => [
                'name' => 'Laptop',
                'prices' => ['USD' => 999.99, 'EUR' => 899.99],
                'type' => 'tech',
                'expected_tva' => 0.2
            ]
        ];
    }

    /**
     * @dataProvider validProductProvider
     */
    public function testProductCreation(string $name, array $prices, string $type, float $expected_tva): void
    {
        $product = new Product($name, $prices, $type);
        
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($prices, $product->getPrices());
        $this->assertEquals($type, $product->getType());
        $this->assertEquals($expected_tva, $product->getTVA());
    }

    public function testInvalidProductType(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid type');
        
        new Product('Test', ['USD' => 10.0], 'invalid_type');
    }

    public function testSetInvalidPrices(): void
    {
        $product = new Product('Test', ['USD' => 10.0], 'food');
        
        $product->setPrices(['JPY' => 1000.0]);
        $this->assertEmpty($product->getPrices());

        $product->setPrices(['USD' => -10.0]);
        $this->assertEmpty($product->getPrices());
    }

    public function testListCurrencies(): void
    {
        $prices = ['USD' => 10.0, 'EUR' => 8.0];
        $product = new Product('Test', $prices, 'food');
        
        $currencies = $product->listCurrencies();
        $this->assertEquals(['USD', 'EUR'], $currencies);
    }

    public function testGetPrice(): void
    {
        $prices = ['USD' => 10.0, 'EUR' => 8.0];
        $product = new Product('Test', $prices, 'food');
        
        $this->assertEquals(10.0, $product->getPrice('USD'));
        $this->assertEquals(8.0, $product->getPrice('EUR'));
    }

    public function testGetPriceInvalidCurrency(): void
    {
        $product = new Product('Test', ['USD' => 10.0], 'food');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        $product->getPrice('JPY');
    }

    public function testGetPriceCurrencyNotAvailable(): void
    {
        $product = new Product('Test', ['USD' => 10.0], 'food');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Currency not available for this product');
        $product->getPrice('EUR');
    }

    public function testSetAndGetName(): void
    {
        $product = new Product('Initial', ['USD' => 10.0], 'food');
        
        $product->setName('New Name');
        $this->assertEquals('New Name', $product->getName());
    }
}