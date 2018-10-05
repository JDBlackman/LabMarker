@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @can('admin')
                      <a href="{{ route('student.index') }}">
                        <button class="btn btn-primary">Add Students</button>
                      </a>
                    @endcan

                    @can('create labs')
                      <a href="{{ route('lab.create') }}">
                        <button class="btn btn-secondary">Create Lab</button>
                      </a>
                    @endcan

                    <a href="{{ route('lab.index') }}">
                      <button class="btn btn-info">View Lab's</button>
                    </a>



                    <p>Your Permissions</p>
                    <ul>
                      @foreach($permissions as $permission)
                        <li>{{ $permission->name }}</li>
                      @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection