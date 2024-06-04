TaxCode: "Germany" => "DE1234567890", "Italy" => "IT1234567890", "France" => "FRY1234567890", "Greece"=> "GR1234567890"<br>
Products Id: "Iphone" => 1, "Headphones" => 2, "Case" => 3<br>
Coupons: "P10" => 10, "P100" => 100


Api Request:
# /calculate-price<br>
{<br> 
  "product":  1,<br>
  "taxNumber":"DE1234567890",<br>
  "couponCode":"P10"<br>
}<br>
Response<br>
{<br>
    "id": 1,<br>
    "name": "Iphone",<br>
    "price": "107.1"<br>
}<br>

# /purchase<br>
{<br> 
  "product":  1,<br>
  "taxNumber":"DE1234567890",<br>
  "couponCode":"P10",<br>
  'paymentProcessor' => 'paypal'<br>
}<br>

Response<br>
{<br>
    "id": 1,<br>
    "name": "Iphone",<br>
    "price": "107.1"<br>
}

