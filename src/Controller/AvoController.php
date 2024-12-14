<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ClassRoom;
use App\Entity\Student;
use App\Entity\User;

class AvoController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('avo/index.html.twig', [
            'controller_name' => 'AvoController',
        ]);
    }

    /**
     * @Route("/api/classrooms", name="classrooms")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/classrooms', name: 'classrooms', methods: ['POST'])]
    public function apiClassRooms(EntityManagerInterface $entityManager, Request $request)
    {
        $response = [];
        $data = json_decode($request->getContent(), true);

        if (isset($data['action']) && isset($data['uid'])) {

            $currentUser = $entityManager->getRepository(User::class)->find($data['uid']);

            switch ($data['action']) {
                case 'list':

                    $is_admin = in_array('ROLE_ADMIN', $currentUser->getRoles());

                    $response = [
                        'stats' => [
                            'classrooms' => 0,
                            'students' => 0,
                        ],
                        'current' => [],
                        'all' => []
                    ];

                    $classRooms = $entityManager->getRepository(ClassRoom::class)->findByAll();

                    foreach ($classRooms as $classRoom) {

                        $id = $classRoom->getId();

                        $item = [
                            'id' => $id,
                            'name' => $classRoom->getName(),
                            'students' => $classRoom->getStudentCount(),
                            'teachers' => $classRoom->getTeacherCount(),
                        ];

                        if (in_array($currentUser->getId(), $classRoom->getTeachersIds())) {
                            $response['current'][$id] = $item;
                            $response['stats']['classrooms'] += 1;
                            $response['stats']['students'] += $item['students'];
                        } else if ($is_admin) {
                            $response['all'][$id] = $item;
                            $response['stats']['classrooms'] += 1;
                            $response['stats']['students'] += $item['students'];
                        }
                    }

                    if ($is_admin) {
                        $response['stats']['teachers'] = $entityManager->getRepository(User::class)->findUsersCountByRole('ROLE_TEACHER');
                        $response['stats']['admins'] = $entityManager->getRepository(User::class)->findUsersCountByRole('ROLE_ADMIN');
                    }

                    break;
                case 'info':

                    $is_admin = in_array('ROLE_ADMIN', $currentUser->getRoles());
                    $classRoom = $entityManager->getRepository(ClassRoom::class)->find($data['classRoom']);

                    // Get Students

                    if ($is_admin || in_array($currentUser->getId(), $classRoom->getTeachersIds())) {

                        $response = [
                            'className' => $classRoom->getName(),
                            'students' => [],
                            'teachers' => [],
                        ];

                        $students = $entityManager->getRepository(Student::class)->findByClassRooms($classRoom);

                        foreach ($students as $student) {
                            $id = $student->getId();

                            $response['students'][$id] = [
                                'id' => $id,
                                'name' => $student->getName(),
                            ];
                        }

                        // Get Teachers

                        $teachers = $classRoom->getTeachers();

                        foreach ($teachers as $teacher) {

                            $id = $teacher->getId();

                            $response['teachers'][$id] = [
                                'id' => $id,
                                'name' => $teacher->getUsername(),
                            ];
                        }
                    }

                    break;
                case 'create':

                    $classRoom = new ClassRoom();
                    $classRoom->setName($data['name']);
                    $classRoom->addTeacher($currentUser);

                    $entityManager->persist($classRoom);
                    $entityManager->flush();

                    $response = [
                        'success' => 1
                    ];

                    break;
                case 'update':

                    $classRoom = $entityManager->getRepository(ClassRoom::class)->find($data['classRoom']);

                    if ($classRoom && in_array($currentUser->getId(), $classRoom->getTeachersIds())) {
                        $classRoom->setName($data['name']);
                        $classRoom->setStatus($data['status']);
                        $entityManager->flush();
                    }

                    break;
            }
        }

        $r = new Response();

        $r->headers->set('Content-Type', 'application/json');
        $r->headers->set('Access-Control-Allow-Origin', '*');

        $r->setContent(json_encode($response));

        return $r;
    }

    /**
     * @Route("/api/students", name="students")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/students', name: 'students', methods: ['POST'])]
    public function apiStudents(EntityManagerInterface $entityManager, Request $request)
    {
        $response = [];
        $data = json_decode($request->getContent(), true);

        if (isset($data['action']) && isset($data['uid'])  && isset($data['classRoom'])) {

            $currentUser = $entityManager->getRepository(User::class)->find($data['uid']);
            $classRoom = $entityManager->getRepository(ClassRoom::class)->find($data['classRoom']);

            switch ($data['action']) {
                case 'list':

                    $is_admin = in_array('ROLE_ADMIN', $currentUser->getRoles());

                    $response = [
                        'students' => [],
                    ];

                    if ($is_admin || in_array($currentUser->getId(), $classRoom->getTeachersIds())) {

                        $students = $entityManager->getRepository(Student::class)->findByClassRooms($classRoom);

                        foreach ($students as $student) {
                            $id = $student->getId();

                            $response['students'][$id] = [
                                'id' => $id,
                                'name' => $student->getName(),
                            ];
                        }
                    }

                    break;
                case 'create':

                    foreach ($data['names'] as $name) {
                        $student = new Student();
                        $student->setName($name);
                        $student->addClassRoom($classRoom);

                        $entityManager->persist($student);
                        $entityManager->flush();
                    }

                    $response = [
                        'success' => 1
                    ];

                    break;
                case 'update':

                    $student = $entityManager->getRepository(Student::class)->find($data['student']);

                    if ($student && in_array($currentUser->getId(), $classRoom->getTeachersIds()) && in_array($data['student'], $classRoom->getStudentIds())) {
                        $student->setName($data['name']);
                        $student->setStatus($data['status']);
                        $student->flush();
                    }

                    break;
            }
        }

        $r = new Response();

        $r->headers->set('Content-Type', 'application/json');
        $r->headers->set('Access-Control-Allow-Origin', '*');

        $r->setContent(json_encode($response));

        return $r;
    }

    /**
     * @Route("/api/users", name="users")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/users', name: 'users', methods: ['POST'])]
    public function apiUsers(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $response = [];
        $data = json_decode($request->getContent(), true);

        if (isset($data['action']) && isset($data['uid'])) {

            switch ($data['action']) {
                case 'login':

                    break;
                case 'logout':

                    break;
                case 'auth':

                    break;
                case 'list':

                    $currentUser = $entityManager->getRepository(User::class)->find($data['uid']);
                    $is_admin = in_array('ROLE_ADMIN', $currentUser->getRoles());

                    if ($is_admin) {

                        $response = [
                            'teachers' => [],
                            'admins' => [],
                        ];

                        $teachers = $entityManager->getRepository(User::class)->findUsersByRole('ROLE_TEACHER');
                        $admins = $entityManager->getRepository(User::class)->findUsersByRole('ROLE_ADMIN');

                        foreach ($teachers as $teacher) {

                            $id = $teacher->getId();

                            $response['teachers'][$id] = [
                                'id' => $id,
                                'name' => $teacher->getName(),
                            ];
                        }

                        foreach ($admins as $admin) {

                            $id = $admin->getId();

                            $response['admins'][$id] = [
                                'id' => $id,
                                'name' => $admin->getName(),
                            ];
                        }
                    }

                    break;
                case 'create':

                    $user = new User();
                    $user->setUsername($data['name']);
                    $user->setPassword($passwordHasher->hashPassword($user, $data['pwd']));
                    $user->setRoles($data['roles']);

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $response = [
                        'success' => 1
                    ];

                    break;
                case 'update':

                    $currentUser = $entityManager->getRepository(User::class)->find($data['uid']);
                    $user = $entityManager->getRepository(User::class)->find($data['user']);

                    if ($user && in_array('ROLE_ADMIN', $currentUser->getRoles())) {
                        $user->setUsername($data['name']);
                        $user->setStatus($data['status']);

                        if (isset($data['pwd'])) {
                            $user->setPassword($passwordHasher->hashPassword($user, $data['pwd']));
                        }

                        $user->flush();
                    }

                    break;
            }
        }

        $r = new Response();

        $r->headers->set('Content-Type', 'application/json');
        $r->headers->set('Access-Control-Allow-Origin', '*');

        $r->setContent(json_encode($response));

        return $r;
    }
}
