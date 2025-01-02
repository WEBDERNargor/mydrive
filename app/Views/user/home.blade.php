@extends('user.layout')
@section("head")
<title>{{NAME()}}</title>
@endsection
@section('content')
   


<div class="container mx-auto px-4 py-8 mt-[100px]">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold">Welcome to Your Cloud Drive</h1>
        <p class="text-lg text-gray-600">Access your files from anywhere, anytime.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Upload Files</h2>
            <p class="text-gray-600 mb-4">Easily upload your files to the cloud.</p>
            <button class="bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Manage Files</h2>
            <p class="text-gray-600 mb-4">Organize and manage your files efficiently.</p>
            <button class="bg-blue-500 text-white px-4 py-2 rounded">Manage</button>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Share Files</h2>
            <p class="text-gray-600 mb-4">Share your files with others securely.</p>
            <button class="bg-blue-500 text-white px-4 py-2 rounded">Share</button>
        </div>
    </div>
</div>


@endsection