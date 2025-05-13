@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Credits</h2>
                    @can('create', App\Models\Credit::class)
                        <a href="{{ route('credits.create') }}" class="btn btn-primary">Add Credits</a>
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
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($credits as $credit)
                                    <tr>
                                        <td>{{ $credit->user->name }}</td>
                                        <td>{{ $credit->amount }}</td>
                                        <td>{{ $credit->type }}</td>
                                        <td>{{ $credit->description }}</td>
                                        <td>{{ $credit->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('view', $credit)
                                                    <a href="{{ route('credits.show', $credit) }}" class="btn btn-info btn-sm">View</a>
                                                @endcan
                                                
                                                @can('update', $credit)
                                                    <a href="{{ route('credits.edit', $credit) }}" class="btn btn-primary btn-sm">Edit</a>
                                                @endcan
                                                
                                                @can('delete', $credit)
                                                    <form action="{{ route('credits.destroy', $credit) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this credit record?')">Delete</button>
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