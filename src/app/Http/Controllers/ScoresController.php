<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class ScoresController extends Controller
{
    public function index(Request $request)
    {
        $api = new ApiClient();

        // Load grades for dropdown
        $gradesResult = $api->getGrades();
        $grades = ($gradesResult['success'] ?? false) ? $gradesResult['data'] : [];

        // Load students for dropdown
        $studentsResult = $api->searchStudents('', 'active');
        $students = ($studentsResult['success'] ?? false) ? $studentsResult['data'] : [];

        $scores   = [];
        $error    = null;
        $searched = false;
        $selectedStudent = null;

        $filters = [
            'studentId' => $request->get('studentId', ''),
            'gradeId'   => $request->get('gradeId', ''),
            'year'      => $request->get('year', date('Y')),
            'month'     => $request->get('month', date('n')),
        ];

        // Search if we have all required params
        if ($filters['studentId'] && $filters['gradeId'] && $filters['month']) {
            $searched = true;
            $result = $api->getScores(
                (int) $filters['studentId'],
                (int) $filters['gradeId'],
                (int) $filters['year'],
                (int) $filters['month']
            );

            if ($result['success'] ?? false) {
                $scores = $result['data'] ?? [];
            } else {
                $error = $result['message'] ?? 'Error al consultar calificaciones';
            }

            // Find the selected student name
            foreach ($students as $s) {
                if ($s['id'] == $filters['studentId']) {
                    $selectedStudent = $s;
                    break;
                }
            }
        }

        return view('scores.index', compact(
            'scores', 'filters', 'error', 'searched',
            'grades', 'students', 'selectedStudent'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'studentId' => 'required|integer|min:1',
            'subjectId' => 'required|integer|min:1',
            'gradeId'   => 'required|integer|min:1',
            'year'      => 'required|integer|min:2020',
            'month'     => 'required|integer|min:1|max:12',
            'score'     => 'required|numeric|min:0|max:10',
        ]);

        $api = new ApiClient();
        $result = $api->recordScore($request->only([
            'studentId', 'subjectId', 'gradeId', 'year', 'month', 'score',
        ]));

        $redirect = redirect('/scores?' . http_build_query([
            'studentId' => $request->studentId,
            'gradeId'   => $request->gradeId,
            'year'      => $request->year,
            'month'     => $request->month,
        ]));

        if ($result['success'] ?? false) {
            return $redirect->with('success', 'Calificación guardada correctamente');
        }

        return $redirect->with('error', $result['message'] ?? 'Error al guardar');
    }
}
