<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController {
    public $users = [
        ["id" => 1, "username" => "john_doe", "email" => "john@example.com"],
        ["id" => 2, "username" => "jane_smith", "email" => "jane@example.com"],
        ["id" => 3, "username" => "michael_b", "email" => "michael@example.com"],
        ["id" => 4, "username" => "sarah_lee", "email" => "sarah@example.com"],
        ["id" => 5, "username" => "david_jones", "email" => "david@example.com"],
        ["id" => 6, "username" => "emma_w", "email" => "emma@example.com"],
        ["id" => 7, "username" => "william_k", "email" => "william@example.com"],
        ["id" => 8, "username" => "olivia_r", "email" => "olivia@example.com"],
        ["id" => 9, "username" => "james_m", "email" => "james@example.com"],
        ["id" => 10, "username" => "sophia_t", "email" => "sophia@example.com"],
        ["id" => 11, "username" => "delinev", "email" => "ipz231_ldyu@studen.ztu.edu.ua"],
    ];

    #[Route('/users', name: 'users', methods: ['GET'])]
    public function getCollection(): JsonResponse {
        return new JsonResponse([
            'data' => $this->users
        ], Response::HTTP_OK);
    }

    #[Route('/users/{id}', name: 'user', methods: ['GET'])]
    public function getItem(string $id): JsonResponse {
        $user = $this->findUserById($id);

        return new JsonResponse([
            'data' => $user
        ], Response::HTTP_OK);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createItem(Request $request): JsonResponse {
        $jsonData = json_decode($request->getContent(), true);

        $this->validateUserData($jsonData);

        $newUser = [
            'id' => count($this->users) + 1,
            'username' => $jsonData['username'],
            'email' => $jsonData['email']
        ];

        $this->users[] = $newUser;

        return new JsonResponse([
            'data' => $newUser
        ], Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteItem(string $id): JsonResponse {
        $this->deleteUserById($id);
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}', name: 'update_user', methods: ['PATCH'])]
    public function updateItem(Request $request, string $id): JsonResponse {
        $jsonData = json_decode($request->getContent(), true);

        if (!isset($jsonData['username'])) {
            throw new UnprocessableEntityHttpException("Username is required");
        }

        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $jsonData['username'])) {
            throw new UnprocessableEntityHttpException("Username " . $jsonData['username'] . " is not valid");
        }

        $user = $this->updateUsernameById($id, $jsonData['username']);

        return new JsonResponse([
            'data' => $user
        ], Response::HTTP_OK);
    }

    private function findUserById($id): array {
        foreach ($this->users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }

        throw new NotFoundHttpException("User with id " . $id . " not found");
    }

    private function deleteUserById($id): void {
        for ($i = 0; $i < count($this->users); $i++) {
            if ($this->users[$i]['id'] == $id) {
                unset($this->users[$i]);
                $this->users = array_values($this->users);
                return;
            }
        }

        throw new NotFoundHttpException("User with id " . $id . " not found");
    }

    private function updateUsernameById($id, string $username): array {
        for ($i = 0; $i < count($this->users); $i++) {
            if ($this->users[$i]['id'] == $id) {
                $this->users[$i]['username'] = $username;
                return $this->users[$i];
            }
        }

        throw new NotFoundHttpException("User with id " . $id . " not found");
    }

    private function validateUserData(array $data): void {
        if (!isset($data['email'], $data['username'])) {
            throw new UnprocessableEntityHttpException("Username and email are required");
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new UnprocessableEntityHttpException("Email" . $data['email'] . " is not valid");
        }

        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username'])) {
            throw new UnprocessableEntityHttpException("Username " . $data['username'] . " is not valid");
        }
    }
}