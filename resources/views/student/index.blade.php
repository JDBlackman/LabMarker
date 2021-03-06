@extends('layouts.app')

@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('lab.index') }}">Labs</a></li>
        <li class="breadcrumb-item active">{{ $lab->course_code }}</li>
      </ol>
    </nav>

    @include('partials._messages')

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1>{{ $lab->course_code }}</h1>
            <!-- Marker/Lecturer/Overseer/Admin -->
            @if(auth()->user()->can('marker ' . $lab->course_code) || auth()->user()->can('view ' . $lab->course_code) || auth()->user()->can('view labs'))
            <div class="card mb-3">
                <div class="card-header">Search Students</div>
                <div class="card-body">
                    <div class="form-group">
                    <label for="student_number">Search Students</label>
                    <select type="select" id="student" name="student" class="input-students form-control">
                        <option value="None"></option>
                        @foreach($groups as  $group)
                          <option value="{{ $group->id }}" data-url="{{ route('group.show', [$lab->id, $group->id]) }}">{{ $group->name }}</option>
                        @endforeach
                        @foreach($students as $student)
                          <option value="{{ $student->id }}" data-url="{{ route('student.show', [$lab->id, $student->id]) }}">{{ $student->firstname }} {{ $student->surname }} ({{ $student->identifier }})</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.input-students').select2();
});

$("#student").on("select2:select", function (e) {
  var url = $('select.input-students').find(':selected').data('url');
  window.location.href = url;
});
</script>
@endsection
