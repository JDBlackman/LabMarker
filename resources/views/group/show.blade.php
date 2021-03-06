@extends('layouts.app')

@section('style')
  <link href="{{ asset('css/foundation-icons.css') }}" rel="stylesheet" />
@endsection


@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('lab.index') }}">Labs</a></li>
      <li class="breadcrumb-item"><a href="{{ route('lab.show', $lab->id) }}">{{ $lab->course_code }}</a></li>
      <li class="breadcrumb-item active">{{ $group->name }}</li>
    </ol>
  </nav>

  @include('partials._messages')

  <div class="row justify-content-center">
      <div class="col-md-6">
        <h1>{{ $lab->course_code }} - {{ $group->name }}</h1>
        @foreach($students as $user)
        <h2>{{ $user->surname }}, {{ $user->firstname}} ({{ $user->identifier}})</h2>
        <div class="card mt-5">
            <div class="card-header">{{ $user->firstname }}'s Task Progress</div>
            <div class="card-body">
              <table class="table">
                <tr>
                  <th>Task Name</th>
                  <th> </th>
                  <th>Action</th>
                </tr>
                @foreach($tasks as $key => $task)
                <tr>
                  <td>{{ $task->name }}</td>
                  <td><i class="{{ $studentsProgress[$user->identifier]->get($key)->getProgressIcon()}}"></i></td>
                  <td>
                    @if($studentsProgress[$user->identifier]->get($key)->status == 0)
                      {{ Form::open(['route' => ['taskprogress.store'], 'class' => 'form-add']) }}
                        {{ Form::hidden('lab', $lab->id) }}
                        {{ Form::hidden('user_id', $user->id) }}
                        {{ Form::hidden('task_id', $task->id) }}
                        {{Form::submit('Sign Off', ['class' => 'btn btn-danger']) }}
                      {{ Form::close() }}
                    @else
                      <button class="btn btn-primary">Signed Off</button>
                    @endif
                  </td>
                </tr>
                @endforeach
              </table>
            </div>
        </div>
        @endforeach
      </div>
  </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function() {
  $(".form-add").submit(function(e) {

      var submit = $("input[type=submit]",this).prop('disabled', true).removeClass('btn-danger').addClass('btn-warning').val("Working...");

      var form = $(this);
      var url = form.attr('action');

      $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function(data) {
             if(data.status == "success"){
               submit.removeClass("btn-warning").addClass("btn-primary").prop('disabled', true).val('Signed Off');
               var icon = $('#' + data.user_id + '-' + data.task_id).removeClass('fi-x').addClass(data.icon);
             }
         }
      });

      e.preventDefault(); // avoid to execute the actual submit of the form.
  });
});
</script>
@endsection
