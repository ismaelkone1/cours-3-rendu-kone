<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Wallet;

class WalletTest extends TestCase
{
    public function iniWallet()
    {
        $wallet = new Wallet('USD');
        $this->assertEquals(0, $wallet->getBalance());
        $this->assertEquals('USD', $wallet->getCurrency());
        return $wallet;
    }
    public function testAddFunds()
    {
        $wallet = $this->iniWallet();
        $wallet->addFund(100);
        $this->assertEquals(100, $wallet->getBalance());
    }
    public function testAddFundsInvalid()
    {
        $wallet = $this->iniWallet();
        $this->expectException(\Exception::class);
        $wallet->addFund(-100);
    }
    public function testRemoveFunds()
    {
        $wallet = $this->iniWallet();
        $wallet->addFund(100);
        $wallet->removeFund(50);
        $this->assertEquals(50, $wallet->getBalance());
    }
    public function testRemoveFundsInvalid()
    {
        $wallet = $this->iniWallet();
        $this->expectException(\Exception::class);
        $wallet->removeFund(50);
    }
    public function testSetCurrency()
    {
        $wallet = $this->iniWallet();
        $wallet->setCurrency('EUR');
        $this->assertEquals('EUR', $wallet->getCurrency());
    }
    public function testSetCurrencyInvalid()
    {
        $wallet = $this->iniWallet();
        $this->expectException(\Exception::class);
        $wallet->setCurrency('JPY');
    }
    public function testSetBalance()
    {
        $wallet = $this->iniWallet();
        $wallet->setBalance(100);
        $this->assertEquals(100, $wallet->getBalance());
    }
    public function testSetBalanceInvalid()
    {
        $wallet = $this->iniWallet();
        $this->expectException(\Exception::class);
        $wallet->setBalance(-100);
    }
    
    
}
