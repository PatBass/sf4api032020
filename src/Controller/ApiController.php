<?php

namespace App\Controller;

use App\Entity\Phone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class ApiController extends AbstractController
{

    /**
     * @Route("/api", name="api")
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function index(SerializerInterface $serializer)
    {
        $phone = new Phone();

        $phone->setName('iPhone');
        $phone->setPrice(1000);
        $phone->setDescription('Je mets mon iPhone en vente');

        $data = $serializer->serialize($phone, 'json');

        $response = new Response($data, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
