<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Enrollment;
use App\Student;
use App\User;
use App\Lab;
use App\Task;
use Auth;

class LabController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:cosi');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $labs = Lab::all();
        return view('lab.index')->with('labs', $labs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('lab.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
          'course_code' => 'required|unique:labs',
          'year' => 'required',
        ]);

        $user = Auth::user();

        $lab = new Lab;
        $lab->course_code = $request->course_code;
        $lab->lecturer_id = $user->id;
        $lab->year = $request->year;
        $lab->save();


        return redirect()->route('lab.show', $lab->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $lab = Lab::findOrFail($id);
      $students = $lab->enrolledStudents()->get();
      return view('lab.show')->with('lab', $lab)->with('students', $students);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $lab = Lab::findOrFail($id);
        return view('lab.edit')->with('lab', $lab);
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
        $request->validate([
          //'course_code' => 'required|unique:labs,course_code,' . $id,
          'year' => 'required',
        ]);

        $lab = Lab::findOrFail($id);
        $lab->year = $request->year;
        $lab->save();
        Session::flash('success', 'Successfully updated lab.');
        return redirect()->route('lab.edit', $lab->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lab = Lab::findOrFail($id);
        $lab->delete();

        $permission = Permission::where('name', 'marker ' . $lab->course_code)->first();
        $permission->delete();

        $permission = Permission::where('name', 'lecturer ' . $lab->course_code)->first();
        $permission->delete();

        $permission = Permission::where('name', 'view ' . $lab->course_code)->first();
        $permission->delete();

        Session::flash('success', 'Successfully deleted lab.');
        return redirect()->route('lab.index');
    }


}
