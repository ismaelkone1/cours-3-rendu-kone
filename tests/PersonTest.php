<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Person;
use App\Entity\Product;
use App\Entity\Wallet;

class PersonTest extends TestCase
{
    /**
     * @return array<string, array<string, string>>
     */
    public function personProvider(): array
    {
        return [
            'usd_wallet' => [
                'name' => 'John',
                'currency' => 'USD'
            ],
            'eur_wallet' => [
                'name' => 'Jane',
                'currency' => 'EUR'
            ]
        ];
    }

    /**
     * @dataProvider personProvider
     */
    public function testPersonCreation(string $name, string $currency): void
    {
        $person = new Person($name, $currency);
        
        $this->assertEquals($name, $person->getName());
        $this->assertEquals($currency, $person->getWallet()->getCurrency());
        $this->assertEquals(0.0, $person->getWallet()->getBalance());
    }

    public function testSetAndGetName(): void
    {
        $person = new Person('Initial', 'USD');
        $person->setName('New Name');
        $this->assertEquals('New Name', $person->getName());
    }

    public function testSetAndGetWallet(): void
    {
        $person = new Person('John', 'USD');
        $newWallet = new Wallet('EUR');
        $person->setWallet($newWallet);
        
        $this->assertSame($newWallet, $person->getWallet());
    }

    public function testHasFund(): void
    {
        $person = new Person('John', 'USD');
        $this->assertFalse($person->hasFund());

        $person->getWallet()->addFund(100.0);
        $this->assertTrue($person->hasFund());
    }

    public function testTransfertFund(): void
    {
        $sender = new Person('John', 'USD');
        $receiver = new Person('Jane', 'USD');
        
        $sender->getWallet()->addFund(100.0);
        $sender->transfertFund(50.0, $receiver);

        $this->assertEquals(50.0, $sender->getWallet()->getBalance());
        $this->assertEquals(50.0, $receiver->getWallet()->getBalance());
    }

    public function testTransfertFundDifferentCurrencies(): void
    {
        $sender = new Person('John', 'USD');
        $receiver = new Person('Jane', 'EUR');
        
        $sender->getWallet()->addFund(100.0);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t give money with different currencies');
        $sender->transfertFund(50.0, $receiver);
    }

    public function testDivideWallet(): void
    {
        $sender = new Person('John', 'USD');
        $receiver1 = new Person('Jane', 'USD');
        $receiver2 = new Person('Bob', 'USD');
        $receiver3 = new Person('Alice', 'EUR'); // Should be filtered out

        $sender->getWallet()->addFund(100.0);
        $sender->divideWallet([$receiver1, $receiver2, $receiver3]);

        $this->assertEquals(0.0, $sender->getWallet()->getBalance());
        $this->assertEquals(50.0, $receiver1->getWallet()->getBalance());
        $this->assertEquals(50.0, $receiver2->getWallet()->getBalance());
        $this->assertEquals(0.0, $receiver3->getWallet()->getBalance());
    }

    public function testBuyProduct(): void
    {
        $person = new Person('John', 'USD');
        $product = new Product('Test', ['USD' => 50.0], 'food');
        
        $person->getWallet()->addFund(100.0);
        $person->buyProduct($product);
        
        $this->assertEquals(50.0, $person->getWallet()->getBalance());
    }

    public function testBuyProductInvalidCurrency(): void
    {
        $person = new Person('John', 'USD');
        $product = new Product('Test', ['EUR' => 50.0], 'food');
        
        $person->getWallet()->addFund(100.0);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t buy product with this wallet currency');
        $person->buyProduct($product);
    }

    public function testBuyProductInsufficientFunds(): void
    {
        $person = new Person('John', 'USD');
        $product = new Product('Test', ['USD' => 150.0], 'food');
        
        $person->getWallet()->addFund(100.0);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        $person->buyProduct($product);
    }
}