@extends('layouts.dashboard')

@section('title', 'Trash Categories')

@section('breadcumb')
    @parent
    <li class="breadcrumb-item ">Categories</li>
    <li class="breadcrumb-item active">Trash</li>
@endsection

@section('content')
    <div class="mb-5">
        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-primary">Back</a>
    </div>
    <x-alert type="success" />
    <form action="{{ URL::current() }}" method="get" class="d-flex justify-content-between mb-4">
        <input type="text" name="name" placeholder="Name" class="mx-2" />
        <select name="status" class="form-control mx-2">
            <option value="">All</option>
            <option value="active" @selected(request('status') == 'active')>Active</option>
            <option value="archived" @selected(request('status') == 'archived')>Archived</option>
        </select>
        <button class="btn btn-dark mx-2">Filter</button>
    </form>
    {{-- table.table>thead>th>th*4 --}}
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Deleted At</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>

            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>{{ $category->status }}</td>
                    <td>{{ $category->deleted_at }}</td>

                    <td>
                        <form action="{{ route('categories.restore', [$category->id]) }} " method="post">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-outline-info">Restore</button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('categories.force-delete', [$category->id]) }} " method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">NO categories defind.</td>
                </tr>
            @endforelse

        </tbody>
    </table>
    {{ $categories->withQueryString()->links() }}
@endsection
