<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ClassRoom;
use App\Entity\Student;
use App\Entity\User;

class StudentController extends AbstractController
{
    /**
     * @Route("/api/students/list", name="students_list")
     * Return list of students either part of a class or all students
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/students/list', name: 'students_list', methods: ['POST'])]
    public function apiStudentsList(EntityManagerInterface $entityManager, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $json = [ // Prepare data for FE
            'students' => [],
        ];

        if (isset($data['classRoom'])) { // Get list of students part of a classroom
            $classRoom = $entityManager->getRepository(ClassRoom::class)->find($data['classRoom']);
            $students = $entityManager->getRepository(Student::class)->findByClassRooms($classRoom);
        } else { // Get all students
            $students = $entityManager->getRepository(Student::class)->findAll();
        }

        foreach ($students as $student) { // Loop through all students and add to JSON data
            $id = $student->getId();

            $json['students'][$id] = [
                'value' => $id,
                'label' => $student->getName(),
            ];
        }

        if (!empty($data['allTeachers'])) { // Include all teacher objects for creating new classrooms

            $json['allTeachers'] = [];

            // Get all User Objects by role
            $teachers = $entityManager->getRepository(User::class)->findUsers();

            foreach ($teachers as $teacher) {
                $id = $teacher->getId();

                $json['allTeachers'][$id] = [
                    'value' => $id,
                    'label' => $teacher->getUsername(),
                ];
            }
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($json));

        return $response;
    }
}
