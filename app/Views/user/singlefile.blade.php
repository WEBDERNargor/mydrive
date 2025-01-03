@extends('user.layout')
@section("top_php")



@php
    use App\Controllers\ServiceController;
    $service = new ServiceController();
    $token = getCookieValue('login_token');
    if ($file->file_public == 0 && $token == null) {
    http_response_code(404);
    header('HTTP/1.1 404 Not Found');
    echo "error page no found";
    exit();
}

    if ($token != null) {
        $user_login = $service->verifyTokenServer($token);
    }
    if($file->file_public == 0 and $user_login['u_id'] != $file->u_id){
        http_response_code(404);
        header('HTTP/1.1 404 Not Found');
        echo "error page no found";
        exit();

    }

@endphp



@endsection
@section('head')
    <link href="https://vjs.zencdn.net/8.6.1/video-js.css" rel="stylesheet" />
    <style>
        .video-container {
            position: relative;
            width: 100%;
            max-width: 100%;
            aspect-ratio: 16/9;
        }
        .video-js {
            width: 100%;
            height: 100%;
        }
    </style>
    <title>{{$file->file_name}} | {{NAME()}}</title>
@endsection

@section('content')
    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <!-- File Preview Section -->
                <div class="mb-6">
                    @php
                        $fileExtension = pathinfo($file->file_raw_name . '.' . $file->file_ext, PATHINFO_EXTENSION);
                        $fileName = pathinfo($file->file_raw_name . '.' . $file->file_ext, PATHINFO_FILENAME);
                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isVideo = in_array(strtolower($fileExtension), ['mp4', 'webm', 'ogg']);
                        $isArchive = in_array(strtolower($fileExtension), ['zip', 'rar', '7z']);
                    @endphp

                    <div class="flex justify-center items-center bg-gray-100 rounded-lg p-4">
                        @if ($isImage)
                            <img src="{{ URL() }}/file/{{ $fileName }}/{{ $fileExtension }}"
                                alt="{{ $file->file_name }}" class="max-w-full h-auto rounded">
                        @elseif($isVideo)
                            <div class="video-container">
                                <video id="my-video" class="video-js vjs-big-play-centered" controls preload="metadata">
                                    <source src="{{ URL() }}/stream/{{ $fileName }}/{{ $fileExtension }}" type="video/{{ $fileExtension }}" />
                                </video>
                            </div>

                        @elseif($isArchive)
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20 6h-3V4c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM9 4h6v2H9V4zm11 15H4V8h16v11z" />
                                </svg>
                            </div>
                        @else
                            <div class="text-center">
                                <svg class="w-24 h-24 mx-auto text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- File Information -->
                <div class="text-center">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $file->file_name }}</h2>
                    <p class="text-gray-600 mb-4">Uploaded by {{ $file->u_fullname }}</p>

                    <div class="flex justify-center gap-4">
                        <a href="{{ URL() }}/file/{{ $fileName }}/{{ $fileExtension }}"
                            download="{{ $file->file_name }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>

                        <button onclick="copyShareLink()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://vjs.zencdn.net/8.6.1/video.min.js"></script>
    <script>
        function copyShareLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy link: ', err);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('#my-video')) {
                var player = videojs('my-video', {
                    controls: true,
                    fluid: true,
                    playbackRates: [0.5, 1, 1.5, 2],
                    userActions: {
                        hotkeys: true
                    },
                    html5: {
                        nativeTextTracks: false,
                        nativeAudioTracks: false,
                        nativeVideoTracks: false,
                        preloadTextTracks: false
                    },
                    controlBar: {
                        children: [
                            'playToggle',
                            'volumePanel',
                            'currentTimeDisplay',
                            'timeDivider',
                            'durationDisplay',
                            'progressControl',
                            'playbackRateMenuButton',
                            'fullscreenToggle'
                        ]
                    }
                });

                // Optimize buffering
                player.reloadSourceOnError({
                    getSource: function(reload) {
                        reload({
                            src: player.currentSource().src,
                            type: player.currentSource().type
                        });
                    }
                });
            }
        });
    </script>
@endsection