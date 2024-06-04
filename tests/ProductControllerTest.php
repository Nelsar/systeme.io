<?php

namespace App\Tests;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ProductControllerTest extends ApiTestCase
{


    /**
     * @runInSeparateProcess
     */

    public function testCalculatePriceIsSuccesFull(): void
    {
        
        if (!headers_sent()) {
            header("Content-Type: application/json; charset=utf-8");
        }

        $response = static::createClient()->request('POST', 
                                                    '/calculate-price', 
                                                    ['json' => [
                                                        'product' => 1, 
                                                        'taxNumber' => 'DE12345678900', 
                                                        'couponCode' => 'P10'
                                                    ]]);

        

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertJsonContains([
            'id' => 1,
            'name' => "Iphone",
            'price' => "107.1"
        ]);
    }

    public function testCalculatePriceIsNotFound(): void
    {

        $response = static::createClient()->request('POST', 
                                                    '/calculate-price',
                                                    ['json' => [
                                                        'product' => 1, 
                                                        'taxNumber' => 'DE1234567890', 
                                                        'couponCode' => 'P10'
                                                    ]]);

        

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testPurchaseIsSuccesFull(): void
    {
        
        if (!headers_sent()) {
            header("Content-Type: application/json; charset=utf-8");
        }

        $response = static::createClient()->request('POST', 
                                                    '/calculate-price', 
                                                    ['json' => [
                                                        'product' => 1, 
                                                        'taxNumber' => 'DE12345678900', 
                                                        'couponCode' => 'P10',
                                                        'paymentProcessor' => 'paypal'
                                                    ]]);

        

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertJsonContains([
            'id' => 1,
            'name' => "Iphone",
            'price' => "107.1"
        ]);
    }

    public function testPurchasIsNotFound(): void
    {

        $response = static::createClient()->request('POST', 
                                                    '/calculate-price',
                                                    ['json' => [
                                                        'product' => 1, 
                                                        'taxNumber' => 'DE1234567890', 
                                                        'couponCode' => 'P10',
                                                        'paymentProcessor' => 'gpay'
                                                    ]]);

        

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
