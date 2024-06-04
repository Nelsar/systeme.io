TaxCode: "Germany" => "DE1234567890", "Italy" => "IT1234567890", "France" => "FRY1234567890", "Greece"=> "GR1234567890"
Products Id: "Iphone" => 1, "Headphones" => 2, "Case" => 3
Coupons: "P10" => 10, "P100" => 100


Api Request:
# /calculate-price
{ 
  "product":  1,
  "taxNumber":"DE1234567890",
  "couponCode":"P10"
}
Response
{
    "id": 1,
    "name": "Iphone",
    "price": "107.1"
}

# /purchase
{ 
  "product":  1,
  "taxNumber":"DE1234567890",
  "couponCode":"P10",
  'paymentProcessor' => 'paypal'
}

Response
{
    "id": 1,
    "name": "Iphone",
    "price": "107.1"
}

