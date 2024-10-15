<?php

namespace App\Controller;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Task;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/api/tasks', name: 'fetchTasks', methods:["GET"])]
    public function getTasks(TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
    {
        $taskList = $taskRepository->findAll();
        $jsonTaskList= $serializer->serialize($taskList, 'json'/*, ['groups' => 'getTasks']*/);
        return new JsonResponse($jsonTaskList, JsonResponse::HTTP_OK, [], true);
    }
    #[Route('/api/tasks/{id}', name: 'detailTask', methods:["GET"])]
    public function getTaskById(int $id, TaskRepository $taskRepository, SerializerInterface $serializer) : JsonResponse
    {
        $task = $taskRepository->find($id);
        if ($task) 
        {
            $jsonTask= $serializer->serialize($task, 'json'/*, ['groups' => 'getTasks']*/);
            return new JsonResponse($jsonTask, JsonResponse::HTTP_OK, [], true);
        }
        return new JsonResponse(['error' => "L'id n°$id n'a pas été trouvé"], Response::HTTP_NOT_FOUND);
    }
    #[Route('/api/tasks', name: 'createTask', methods:["POST"])]
    public function createTask(Request $request,SerializerInterface $serializer, EntityManagerInterface $em,
    UrlGeneratorInterface $urlGenerator,
    ValidatorInterface $validator): JsonResponse
    {
        $task = $serializer->deserialize($request->getContent(), Task::class, 'json');
        $errors= $validator->validate($task);

        if (count($errors) > 0) {

            $errorsString = (string) $errors;
    
            //return new JsonResponse($errorsString,400,[] ,true);

            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);

        }
        else
        {
            
            $jsonTask = $serializer->serialize($task, 'json');

            //$location = $urlGenerator->generate('detailTask', ['id' => $task->getId()], UrlGeneratorInterface::ABSOLUTE_URL);   
            $em->persist($task);
            $em->flush();
        }
   return new JsonResponse($jsonTask, Response::HTTP_CREATED, [], true);
}
    #[Route('api/tasks/{id}', name: 'updateTasks', methods: ["PUT"])]
    public function updateTasks(int $id, TaskRepository $taskRepository,
    Request $request,SerializerInterface $serializer, ValidatorInterface $validator,
    EntityManagerInterface $em): JsonResponse
    {
        $task = $taskRepository->findOneById($id);
        $errors= $validator->validate($serializer->deserialize($request->getContent(), Task::class, 'json'));

        if (count($errors) > 0) {

            $errorsString = (string) $errors;
    
            //return new JsonResponse($errorsString,400,[] ,true);

            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);

        }
        
        else {
            if ($task) 
            {
                $updatedTask= $request->toArray();
                $task->setLabel($updatedTask['label'])->setState($updatedTask['state'])
                ->setModified(true)->setCompletionDate($updatedTask['completionDate'])
                ->setLatestModificationDate($updatedTask['latestModificationDate']);
                $jsonTask = $serializer->serialize($task, 'json');
                $em->persist($task);
                $em->flush();
                return new JsonResponse($jsonTask ,Response::HTTP_OK , [] , true);
            }
            
        }

        
        
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
  
    }

    #[Route('api/tasks/{id}', name : 'deleteTask', methods: ["DELETE"])]
    public function deleteTask(int $id, TaskRepository $taskRepository, SerializerInterface $serialize, EntityManagerInterface $em)
    {
        $task = $taskRepository->find($id);
        if (!$task) {
            return new JsonResponse(['error' => "L'id n°$id n'a pas été trouvé"], Response::HTTP_NOT_FOUND);
        } 
        $em->remove($task);
        $em->flush();
        //$jsonTask = $serializer->serialize($task, 'json');
        //$taskRepository->deleteOneById($id);
        return new JsonResponse(["success"=> "deleted"], 204);
    }
}
