<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


class Calculate
{

    #[Assert\NotBlank]
    #[Assert\Integer]
    public int $product;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    public string $taxNumber;

    #[Assert\Length(min: 3)]
    public string $couponCode;

    #[Assert\Length(min: 3)]
    public string $paymentProcessor;
}
