@extends('layouts.app')

@section('style')
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
  <div class="container-fluid">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('lab.show', $lab->id) }}">{{ $lab->course_code }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('group.index', $lab->id) }}">Groups</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Group Members</li>
      </ol>
    </nav>

    @include('partials._messages')

    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">Add Group Members</div>
            <div class="card-body">
              {{ Form::open(array('url' => route('groupmember.store', $lab->id, $group->id) )) }}
                <div class="form-group">
                  {{ Form::label('members', 'Group Members:')}}
                  {{ Form::select('members[]', $students, null, array('class' => 'form-control', 'multiple' => 'true', 'id' => 'members')) }}
                </div>
                <div class="form-group">
                  {{ Form::hidden('lab', $lab->id) }}
                  {{ Form::submit('Add Members', array('class' => 'btn  btn-primary btn-block'))}}
                </div>
              {{ Form::close() }}
            </div>
          </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>

$("#members").select2({
	placeholder: "Select students to add to Group",
	tokenSeparators: [';', ' ', ',']
});

</script>
@endsection
