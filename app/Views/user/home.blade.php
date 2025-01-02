@extends('user.layout')
@section('head')
    <title>{{ NAME() }}</title>
@endsection
@section('content')
    <div class="bg-white mt-[50px]">






        <section class="bg-gray-100 py-20">
            <div class="container mx-auto text-center">
                <h2 class="text-4xl font-bold mb-4">Your Ultimate Cloud Storage Solution</h2>
                <p class="text-lg mb-8">Secure, reliable, and easy to use. Store and share your files with confidence.</p>
                <a href="{{ route('upload') }}" class="bg-blue-500 text-white px-6 py-3 rounded-full">Get Started</a>
            </div>
        </section>

        <section class="py-20">
            <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 p-6 rounded-full inline-block mb-4">
                        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4m-1 4v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2m16-10V5a2 2 0 00-2-2H7a2 2 0 00-2 2v2m0 0h10">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Secure Storage</h3>
                    <p>Keep your files safe with our top-notch security features.</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 p-6 rounded-full inline-block mb-4">
                        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m0 0H8m4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Easy Access</h3>
                    <p>Access your files from anywhere, anytime, on any device.</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 p-6 rounded-full inline-block mb-4">
                        <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m0 0H8m4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Collaboration</h3>
                    <p>Share and collaborate on files with your team effortlessly.</p>
                </div>
            </div>
        </section>


    </div>
@endsection

@section('scripts')
@endsection
