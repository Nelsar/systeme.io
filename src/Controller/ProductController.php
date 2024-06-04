<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductsRepository;
use App\Repository\TaxesRepository;
use App\Repository\CouponsRepository;
use App\Repository\PaymentProcessorRepository;
use App\Entity\Calculate;
use App\Entity\ErrorResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductController extends AbstractController
{
    private ProductsRepository $productRepository;
    private TaxesRepository $taxesRepository;
    private CouponsRepository $couponsRepository;
    private PaymentProcessorRepository $paymentProcessorRepository;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private Calculate $calculate;
    private ErrorResponse $error;

    public function __construct(ProductsRepository $productRepository,
                                TaxesRepository $taxesRepository,
                                CouponsRepository $couponsRepository,
                                PaymentProcessorRepository $paymentProcessorRepository,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator,
                                Calculate $calculate,
                                ErrorResponse $error)
    {
        $this->productRepository = $productRepository;
        $this->taxesRepository = $taxesRepository;
        $this->couponsRepository = $couponsRepository;
        $this->paymentProcessorRepository = $paymentProcessorRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->calculate = $calculate;
        $this->error = $error;
    }

    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/')]
    public function root(): Response
    {
        $products = $this->productRepository->findAll(); 
        return $this->json($products);
    }

    #[Route('/calculate-price' , methods: ['POST'])]
    public function calculate(Request $request): Response
    {
        
        try {

            $body = $this->serializer->deserialize(
            $request->getContent(), Calculate::class, 'json');
            

            $errors = $this->validator->validate($body);
            if(count($errors) > 0) {
                throw new UnexpectedValueException($errors);
            }

            $product = $this->productRepository->find($body->product);
            $taxes = $this->taxesRepository->findOneBy(array('number' => $body->taxNumber));
            $coupons = $this->couponsRepository->findOneBy(array('code' => $body->couponCode));
            

            if(!$product) {
                throw $this->createNotFoundException('No product found for productId: '.$body->product);
            }

            if(!$taxes){
                throw $this->createNotFoundException('No taxes found for taxes number: '.$body->taxNumber);
            }

            if(!$coupons) {
                throw $this->createNotFoundException('No coupon found for coupon code: '.$body->couponCode);
            }

            $product->setPrice($this->priceConvert($product, $taxes, $coupons));
         
            
            return $this->json($product);

        } catch (\NotFoundHttpException  $ex) {
            return $this->json($this->errorReponse($ex, Response::HTTP_NOT_FOUND));
        } catch(\UnexpectedValueException $ex) {
            return $this->json($this->errorReponse($ex, Response::HTTP_BAD_REQUEST));
        }
    }

    #[Route('/purchase' , methods: ['POST'])]
    public function purchase(Request $request): Response
    {
        try {

            $body = $this->serializer->deserialize(
            $request->getContent(), Calculate::class, 'json');

            $errors = $this->validator->validate($body);
            
            if(count($errors) > 0) {
                throw new UnexpectedValueException($errors);
            }

            $product = $this->productRepository->find($body->product);
            $taxes = $this->taxesRepository->findOneBy(array('number' => $body->taxNumber));
            $coupons = $this->couponsRepository->findOneBy(array('code' => $body->couponCode));
            $payment = $this->paymentProcessorRepository->findOneBy(array('name' => $body->paymentProcessor));
            

            if(!$product) {
                throw $this->createNotFoundException('No product found for productId: '.$body->product);
            }

            if(!$taxes){
                throw $this->createNotFoundException('No taxes found for taxes number: '.$body->taxNumber);
            }

            if(!$coupons) {
                throw $this->createNotFoundException('No coupon found for coupon code: '.$body->couponCode);
            }

            if(!$payment) {
                throw $this->createNotFoundException('No payment processor found for payment processor code: '.$body->paymentProcessor);
            }

            $product->setPrice($this->priceConvert($product, $taxes, $coupons));
         
            
            return $this->json($product);

        } catch (\NotFoundHttpException  $ex) {
            return $this->json($this->errorReponse($ex, Response::HTTP_NOT_FOUND));
        } catch(\UnexpectedValueException $ex) {
            return $this->json($this->errorReponse($ex, Response::HTTP_BAD_REQUEST));
        }
    }

    public function errorReponse($exeption, $code = 0)
    {
        $response = new Response();
        
        $this->error->code = $exeption->GetCode();
        $this->error->errorMessage = $exeption->getMessage();
        
        $json = $this->serializer->serialize($this->error, 'json');
        
        $response->headers->set('Content-Type', 'application/json');
        
        $response->setContent($json);
        if($code > 0) {
            $response->setStatusCode($code);
        }
        $response->send();
        return $json;
    }

    public function priceConvert($product, $taxes, $coupon)
    {
        $percent = $product->getPrice() + ($product->getPrice() * ($taxes->getPercent() / 100));
        $result = $percent - ($percent * ($coupon->getPercent() / 100));
        return $result;
    }

}
