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
    /**
     * @Template("default/index.html.twig")
     * @Route("/{reactRouting}", name="home", priority="-1", requirements={"reactRouting"="^(?!api).+"}, defaults={"reactRouting": null})
     */
    #[Route('/{reactRouting}', name: 'home', priority: '-1', requirements: ['reactRouting' => '^(?!api).+'], defaults: ['reactRouting' => null])]
    public function index(): Response
    {
        return $this->render('avo/index.html.twig');
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
        $currentUser = $this->getUser();

        if (isset($data['action'])) {

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

                    if ($is_admin || in_array($currentUser->getId(), $classRoom->getTeachersIds())) {

                        $response = [
                            'className' => $classRoom->getName(),
                            'status' => $classRoom->getStatus(),
                            'students' => [],
                            'teachers' => [],
                        ];

                        // Get Students
                        foreach ($classRoom->getStudents() as $student) {
                            $id = $student->getId();

                            $response['students'][$id] = [
                                'value' => $id,
                                'label' => $student->getName(),
                            ];
                        }

                        // Get Teachers
                        foreach ($classRoom->getTeachers() as $teacher) {

                            $id = $teacher->getId();

                            $response['teachers'][$id] = [
                                'value' => $id,
                                'label' => $teacher->getUsername(),
                            ];
                        }
                    }

                    break;
                case 'update':

                    if (!empty($data['classRoom'])) { // Check if we are updating
                        $classRoom = $entityManager->getRepository(ClassRoom::class)->find($data['classRoom']);
                    } else { // Or Creating a new Class
                        $classRoom = new ClassRoom();
                    }

                    $classRoom->setName($data['name']);
                    $entityManager->persist($classRoom); // Make sure we have an object to work with

                    $teacher_count = 0; // In case we need to add the creator as a teacher

                    // Add Teachers
                    if (!empty($data['teachers'])) {
                        foreach ($data['teachers'] as $teacherData) { // Loop through teacher values to load User objects
                            $teacher = $entityManager->getRepository(User::class)->find($teacherData['value']);

                            if ($teacher) {
                                $teacher->addClassRoom($classRoom); // This will update both objects
                                $entityManager->persist($teacher);
                                $teacher_count++;
                            }
                        }
                    }

                    if (empty($teacher_count)) { //  Make sure the class has at least one teacher
                        $classRoom->addTeacher($currentUser);
                    }

                    // Add Students
                    if (!empty($data['students'])) {
                        foreach ($data['students'] as $studentData) { // Loop through student values to load Student objects

                            if (strpos($studentData['value'], 'new') !== false) { // Create this student before we add it
                                $student = new Student();
                                $student->setName($studentData['label']);
                                $entityManager->persist($student);
                            } else {
                                $student = $entityManager->getRepository(User::class)->find($studentData['value']);
                            }

                            if ($student) {
                                $student->addClassRoom($classRoom); // This will update both objects
                                $entityManager->persist($student);
                            }
                        }
                    }

                    $entityManager->flush();

                    $response = [ // Let the FE know of the status
                        'success' => 1
                    ];

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

        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'list':

                    $response = [
                        'students' => [],
                    ];

                    if (isset($data['classRoom'])) {
                        $classRoom = $entityManager->getRepository(ClassRoom::class)->find($data['classRoom']);
                        $students = $entityManager->getRepository(Student::class)->findByClassRooms($classRoom);
                    } else {
                        $students = $entityManager->getRepository(Student::class)->findAll();
                    }

                    foreach ($students as $student) {
                        $id = $student->getId();

                        $response['students'][$id] = [
                            'value' => $id,
                            'label' => $student->getName(),
                        ];
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

        if (isset($data['action'])) {

            switch ($data['action']) {
                case 'info':

                    $response = [
                        'roles' => $this->getUser()->getRoles(),
                    ];

                    break;
                case 'list':

                    if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {

                        $response = [
                            'teachers' => [],
                            'admins' => [],
                        ];

                        $teachers = $entityManager->getRepository(User::class)->findUsersByRole('ROLE_USER');
                        $admins = $entityManager->getRepository(User::class)->findUsersByRole('ROLE_ADMIN');

                        foreach ($teachers as $teacher) {

                            $id = $teacher->getId();

                            $response['teachers'][$id] = [
                                'value' => $id,
                                'label' => $teacher->getName(),
                            ];
                        }

                        foreach ($admins as $admin) {

                            $id = $admin->getId();

                            $response['admins'][$id] = [
                                'value' => $id,
                                'label' => $admin->getName(),
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

                    $user = $entityManager->getRepository(User::class)->find($data['user']);

                    if ($user && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
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
