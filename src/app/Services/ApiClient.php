<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('API_BASE_URL', 'http://localhost:3002'), '/');
    }

    /**
     * Login against the .NET API and store the JWT token in session.
     */
    public function login(string $email, string $password): array
    {
        $response = Http::post("{$this->baseUrl}/login", [
            'email'    => $email,
            'password' => $password,
        ]);

        $body = $response->json();

        if ($response->successful() && ($body['success'] ?? false)) {
            Session::put('jwt_token', $body['data']['token']);
            Session::put('user', $body['data']['user']);
            return ['success' => true, 'user' => $body['data']['user']];
        }

        return ['success' => false, 'message' => $body['message'] ?? 'Login failed'];
    }

    /**
     * GET /students/search
     */
    public function searchStudents(string $term = '', string $status = 'active'): array
    {
        $token = Session::get('jwt_token');

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/students/search", [
                'term'   => $term,
                'status' => $status,
                'limit'  => 50,
            ]);

        return $response->json() ?? ['success' => false, 'data' => []];
    }

    /**
     * GET /grades
     */
    public function getGrades(): array
    {
        $token = Session::get('jwt_token');

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/grades");

        return $response->json() ?? ['success' => false, 'data' => []];
    }

    /**
     * GET /scores with query params.
     */
    public function getScores(int $studentId, int $gradeId, int $year, int $month): array
    {
        $token = Session::get('jwt_token');

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/scores", [
                'studentId' => $studentId,
                'gradeId'   => $gradeId,
                'year'      => $year,
                'month'     => $month,
            ]);

        return $response->json() ?? ['success' => false, 'message' => 'No response from API'];
    }

    /**
     * POST /scores to record a single score.
     */
    public function recordScore(array $data): array
    {
        $token = Session::get('jwt_token');

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/scores", $data);

        return $response->json() ?? ['success' => false, 'message' => 'No response from API'];
    }
}
