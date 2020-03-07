<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SecurityController
 * @package App\Controller
 *
 * @Route("/api")
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/register", name="register", methods={"POST"})
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ) {
        $requestData = json_decode($request->getContent());
        if (isset($requestData['email']) && isset($requestData['password'])) {
            $user = new User();
            $user->setEmail($requestData['email']);
            $user->setPassword($passwordEncoder->encodePassword($user, $requestData['password']));
            $user->setRoles($user->getRoles());

            $errors = $validator->validate($user);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new JsonResponse(
                    $errors,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    [ 'Content-Type' => 'application/json']
                );
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_CREATED,
                'message' => 'The user has been successfully created!'
            ];

            return new JsonResponse($data, Response::HTTP_CREATED);
        }

        $data = [
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Please provide valid email and password'
        ];

        return new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login()
    {
        $user = $this->getUser();
        $this->json(
            [
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        );
    }
}
