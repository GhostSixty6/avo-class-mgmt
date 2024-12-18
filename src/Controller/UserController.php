<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users/list", name="users_list")
     * Return a list of teachers and admins
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/users/list', name: 'users_list', methods: ['POST'])]
    public function apiUsersList(EntityManagerInterface $entityManager)
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) { // Make extra sure the user is an admin
            throw new \InvalidArgumentException('The current user is not an admin');
        }

        $json = [ // Prepare the data for FE
            'teachers' => [],
            'admins' => [],
        ];

        // Get all User Objects by role
        $teachers = $entityManager->getRepository(User::class)->findByRoleNot('ROLE_ADMIN');
        $admins = $entityManager->getRepository(User::class)->findByRole('ROLE_ADMIN');

        foreach ($teachers as $teacher) {

            $id = $teacher->getId();

            $json['teachers'][$id] = [
                'value' => $id,
                'label' => $teacher->getUsername(),
            ];
        }

        foreach ($admins as $admin) {

            $id = $admin->getId();

            $json['admins'][$id] = [
                'value' => $id,
                'label' => $admin->getUsername(),
            ];
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($json));

        return $response;
    }

    /**
     * @Route("/api/users/info", name="users_info")
     * Return info on a specific User object
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/users/info', name: 'users_info', methods: ['POST'])]
    public function apiUsersInfo(EntityManagerInterface $entityManager, Request $request)
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) { // Make extra sure the user is an admin
            throw new \InvalidArgumentException('The current user is not an admin');
        }

        $json = [];
        $data = json_decode($request->getContent(), true);

        if (!isset($data['user'])) {
            throw new \InvalidArgumentException('Missing user variable');
        }

        $infoUser = $entityManager->getRepository(User::class)->find($data['user']);

        if (!$infoUser) { // the User object must exist
            throw new \InvalidArgumentException('User ' . $data['user'] . ' does not exist');
        }

        $json = [ // Prepare the data for FE
            'username' => $infoUser->getUsername(),
            'roles' => $infoUser->getRoles(),
        ];

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($json));

        return $response;
    }

    /**
     * @Route("/api/users/update", name="users_update")
     * Creates or updates a User Object
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/users/update', name: 'users_update', methods: ['POST'])]
    public function apiUsersUpdate(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) { // Make extra sure the user is an admin
            throw new \InvalidArgumentException('The current user is not an admin');
        }

        $data = json_decode($request->getContent(), true);

        if (!empty($data['user'])) { // Check if we are updating
            $user = $entityManager->getRepository(User::class)->find($data['user']);
            $new = FALSE;

            if (!$user) { // the User object must exist
                throw new \InvalidArgumentException('User ' . $data['user'] . ' does not exist');
            }
        } else { // Or Creating a new User
            $user = new User();
            $new = TRUE;
        }

        if (empty($data['name'])) { // Require a name atleast
            throw new \InvalidArgumentException('Missing username for ' . $data['user']);
        }

        $user->setUsername($data['name']);

        if (!empty($data['password'])) { // If we receive a password, update it
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        } else if ($new) { // New users MUST have a password
            throw new \InvalidArgumentException('Missing password for ' . $data['user']);
        }

        if (!empty($data['admin'])) { // set only if the user has been set as an admin
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        }

        $entityManager->persist($user); // Save the User Object
        $entityManager->flush(); // Update the DB

        $response = new Response(); // Send 200 response so that FE knows we succeeded
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode([]));

        return $response;
    }
}
