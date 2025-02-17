@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>All Contacts</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
                @if (session('skipped_records'))
                    <ul>
                        @foreach (session('skipped_records') as $record)
                            <li>{{ $record['phone'] ?? 'Unknown' }} - {{ $record['reason'] }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <form action="{{ route('contacts.importXML') }}" method="POST" enctype="multipart/form-data" class="mb-3">
            @csrf
            <div class="input-group">
                <input type="file" name="file" class="form-control" accept=".xml" required>
                <button type="submit" class="btn btn-primary">Import XML</button>
            </div>
        </form>

        <a href="{{ route('contacts.create') }}" class="btn btn-success mb-3">Add New Contact</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @isset($contacts)
                    @foreach ($contacts as $contact)
                        <tr>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->dial_code . ' ' . $contact->phone }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>
                                <a href="{{ route('contacts.show', $contact->id) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">No contacts found.</td>
                    </tr>
                @endisset
            </tbody>
        </table>
    </div>

@endsection
