<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Player</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: black;
        }

        .video-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            background: black;
        }

        .video-player {
            width: 100%;
            height: 100%;
            object-fit: contain;
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

        .progress-container {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            cursor: pointer;
            position: relative;
        }

        .buffer-bar {
            position: absolute;
            height: 100%;
            background: rgba(255, 255, 255, 0.4);
            transition: width 0.1s ease-in-out;
        }

        .progress {
            position: absolute;
            height: 100%;
            background: #3b82f6;
            transition: width 0.1s ease-in-out;
        }

        .controls-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
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

        .time-display {
            color: white;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        .loading-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.5);
            z-index: 20;
        }

        .loading-container.hidden {
            display: none;
        }

        .loading-spinner {
            color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (max-width: 991px) {
            .volume-control {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="video-container">
        <video class="video-player" playsinline
            @if ($file->has_thumbnail==1) poster="{{ URL() }}/thumnail/{{ $fileName }}" @endif
            preload="metadata">
            Your browser does not support the video tag.
        </video>

        <div class="loading-container hidden">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-3x"></i>
            </div>
        </div>

        <div class="video-controls">
            <div class="progress-container">
                <div class="buffer-bar"></div>
                <div class="progress"></div>
            </div>
            <div class="controls-row">
                <button class="control-button play-pause">
                    <i class="fas fa-play"></i>
                </button>
                <div class="volume-control">
                    <button class="control-button volume">
                        <i class="fas fa-volume-up"></i>
                    </button>
                    <input type="range" class="volume-slider" min="0" max="1" step="0.1" value="1">
                </div>
                <span class="time-display">
                    <span class="current-time">0:00</span>
                    /
                    <span class="duration">0:00</span>
                </span>
                <div style="flex-grow: 1"></div>
                <button class="control-button fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.video-container');
            const video = container.querySelector('.video-player');
            const controls = container.querySelector('.video-controls');
            const progressContainer = container.querySelector('.progress-container');
            const bufferBar = container.querySelector('.buffer-bar');
            const progress = container.querySelector('.progress');
            const playPauseBtn = container.querySelector('.play-pause');
            const volumeBtn = container.querySelector('.volume');
            const volumeSlider = container.querySelector('.volume-slider');
            const currentTimeDisplay = container.querySelector('.current-time');
            const durationDisplay = container.querySelector('.duration');
            const fullscreenBtn = container.querySelector('.fullscreen');
            const loadingContainer = container.querySelector('.loading-container');

            let controlsTimeout;
            let isControlsVisible = true;
            let isMobile = window.innerWidth <= 991;

            // Set video source
            video.src = "{{ URL() }}/stream/{{ $fileName }}/{{ $fileExtension }}";

            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                seconds = Math.floor(seconds % 60);
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }

            function showLoading() {
                loadingContainer.classList.remove('hidden');
            }

            function hideLoading() {
                loadingContainer.classList.add('hidden');
            }

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
                if (!video.paused && !isMobile) {
                    controlsTimeout = setTimeout(hideControls, 3000);
                }
            }

            // Loading events
            video.addEventListener('loadstart', showLoading);
            video.addEventListener('waiting', showLoading);
            video.addEventListener('canplay', hideLoading);
            video.addEventListener('playing', hideLoading);

            // Play/Pause
            function togglePlayPause() {
                if (video.paused) {
                    video.play().then(() => {
                        playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                        startControlsTimer();
                    }).catch(error => {
                        console.error('Error playing video:', error);
                    });
                } else {
                    video.pause();
                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            }

            playPauseBtn.addEventListener('click', togglePlayPause);

            // Click video to play/pause
            video.addEventListener('click', (e) => {
                if (e.target.closest('.video-controls')) return;
                
                if (isMobile) {
                    if (!isControlsVisible) {
                        showControls();
                    } else {
                        togglePlayPause();
                    }
                } else {
                    togglePlayPause();
                }
            });

            // Progress bar
            function updateProgress() {
                const percent = (video.currentTime / video.duration) * 100;
                progress.style.width = `${percent}%`;
                currentTimeDisplay.textContent = formatTime(video.currentTime);
            }

            function updateBuffer() {
                if (video.buffered.length > 0) {
                    const bufferedEnd = video.buffered.end(video.buffered.length - 1);
                    const duration = video.duration;
                    const width = (bufferedEnd / duration) * 100;
                    bufferBar.style.width = `${width}%`;
                }
            }

            video.addEventListener('timeupdate', updateProgress);
            video.addEventListener('progress', updateBuffer);

            // Seek
            progressContainer.addEventListener('click', (e) => {
                const rect = progressContainer.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                video.currentTime = pos * video.duration;
            });

            // Volume
            function updateVolume() {
                video.volume = volumeSlider.value;
                volumeBtn.innerHTML = video.volume === 0 
                    ? '<i class="fas fa-volume-mute"></i>' 
                    : '<i class="fas fa-volume-up"></i>';
            }

            volumeSlider.addEventListener('input', updateVolume);
            volumeBtn.addEventListener('click', () => {
                video.volume = video.volume === 0 ? 1 : 0;
                volumeSlider.value = video.volume;
                updateVolume();
            });

            // Fullscreen
            function toggleFullscreen() {
                if (!document.fullscreenElement) {
                    container.requestFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                } else {
                    document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                }
            }

            fullscreenBtn.addEventListener('click', toggleFullscreen);
            video.addEventListener('dblclick', (e) => {
                if (e.target.closest('.video-controls')) return;
                toggleFullscreen();
            });

            // Controls visibility
            container.addEventListener('mousemove', () => {
                if (!isMobile) {
                    showControls();
                }
            });

            container.addEventListener('mouseleave', () => {
                if (!isMobile && !video.paused) {
                    hideControls();
                }
            });

            // Metadata loaded
            video.addEventListener('loadedmetadata', () => {
                durationDisplay.textContent = formatTime(video.duration);
            });

            // Cleanup
            window.addEventListener('unload', () => {
                video.src = '';
                video.load();
            });
        });
    </script>
</body>
</html>
