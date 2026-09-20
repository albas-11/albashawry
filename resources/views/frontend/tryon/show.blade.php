@extends('layouts.app')

@section('title', __('Virtual Try-On') . ' - ' . $product->getTranslation('name', app()->getLocale()))

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('products.show', $product->seo_url) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to Product') }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-4">
                {{ __('Virtual Try-On') }}: {{ $product->getTranslation('name', app()->getLocale()) }}
            </h1>
            <p class="text-gray-600 mt-2">{{ __('Try on glasses virtually using your camera') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Main Try-On Area -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">

                    <!-- Video/Canvas Container -->
                    <div id="tryon-container" class="relative bg-gray-900 aspect-video">

                        <!-- Loading State -->
                        <div id="loading-state" class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center text-white">
                                <svg class="animate-spin h-12 w-12 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-lg">{{ __('Initializing camera...') }}</p>
                            </div>
                        </div>

                        <!-- Error State -->
                        <div id="error-state" class="absolute inset-0 hidden flex items-center justify-center">
                            <div class="text-center text-white px-4">
                                <svg class="w-16 h-16 mx-auto mb-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <h3 class="text-xl font-bold mb-2">{{ __('Camera Access Required') }}</h3>
                                <p id="error-message" class="text-gray-300 mb-4"></p>
                                <button onclick="initTryOn()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                    {{ __('Try Again') }}
                                </button>
                            </div>
                        </div>

                        <!-- Video Element (Hidden) -->
                        <video id="video" autoplay playsinline class="hidden"></video>

                        <!-- Canvas for Rendering -->
                        <canvas id="canvas" class="w-full h-full"></canvas>

                        <!-- Face Detection Indicator -->
                        <div id="detection-indicator" class="absolute top-4 left-4 hidden">
                            <div class="bg-green-500 text-white px-4 py-2 rounded-full text-sm flex items-center">
                                <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                                {{ __('Face Detected') }}
                            </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="p-6 bg-gray-50 border-t">
                        <div class="flex flex-wrap gap-3">

                            <button id="capture-btn" onclick="capturePhoto()" disabled class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold transition disabled:cursor-not-allowed">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('Capture Photo') }}
                            </button>

                            <button id="switch-camera-btn" onclick="switchCamera()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>

                            <button onclick="stopTryOn()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Instructions -->
                        <div class="mt-4 text-sm text-gray-600">
                            <p>💡 {{ __('Tips') }}:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>{{ __('Make sure your face is well-lit and clearly visible') }}</li>
                                <li>{{ __('Look directly at the camera') }}</li>
                                <li>{{ __('Remove any existing glasses for best results') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Captured Photos -->
                <div id="captured-photos" class="mt-6 hidden">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('Captured Photos') }}</h3>
                    <div id="photo-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4"></div>
                </div>
            </div>

            <!-- Product Info Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">

                    <!-- Product Image -->
                    @if($product->images->first())
                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                         alt="{{ $product->getTranslation('name', app()->getLocale()) }}"
                         class="w-full h-48 object-cover rounded-lg mb-4">
                    @endif

                    <!-- Product Details -->
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        {{ $product->getTranslation('name', app()->getLocale()) }}
                    </h3>

                    <p class="text-gray-600 mb-4">
                        {{ $product->brand->getTranslation('name', app()->getLocale()) }}
                    </p>

                    <!-- Price -->
                    <div class="mb-6">
                        @if($product->discount_price)
                            <span class="text-2xl font-bold text-red-600">
                                {{ number_format($product->discount_price, 2) }} {{ __('MAD') }}
                            </span>
                            <span class="text-lg text-gray-500 line-through ml-2">
                                {{ number_format($product->price, 2) }} {{ __('MAD') }}
                            </span>
                        @else
                            <span class="text-2xl font-bold text-gray-900">
                                {{ number_format($product->price, 2) }} {{ __('MAD') }}
                            </span>
                        @endif
                    </div>

                    <!-- Specifications -->
                    @if($product->frame_shape || $product->frame_material)
                    <div class="border-t pt-4 mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">{{ __('Specifications') }}</h4>
                        <div class="space-y-2 text-sm">
                            @if($product->frame_shape)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Shape') }}:</span>
                                <span class="font-medium">{{ ucfirst($product->frame_shape) }}</span>
                            </div>
                            @endif
                            @if($product->frame_material)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Material') }}:</span>
                                <span class="font-medium">{{ ucfirst($product->frame_material) }}</span>
                            </div>
                            @endif
                            @if($product->gender)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Gender') }}:</span>
                                <span class="font-medium">{{ ucfirst($product->gender) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="space-y-3">
                        <a href="{{ route('products.show', $product->seo_url) }}"
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-6 py-3 rounded-lg font-semibold transition">
                            {{ __('View Product Details') }}
                        </a>

                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                                {{ __('Add to Cart') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Face-API.js -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/face-api.js/0.22.2/face-api.min.js"></script>

<script>
    let video, canvas, ctx;
    let stream = null;
    let glassesImg = null;
    let tryonData = null;
    let detectionInterval = null;
    let facingMode = 'user'; // 'user' for front camera, 'environment' for back camera
    let capturedPhotos = [];

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initTryOn();
    });

    async function initTryOn() {
        try {
            // Hide error, show loading
            document.getElementById('error-state').classList.add('hidden');
            document.getElementById('loading-state').classList.remove('hidden');

            // Get elements
            video = document.getElementById('video');
            canvas = document.getElementById('canvas');
            ctx = canvas.getContext('2d');

            // Load Face-API models
            await loadFaceAPIModels();

            // Load product try-on data
            await loadTryOnData();

            // Load glasses image
            await loadGlassesImage();

            // Start camera
            await startCamera();

            // Start detection
            startDetection();

            // Hide loading
            document.getElementById('loading-state').classList.add('hidden');

        } catch (error) {
            console.error('Initialization error:', error);
            showError(error.message);
        }
    }

    async function loadFaceAPIModels() {
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';

        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    }

    async function loadTryOnData() {
        const productId = {{ $product->id }};
        const response = await fetch(`/tryon/data/${productId}`);

        if (!response.ok) {
            throw new Error('{{ __("Failed to load try-on data") }}');
        }

        const data = await response.json();
        tryonData = data.tryon;
    }

    async function loadGlassesImage() {
        return new Promise((resolve, reject) => {
            glassesImg = new Image();
            glassesImg.crossOrigin = 'anonymous';
            glassesImg.onload = resolve;
            glassesImg.onerror = () => reject(new Error('{{ __("Failed to load glasses image") }}'));
            glassesImg.src = tryonData.file_path;
        });
    }

    async function startCamera() {
        const constraints = {
            video: {
                facingMode: facingMode,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };

        try {
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = stream;

            return new Promise((resolve) => {
                video.onloadedmetadata = () => {
                    video.play();
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    resolve();
                };
            });
        } catch (error) {
            throw new Error('{{ __("Camera access denied. Please allow camera access to use virtual try-on.") }}');
        }
    }

    function startDetection() {
        let lastDetection = null;

        detectionInterval = setInterval(async () => {
            if (video.readyState !== video.HAVE_ENOUGH_DATA) return;

            try {
                const detection = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks();

                // Clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Draw video frame
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                if (detection) {
                    lastDetection = detection;

                    // Show detection indicator
                    document.getElementById('detection-indicator').classList.remove('hidden');
                    document.getElementById('capture-btn').disabled = false;

                    // Draw glasses
                    drawGlasses(detection);
                } else {
                    // Hide detection indicator
                    document.getElementById('detection-indicator').classList.add('hidden');
                    document.getElementById('capture-btn').disabled = true;
                }

            } catch (error) {
                console.error('Detection error:', error);
            }
        }, 100); // Update every 100ms
    }

    function drawGlasses(detection) {
        const landmarks = detection.landmarks;

        // Get eye positions
        const leftEye = landmarks.getLeftEye();
        const rightEye = landmarks.getRightEye();

        // Calculate center between eyes
        const leftEyeCenter = getCenter(leftEye);
        const rightEyeCenter = getCenter(rightEye);

        // Calculate glasses position
        const eyeDistance = Math.sqrt(
            Math.pow(rightEyeCenter.x - leftEyeCenter.x, 2) +
            Math.pow(rightEyeCenter.y - leftEyeCenter.y, 2)
        );

        // Calculate angle
        const angle = Math.atan2(
            rightEyeCenter.y - leftEyeCenter.y,
            rightEyeCenter.x - leftEyeCenter.x
        );

        // Calculate glasses dimensions
        const glassesWidth = eyeDistance * tryonData.scale * 2.5;
        const glassesHeight = glassesImg.height * (glassesWidth / glassesImg.width);

        // Calculate position
        const centerX = (leftEyeCenter.x + rightEyeCenter.x) / 2;
        const centerY = (leftEyeCenter.y + rightEyeCenter.y) / 2;

        // Apply offsets
        const x = centerX - glassesWidth / 2 + tryonData.offset_x;
        const y = centerY - glassesHeight / 2 + tryonData.offset_y;

        // Save context
        ctx.save();

        // Rotate around center
        ctx.translate(centerX, centerY);
        ctx.rotate(angle + (tryonData.rotation * Math.PI / 180));
        ctx.translate(-centerX, -centerY);

        // Draw glasses
        ctx.drawImage(glassesImg, x, y, glassesWidth, glassesHeight);

        // Restore context
        ctx.restore();
    }

    function getCenter(points) {
        const sum = points.reduce((acc, point) => ({
            x: acc.x + point.x,
            y: acc.y + point.y
        }), { x: 0, y: 0 });

        return {
            x: sum.x / points.length,
            y: sum.y / points.length
        };
    }

    function capturePhoto() {
        // Create a new canvas for the photo
        const photoCanvas = document.createElement('canvas');
        photoCanvas.width = canvas.width;
        photoCanvas.height = canvas.height;
        const photoCtx = photoCanvas.getContext('2d');

        // Copy current frame
        photoCtx.drawImage(canvas, 0, 0);

        // Convert to data URL
        const photoURL = photoCanvas.toDataURL('image/png');
        capturedPhotos.push(photoURL);

        // Display photo
        displayCapturedPhoto(photoURL);

        // Show flash effect
        showFlashEffect();
    }

    function displayCapturedPhoto(photoURL) {
        const photoGrid = document.getElementById('photo-grid');
        const capturedSection = document.getElementById('captured-photos');

        capturedSection.classList.remove('hidden');

        const photoDiv = document.createElement('div');
        photoDiv.className = 'relative group';
        photoDiv.innerHTML = `
            <img src="${photoURL}" class="w-full h-auto rounded-lg shadow-lg">
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                <button onclick="downloadPhoto('${photoURL}')" class="bg-white text-gray-900 px-4 py-2 rounded-lg mr-2">
                    {{ __('Download') }}
                </button>
                <button onclick="deletePhoto(this)" class="bg-red-600 text-white px-4 py-2 rounded-lg">
                    {{ __('Delete') }}
                </button>
            </div>
        `;

        photoGrid.appendChild(photoDiv);
    }

    function downloadPhoto(photoURL) {
        const link = document.createElement('a');
        link.download = `tryon-${Date.now()}.png`;
        link.href = photoURL;
        link.click();
    }

    function deletePhoto(button) {
        const photoDiv = button.closest('.relative');
        photoDiv.remove();

        if (document.getElementById('photo-grid').children.length === 0) {
            document.getElementById('captured-photos').classList.add('hidden');
        }
    }

    function showFlashEffect() {
        const flash = document.createElement('div');
        flash.className = 'fixed inset-0 bg-white pointer-events-none z-50';
        flash.style.opacity = '0.8';
        document.body.appendChild(flash);

        setTimeout(() => {
            flash.style.transition = 'opacity 0.3s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 300);
        }, 100);
    }

    async function switchCamera() {
        facingMode = facingMode === 'user' ? 'environment' : 'user';

        stopCamera();
        await startCamera();
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    function stopTryOn() {
        if (detectionInterval) {
            clearInterval(detectionInterval);
            detectionInterval = null;
        }

        stopCamera();

        window.location.href = '{{ route("products.show", $product->seo_url) }}';
    }

    function showError(message) {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('error-state').classList.remove('hidden');
        document.getElementById('error-message').textContent = message;
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        stopCamera();
        if (detectionInterval) clearInterval(detectionInterval);
    });
</script>
@endsection
