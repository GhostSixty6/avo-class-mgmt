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

class ClassRoomController extends AbstractController
{
    /**
     * @Route("/api/classrooms/list", name="classrooms_list")
     * Return stats and list of classrooms the user has access to
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/classrooms/list', name: 'classrooms_list', methods: ['POST'])]
    public function apiClassRoomList(EntityManagerInterface $entityManager)
    {
        $json = [];
        $currentUser = $this->getUser();
        $is_admin = in_array('ROLE_ADMIN', $currentUser->getRoles());

        $json = [ // Prepare the data for FE
            'stats' => [
                'classrooms' => 0,
                'students' => 0,
            ],
            'current' => [],
            'all' => []
        ];

        $classRooms = $entityManager->getRepository(ClassRoom::class)->findAll();

        foreach ($classRooms as $classRoom) {

            $id = $classRoom->getId();

            $item = [ // Prepare the item
                'id' => $id,
                'name' => $classRoom->getName(),
                'students' => $classRoom->getStudentCount(),
                'teachers' => $classRoom->getTeacherCount(),
            ];

            if (in_array($currentUser->getId(), $classRoom->getTeachersIds())) { // The user is a teacher so add to current
                $json['current'][$id] = $item;
                $json['stats']['classrooms'] += 1;
                $json['stats']['students'] += $item['students'];
            } else if ($is_admin) { // The user is an admin so add to all
                $json['all'][$id] = $item;
                $json['stats']['classrooms'] += 1;
                $json['stats']['students'] += $item['students'];
            }
        }

        if ($is_admin) { // Add additional stats for admins
            $json['stats']['teachers'] = $entityManager->getRepository(User::class)->countByRoleNot('ROLE_ADMIN');
            $json['stats']['admins'] = $entityManager->getRepository(User::class)->countByRole('ROLE_ADMIN');
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($json));

        return $response;
    }

    /**
     * @Route("/api/classrooms/info", name="classrooms_info")
     * Return info on a specific class
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/classrooms/info', name: 'classrooms_info', methods: ['POST'])]
    public function apiClassRoomInfo(EntityManagerInterface $entityManager, Request $request)
    {
        $json = [];
        $data = json_decode($request->getContent(), true);

        if (!isset($data['classRoom'])) {
            throw new \InvalidArgumentException('Missing classRoom variable');
        }

        $currentUser = $this->getUser();
        $is_admin = in_array('ROLE_ADMIN', $currentUser->getRoles());
        $classRoom = $entityManager->getRepository(ClassRoom::class)->find($data['classRoom']);

        if (!$classRoom) { // the ClassRoom object must exist
            throw new \InvalidArgumentException('Classroom ' . $data['classRoom'] . ' does not exist');
        } else if (!$is_admin && !in_array($currentUser->getId(), $classRoom->getTeachersIds())) {  // The user must atleast be a class teacher or admin
            throw new \InvalidArgumentException('User does not have access to this class');
        }

        $json = [ // Prepare the data for FE
            'className' => $classRoom->getName(),
            'status' => $classRoom->getStatus(),
            'students' => [],
            'teachers' => [],
        ];

        // Get Students
        foreach ($classRoom->getStudents() as $student) {
            $id = $student->getId();

            $json['students'][$id] = [
                'value' => $id,
                'label' => $student->getName(),
            ];
        }

        // Get Teachers
        foreach ($classRoom->getTeachers() as $teacher) {

            $id = $teacher->getId();

            $json['teachers'][$id] = [
                'value' => $id,
                'label' => $teacher->getUsername(),
            ];
        }

        // Include all student objects for updating classrooms
        if (!empty($data['allStudents'])) {

            $json['allStudents'] = [];
            $students = $entityManager->getRepository(Student::class)->findAll();

            foreach ($students as $student) { // Loop through all students and add to JSON data
                $id = $student->getId();

                $json['allStudents'][$id] = [
                    'value' => $id,
                    'label' => $student->getName(),
                ];
            }
        }

        // Include all teacher objects for updating classrooms
        if (!empty($data['allTeachers'])) {

            $json['allTeachers'] = [];

            // Get all User Objects by role
            $teachers = $entityManager->getRepository(User::class)->findAll();

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

    /**
     * @Route("/api/classrooms/update", name="classrooms_update")
     * Creates or updates a ClassRoom Object
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/classrooms/update', name: 'classrooms_update', methods: ['POST'])]
    public function apiClassRoomUpdate(EntityManagerInterface $entityManager, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $currentUser = $this->getUser();

        if (!empty($data['classRoom'])) { // Check if we are updating
            $classRoom = $entityManager->getRepository(ClassRoom::class)->find($data['classRoom']);

            if (!$classRoom) { // the ClassRoom object must exist
                throw new \InvalidArgumentException('Classroom ' . $data['classRoom'] . ' does not exist');
            }
        } else { // Or Creating a new Class
            $classRoom = new ClassRoom();
            $classRoom->setName("New Classroom");
            $entityManager->persist($classRoom);
        }

        if (empty($data['name'])) { // Require a name atleast
            throw new \InvalidArgumentException('Missing class name for ' . $data['classRoom']);
        }

        $classRoom->setName($data['name']);

        // Add Teachers
        $data['teachers'] = !empty($data['teachers']) ? array_filter($data['teachers']) : []; // Clean up the teachers array

        if (!empty($data['teachers'])) {

            $teacher_count = 0; // In case we need to add the creator as a teacher
            $classRoom->clearTeachers();

            foreach ($data['teachers'] as $teacherId => $teacherLabel) { // Loop through teacher values to load User objects
                $teacher = $entityManager->getRepository(User::class)->find($teacherId);

                if ($teacher) {
                    $classRoom->addTeacher($teacher);
                    $teacher_count++;
                }
            }

            if (empty($teacher_count)) { //  Make sure the class has at least one teacher
                $classRoom->addTeacher($currentUser);
            }
        }

        // Add Students
        $data['students'] = !empty($data['students']) ? array_filter($data['students']) : []; // Clean up the students array

        if (!empty($data['students'])) {

            $classRoom->clearStudents();

            foreach ($data['students'] as $studentId => $studentLabel) {  // Loop through student values to load Student objects

                if (strpos($studentId, 'new') !== false) { // Create this student before we add it
                    $student = new Student();
                    $student->setName($studentLabel);
                    $entityManager->persist($student);
                } else {
                    $student = $entityManager->getRepository(Student::class)->find($studentId);
                }

                if ($student) {
                    $classRoom->addStudent($student);
                    $entityManager->persist($student); // Save the Student object
                }
            }
        }

        $entityManager->flush(); // Update the DB

        $response = new Response(); // Send 200 response so that FE knows we succeeded
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode([]));

        return $response;
    }
}
