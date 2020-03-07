<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PhoneController
 * @package App\Controller
 *
 * @Route("/api")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("/phones/{id<\d+>}", name="show_phone", methods={"GET"})
     */
    public function show(Phone $phone, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($phone, 'json', ['groups' => ['show', 'list']]);
        return new Response($data, Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/phones/{page<\d+>?1}", name="phone_list", methods={"GET"})
     *
     * @param Request $request
     * @param PhoneRepository $phoneRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function index(Request $request, PhoneRepository $phoneRepository, SerializerInterface $serializer)
    {
        $page = $request->query->get('page');
        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        $limit = 5;
        $allPhones = $phoneRepository->findAllPhones($page, $limit/*getenv('LIMIT')*/);

        //dump($allPhones);die;

        $jsonData = $serializer->serialize($allPhones, 'json', ['groups' => ['list']]);

        return new Response($jsonData, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/phones", name="add_phone", methods={"POST"})
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $phone =  $serializer->deserialize($request->getContent(), Phone::class, 'json');
        $errors = $validator->validate($phone);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, Response::HTTP_INTERNAL_SERVER_ERROR, ['Content-Type' => 'application/json']);
        }
        $entityManager->persist($phone);
        $entityManager->flush();
        $data = [
            'status' => Response::HTTP_CREATED,
            'message' => 'Un nouvel objet phone a bien été ajouté dans la base de données'
        ];
        return new JsonResponse($data, Response::HTTP_CREATED);
    }



}
