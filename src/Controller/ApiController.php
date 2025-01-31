<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class ApiController extends AbstractController {
    private string $usersPath = __DIR__ . "/../../data/users.json";

    #[Route('/users', name: 'users', methods: ['GET'])]
    public function getCollection(): JsonResponse {
        return new JsonResponse([
            'data' => $this->getUsersFromJson()
        ], Response::HTTP_OK);
    }

    #[Route('/users/{id}', name: 'user', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getItem(string $id): JsonResponse {
        $user = $this->findUserById($id);

        return new JsonResponse([
            'data' => $user
        ], Response::HTTP_OK);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function createItem(Request $request): JsonResponse {
        $jsonData = json_decode($request->getContent(), true);

        $this->validateUserData($jsonData);
        $users = $this->getUsersFromJson();

        $newUser = [
            'id' => count($users) + 1,
            'username' => $jsonData['username'],
            'email' => $jsonData['email']
        ];

        $users[] = $newUser;
        $this->saveUsersToJson($users);

        return new JsonResponse([
            'data' => $newUser
        ], Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteItem(string $id): JsonResponse {
        $this->deleteUserById($id);
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}', name: 'update_user', methods: ['PATCH'])]
    #[IsGranted("ROLE_ADMIN")]
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

    private function getUsersFromJson(): array {
        if (!file_exists($this->usersPath)) {
            return [];
        }

        return json_decode(file_get_contents($this->usersPath), true) ?? [];
    }

    private function saveUsersToJson(array $users): void {
        file_put_contents($this->usersPath, json_encode($users, JSON_PRETTY_PRINT));
    }

    private function findUserById($id): array {
        foreach ($this->getUsersFromJson() as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }

        throw new NotFoundHttpException("User with id " . $id . " not found");
    }

    private function deleteUserById($id): void {
        $users = $this->getUsersFromJson();

        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['id'] == $id) {
                unset($users[$i]);
                $users = array_values($users);
                $this->saveUsersToJson($users);
                return;
            }
        }

        throw new NotFoundHttpException("User with id " . $id . " not found");
    }

    private function updateUsernameById($id, string $username): array {
        $users = $this->getUsersFromJson();

        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['id'] == $id) {
                $users[$i]['username'] = $username;
                $this->saveUsersToJson($users);
                return $users[$i];
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