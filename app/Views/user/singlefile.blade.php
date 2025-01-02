@extends('user.layout')

@section('head')
    <style>
        .video-container {
            position: relative;
            width: 100%;
            max-width: 100%;
            background: #000;
            overflow: hidden;
        }

        .video-player {
            width: 100%;
            height: auto;
            display: block;
        }

        .video-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0));
            padding: 10px;
            display: flex;
            flex-direction: column;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .video-controls.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .progress-bar {
            width: 100%;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            position: relative;
            margin-bottom: 10px;
        }

        .progress-bar:hover {
            height: 5px;
        }

        .progress {
            height: 100%;
            background: #ff0000;
            position: absolute;
            left: 0;
            top: 0;
        }

        .controls-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }

        .left-controls,
        .right-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .control-button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .volume-container {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .volume-slider {
            width: 80px;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            position: relative;
        }

        .volume-level {
            height: 100%;
            background: white;
            position: absolute;
            left: 0;
            top: 0;
        }

        .time-display {
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        @media (max-width: 991px) {
            .volume-slider {
                display: none;
            }

            .progress-bar {
                height: 4px;
            }

            .progress-bar:hover {
                height: 4px;
            }

            .controls-row {
                padding: 5px 0;
            }
        }
    </style>
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
                                <div id="playeff"
                                    class="absolute top-1/2 -translate-x-1/2 left-1/2 -translate-y-1/2 px-4 py-2 rounded-full bg-black bg-opacity-50 cursor-pointer hidden">

                                    <i class="fa-solid fa-play text-lg text-white"></i>


                                </div>
                                <div id="stopeff"
                                    class="absolute top-1/2 -translate-x-1/2 left-1/2 -translate-y-1/2 px-4 py-2 rounded-full bg-black bg-opacity-50 cursor-pointer hidden">


                                    <i class="fa-solid fa-pause text-lg text-white"></i>

                                </div>
                                <video class="video-player"
                                    @if ($file->has_thumbnail) poster="{{ URL() }}/thumnail/{{ $fileName }}" @endif
                                    src="{{ URL() }}/stream/{{ $fileName }}/{{ $fileExtension }}"
                                    preload="metadata">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="video-controls">
                                    <div class="progress-bar">
                                        <div class="progress"></div>
                                    </div>
                                    <div class="controls-row">
                                        <div class="left-controls">
                                            <button class="control-button play-pause">
                                                <i class="fas fa-play"></i>
                                            </button>
                                            <div class="volume-container">
                                                <button class="control-button volume">
                                                    <i class="fas fa-volume-up"></i>
                                                </button>
                                                <div class="volume-slider">
                                                    <div class="volume-level"></div>
                                                </div>
                                            </div>
                                            <span class="time-display">
                                                <span class="current-time">0:00</span>
                                                /
                                                <span class="duration">0:00</span>
                                            </span>
                                        </div>
                                        <div class="right-controls">
                                            <button class="control-button fullscreen">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const playeff = document.getElementById('playeff');
            const stopeff = document.getElementById('stopeff');
            const container = document.querySelector('.video-container');
            const video = container.querySelector('.video-player');
            const controls = container.querySelector('.video-controls');
            const playPauseBtn = container.querySelector('.play-pause');
            const volumeBtn = container.querySelector('.volume');
            const volumeSlider = container.querySelector('.volume-slider');
            const volumeLevel = container.querySelector('.volume-level');
            const progressBar = container.querySelector('.progress-bar');
            const progress = container.querySelector('.progress');
            const currentTimeDisplay = container.querySelector('.current-time');
            const durationDisplay = container.querySelector('.duration');
            const fullscreenBtn = container.querySelector('.fullscreen');

            let controlsTimeout;
            let isControlsVisible = true;
            let isMobile = window.innerWidth <= 991;
            let lastTouchTime = 0;

            // Format time in seconds to MM:SS
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                seconds = Math.floor(seconds % 60);
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }

            // Update progress bar
            function updateProgress() {
                const percent = (video.currentTime / video.duration) * 100;
                progress.style.width = `${percent}%`;
                currentTimeDisplay.textContent = formatTime(video.currentTime);
            }

            // Show/hide controls
            function showControls() {
                if (!isControlsVisible) {
                    controls.classList.remove('hidden');
                    isControlsVisible = true;
                }
                startControlsTimer();
            }

            function hideControls() {
                if (isControlsVisible && !video.paused) {
                    controls.classList.add('hidden');
                    isControlsVisible = false;
                }
            }

            function startControlsTimer() {
                clearTimeout(controlsTimeout);
                if (!video.paused) {
                    controlsTimeout = setTimeout(hideControls, 3000);
                }
            }

            // Event Listeners
            playPauseBtn.addEventListener('click', () => {
                if (video.paused) {
                    video.play();
                    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                    startControlsTimer();
                } else {
                    video.pause();
                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            });

            video.addEventListener('loadedmetadata', () => {
                durationDisplay.textContent = formatTime(video.duration);
            });

            video.addEventListener('timeupdate', updateProgress);

            video.addEventListener('play', () => {
                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                playeff.classList.remove('hidden');
                stopeff.classList.add('hidden');
                setTimeout(() => {
                    playeff.classList.add('hidden');
                }, 300);
                startControlsTimer();
            });

            video.addEventListener('pause', () => {
                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                playeff.classList.add('hidden');
                stopeff.classList.remove('hidden');
                setTimeout(() => {
                    stopeff.classList.add('hidden');
                }, 300);
                showControls();
            });

            // Volume control
            volumeBtn.addEventListener('click', () => {
                video.muted = !video.muted;
                volumeBtn.innerHTML = video.muted ?
                    '<i class="fas fa-volume-mute"></i>' :
                    '<i class="fas fa-volume-up"></i>';
                volumeLevel.style.width = video.muted ? '0%' : `${video.volume * 100}%`;
            });

            if (!isMobile) {
                volumeSlider.addEventListener('click', (e) => {
                    const rect = volumeSlider.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const percent = x / rect.width;
                    video.volume = Math.max(0, Math.min(1, percent));
                    volumeLevel.style.width = `${video.volume * 100}%`;
                    video.muted = video.volume === 0;
                    volumeBtn.innerHTML = video.muted ?
                        '<i class="fas fa-volume-mute"></i>' :
                        '<i class="fas fa-volume-up"></i>';
                });
            }

            // Progress bar control
            progressBar.addEventListener('click', (e) => {
                const rect = progressBar.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const percent = x / rect.width;
                video.currentTime = percent * video.duration;
            });

            // Fullscreen control
            fullscreenBtn.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    container.requestFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                } else {
                    document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                }
            });

            // Mobile touch controls
            if (isMobile) {
                container.addEventListener('touchstart', (e) => {
                    const currentTime = new Date().getTime();

                    if (!isControlsVisible) {
                        showControls();
                    } else if (!e.target.closest('.video-controls')) {
                        // If controls are visible and touch is not on controls
                        if (currentTime - lastTouchTime < 300) {
                            // Double tap - toggle fullscreen
                            if (!document.fullscreenElement) {
                                container.requestFullscreen();
                            } else {
                                document.exitFullscreen();
                            }
                        } else {
                            // Single tap - toggle play/pause
                            if (video.paused) {
                                playeff.classList.remove('hidden');
                                stopeff.classList.add('hidden');
                                setTimeout(() => {
                                    playeff.classList.add('hidden');
                                }, 500);
                                video.play();
                            } else {
                                playeff.classList.add('hidden');
                                stopeff.classList.remove('hidden');
                                setTimeout(() => {
                                    stopeff.classList.add('hidden');
                                }, 500);
                                video.pause();
                            }
                        }
                    }

                    lastTouchTime = currentTime;
                });
            } else {
                // Desktop hover controls
                container.addEventListener('mousemove', showControls);
                container.addEventListener('mouseleave', () => {
                    if (!video.paused) {
                        hideControls();
                    }
                });

                // Add click to play/pause for desktop
                video.addEventListener('click', (e) => {
                    if (!e.target.closest('.video-controls')) {
                        if (video.paused) {

                            video.play();
                        } else {
                            video.pause();
                        }
                    }
                });
            }

            // Save volume state
            function saveVolumeState() {
                localStorage.setItem('videoVolume', video.volume);
                localStorage.setItem('videoMuted', video.muted);
            }

            // Load saved volume state
            const savedVolume = localStorage.getItem('videoVolume');
            const savedMuted = localStorage.getItem('videoMuted');

            if (savedVolume !== null) {
                video.volume = parseFloat(savedVolume);
                volumeLevel.style.width = `${video.volume * 100}%`;
            }

            if (savedMuted === 'true') {
                video.muted = true;
                volumeBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                volumeLevel.style.width = '0%';
            }

            video.addEventListener('volumechange', saveVolumeState);

            // Handle window resize
            window.addEventListener('resize', () => {
                isMobile = window.innerWidth <= 991;
            });
        });

        function copyShareLink() {
            const url = window.location.href;

            const tempInput = document.createElement('input');
            tempInput.style.position = 'absolute';
            tempInput.style.left = '-9999px';
            tempInput.value = url;
            document.body.appendChild(tempInput);

            tempInput.select();
            document.execCommand('copy');

            document.body.removeChild(tempInput);

            alert('Share URL copied to clipboard!');
        }
    </script>
@endsection
