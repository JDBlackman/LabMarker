<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

use App\Student;
use App\Enrollment;
use App\Lab;
use App\User;

class EnrollmentController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
    public function __construct()
    {
        $this->middleware('auth:cosi');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
      $lab = Lab::findOrFail($id);
      $students = $lab->enrolledStudents()->get();
      return view('enrollments.index')->with('students', $students)->with('lab', $lab);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $lab = Lab::findOrFail($id);
        $students = User::role('student')->get();
        foreach($students as $student){
          $dropdown[$student->identifier] = $student->getDropDownName();
        }
        return view('enrollments.create')->with('students', $dropdown)->with('lab', $lab);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {

        $request->validate([
          'students' => 'required',
        ]);

        $lab = Lab::findOrFail($id);
        $enrollments = 0;
        $errors = array();
        $studentsIdentifier = $request->input()["students"];


        foreach($studentsIdentifier as $studentIdentifier){
          $student = User::where('identifier', $studentIdentifier)->first();

          if(!$student){
            $errors[] = "Could not find student " . $studentIdentifier;
            continue;
          }

          if($student->isEnrolled($lab)) {
            $errors[] = $studentIdentifier ." is already enrolled in this lab.";
            continue;
          }

          $enrollment = new Enrollment;
          $enrollment->user_id = $student->id;
          $enrollment->lab_id = $lab->id;
          $enrollment->save();
          $enrollments++;
        }

        if($enrollments > 0){
          Session::flash('success', 'You have enrolled ' . $enrollments . ' students with ' . count($errors) . ' error(s).');
        }
        return redirect()->route('enrollments.create', $lab->id)->withErrors($errors);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($lab_id)
    {
        $lab = Lab::findOrFail($lab_id);
        // create archived lab clone
        $archived_lab = $lab->archiveLab();
        // get all students
        $students = $lab->enrolledStudents()->get();
        foreach($students as $student){
          // unenroll from current lab and add to archive
          $student->unenrollLab($lab, $archived_lab);
        }

        Session::flash('success', 'Successfully reset year.');
        return redirect()->route('lab.show', $lab_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $lab_id = $request->lab;
        $user_id = $request->student;

        $enrollment = Enrollment::where([
          ['lab_id', '=', $lab_id],
          ['user_id', '=', $user_id],
        ])->first();

        $enrollment->delete();

        Session::flash('success', 'Student has been unenrolled');

        return redirect()->route('enrollment.index', $lab_id);
    }
}
