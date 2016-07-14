<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LectureController extends Controller
{
    private $student;

    /**
     * LectureController constructor.
     */
    public function __construct()
    {
        $this->student = authUser();
    }

    public function index()
    {
        $singleLectures = $this->student->singleLectures;
        $multiLectures = $this->student->multiLectures;

        $lectures = $singleLectures->merge($multiLectures)->sortByDesc('start_at');

        $lectures->load('teacher');

        return frontendView('lectures.index', compact('lectures'));
    }
}