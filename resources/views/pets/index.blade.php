@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Pets</h2>
                    @can('create', App\Models\Pet::class)
                        <a href="{{ route('pets.create') }}" class="btn btn-primary">Add New Pet</a>
                    @endcan
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pets as $pet)
                                    <tr>
                                        <td>{{ $pet->name }}</td>
                                        <td>{{ $pet->type }}</td>
                                        <td>{{ $pet->age }}</td>
                                        <td>{{ $pet->gender }}</td>
                                        <td>
                                            @if($pet->not_available)
                                                <span class="badge bg-danger">Not Available</span>
                                            @else
                                                <span class="badge bg-success">Available</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('view', $pet)
                                                    <a href="{{ route('pets.show', $pet) }}" class="btn btn-info btn-sm">View</a>
                                                @endcan
                                                
                                                @can('update', $pet)
                                                    <a href="{{ route('pets.edit', $pet) }}" class="btn btn-primary btn-sm">Edit</a>
                                                @endcan
                                                
                                                @can('delete', $pet)
                                                    <form action="{{ route('pets.destroy', $pet) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this pet?')">Delete</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 