@extends('layouts.dashboard')

@section('title', 'categories')

@section('breadcumb')
    @parent
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
    <div class="mb-5">
        <a href="{{ route('categories.create') }}" class="btn btn-sm btn-outline-primary mr-2">Create</a>
        <a href="{{ route('categories.trash') }}" class="btn btn-sm btn-outline-dark">Trash</a>
    </div>
    <x-alert type="success" />
    <form action="{{ URL::current() }}" method="get" class="d-flex justify-content-between mb-4">
        <input type="text" name="name" placeholder="Name" class="mx-2" value="{{ $name }}" />
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
                <th>Image</th>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Parent</th>
                <th>Products #</th>
                <th>Status</th>
                <th>Created_at</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>

            @forelse ($categories as $category)
                <tr>
                    <td><img src="{{ asset('storage/' . $category->image) }}" alt="" height="50px"></td>
                    <td>{{ $category->id }}</td>
                    <td><a href="{{ route('categories.show', $category->id) }}">{{ $category->name }}</a></td>
                    <td>{{ $category->description }}</td>
                    <td>{{ $category->parent->name }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td>{{ $category->status }}</td>
                    <td>{{ $category->created_at }}</td>
                    <td>
                        <a href="{{ route('categories.edit', $category->id) }}"
                            class="btn btn-sm btn-outline-success">Edit</a>
                    </td>
                    <td>
                        <form action="{{ route('categories.destroy', [$category->id]) }} " method="post">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">NO categories defind.</td>
                </tr>
            @endforelse

        </tbody>
    </table>
    {{ $categories->withQueryString()->links() }}
@endsection
