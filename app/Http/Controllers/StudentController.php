<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Enrollment;
use App\Student;
use App\Task;
use App\TaskProgress;
use App\Lab;

use Session;

class StudentController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
      return view('student.home');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::all();
        return view('student.index')->with('students', $students);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $student = new Student;
      $student->student_number = $request->student_number;
      $student->name = $request->student_name;
      $student->save();

      Session::flash('success', 'You have created the student account for \'' . $student->name . '\'');

      return redirect()->route('student.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $labs = array();
        $student = Student::findOrFail($id);
        $enrolled = Enrollment::where('student_id', $student->id)->join('labs', 'lab_id', '=', 'labs.id')->get(array('labs.course_code', 'labs.id', 'student_id'));

        foreach($enrolled as $lab){

          $taskStatus = array();
          $lab_data = array();

          $tasks = Task::where('lab_id', $lab->id)->get();

          $taskProgress = TaskProgress::where([
            ['student_id', '=', $student->id],
            ['lab_id', '=', $lab->id],
          ])->get();


          foreach($tasks as $task){
            if($taskProgress->contains('task_id', $task->id)){
              $taskStatus[] = array('status' => 1, 'task' => $task);
            } else {
              $taskStatus[] = array('status' => 0, 'task' => $task);
            }
          }

          $lab_data = array('lab' => $lab, 'tasks' => $taskStatus);
          $labs[$lab->id] = $lab_data;
        }


        return view('student.show')->with('student', $student)->with('labs', $labs);

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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
