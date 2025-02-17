@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Contact Details</h2>
        <p><strong>Name:</strong> {{ $contact->name }}</p>
        <p><strong>Phone:</strong> {{ $contact->dial_code . ' ' . $contact->phone }}</p>
        <p><strong>Email:</strong> {{ $contact->email }}</p>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back</a>
    </div>
@endsection
