<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/api/login', name: 'app_user')]
    public function logIn(Request $request): JsonResponse
    {
        return new JsonResponse(201);
    }
    #[Route('/api/users/signin', name: 'user_signin', methods:["POST"])] 
    public function signIn(
        Request $request, ValidatorInterface $validator, EntityManagerInterface $em, 
        SerializerInterface $serializer,UserPasswordHasherInterface $userPasswordHasher, 
        UserRepository $userRepository ):JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class,'json');
        $username = $user->getUsername();
        $userList = $userRepository->findAll();
        $errors = $validator->validate($user);
        if (count($errors)> 0) 
        {
            return new JsonResponse (serialize($errors, 'json'), 400, [], true);
        }
        for ($i=0; $i <count($userList) ; $i++) { 
            if ( $username == $userList[$i]->getUsername()) 
            {
                return new JsonResponse(["error" => "Le nom d'utilisateur $username  n'est plus disponible"], 401, []);
            }
        }
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
        $em->persist($user);
        $em->flush();
        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, 201,[], true);
    }
    #[Route('/api/users/', name:'fetch_users', methods:['GET'])]
    public function getUsers(SerializerInterface $serializer, UserRepository $userRepository):JsonResponse
    {
        $userList = $userRepository->findAll();
        $jsonUserList = $serializer->serialize($userList, 'json', ['groups' => 'getUsers']);
        return new JsonResponse($jsonUserList, 200, [], true);
    }
}
