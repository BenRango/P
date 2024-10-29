<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Notifications;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\NotificationsRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\Attribute\Route;

class NotificationController extends AbstractController
{
    #[Route('/api/notifications', name: 'fetch', methods:['GET'])]
    public function fetchNotifications(NotificationsRepository $NotifReposittory, SerializerInterface $serializer): JsonResponse
    {
        $notificationsList = $NotifReposittory->findAll();
        if(count($notificationsList)>0)
        {
            $jsonNotificationsList =  $serializer->serialize( $notificationsList , 'json');
            return new JsonResponse($jsonNotificationsList, 200, [], true);
        }
        $message = ["Response"=>"No data found"];
        return new JsonResponse($message, 404,[]);
    }

    #[Route('/api/notifications', name: 'create', methods:['POST'])]
    public function createNotifications(EntityManagerInterface $em, SerializerInterface $serializer, Request $request, ValidatorInterface $validator) : JsonResponse
    {
        $notification = $serializer->deserialize($request->getContent(), Notifications::class, 'json');
        $errors  = $validator->validate($notification);
        if(count($errors)  >0)
        {
            $errorsString = (string) $errors;
            $jsonErrorString = $serializer->serialize($errors, 'json');
            return new JsonResponse($jsonErrorString , 400 ,[],true);
        }
        $em->persist($notification);
        $em->flush();
        $jsonNotification = $serializer->serialize($notification, 'json');
        return new JsonResponse($jsonNotification, 201, [], true);

    }

    #[Route('/api/notifications/{id}', name: "delete-notification", methods:['DELETE'])]
    function deleteNotification() :JsonResponse
    {

    }

}
