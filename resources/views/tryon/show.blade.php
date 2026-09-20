@extends('layouts.app')

@section('title', 'Virtual Try-On • ' . $product->name)
<style>
    @keyframes spin-reverse {
        from {
            transform: rotate(360deg);
        }

        to {
            transform: rotate(0deg);
        }
    }

    .animate-spin-reverse {
        animation: spin-reverse 1.5s linear infinite;
    }
</style>
@section('content')
    <div class="max-w-5xl mx-auto py-12 px-6">

        <div class="mb-12 text-center">
            <span class="text-[10px] font-bold uppercase tracking-[0.4em] text-gray-400 mb-2 block">@lang('try.AI Innovation')</span>
            <h1 class="text-4xl tracking-tight text-gray-900 uppercase mb-3">
                @lang('try.virtual_mirror')

            </h1>
            <p class="text-gray-500 font-serif italic">{{ $product->brand->name }} — {{ $product->name }}</p>
        </div>

        <div id="tryon-container" data-product-id="{{ $product->id }}"
            class="relative bg-black rounded-[2.5rem] overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] border border-gray-100">

            <div id="tryon-loader"
                class="absolute inset-0 bg-black/60 backdrop-blur-xl flex items-center justify-center z-20 transition-all duration-700">
                <div class="text-center">
                    <div class="relative w-20 h-20 mx-auto mb-8">
                        <div class="absolute inset-0 border-t-2 border-white rounded-full animate-spin"></div>
                        <div class="absolute inset-2 border-r-2 border-white/20 rounded-full animate-spin-reverse"></div>
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-white">@lang('try.Initializing Lens...') </p>
                </div>
            </div>

            <div class="relative w-full aspect-[4/3] md:aspect-video bg-gray-950">
                <video id="video" autoplay playsinline class="hidden"></video>
                <canvas id="canvas" class="w-full h-full object-cover"></canvas>

                <div class="absolute top-8 right-8 flex items-center gap-3">
                    <div class="px-3 py-1 bg-black/40 backdrop-blur-md border border-white/10 rounded-full">
                        <span class="text-[9px] text-white font-bold uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                            @lang('try.Virtual Preview')
                        </span>
                    </div>
                </div>

            </div>

            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 w-full max-w-lg px-6 flex justify-center">
                <div
                    class="bg-white/90 backdrop-blur-2xl p-2 rounded-[2rem] shadow-2xl flex items-center gap-2 border border-white/50">

                    <button id="capture-btn"
                        class="flex items-center gap-3 px-8 py-4 bg-[#2E3192] text-white rounded-[1.5rem] text-[10px] font-bold uppercase tracking-widest hover:bg-[#1e2060] transition-all duration-300 shadow-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        @lang('try.Capture')
                    </button>

                    <div class="h-8 w-px bg-gray-200 mx-2"></div>

                    <button id="stop-btn" class="p-4 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-16 grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-4 mb-10">
                    <h3 class="text-[11px] font-bold uppercase tracking-[0.4em] text-black whitespace-nowrap">
                        @lang('try.Usage Protocol')
                    </h3>
                    <div class="h-[1px] w-full bg-gradient-to-r from-gray-200 to-transparent"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10">
                    @php
                        $steps = [
                            ['icon' => 'sun', 'title' => 'Lighting', 'text' => 'Natural light ensures frame accuracy'],
                            [
                                'icon' => 'expand',
                                'title' => 'Alignment',
                                'text' => 'Keep your face centered in the mirror',
                            ],
                            ['icon' => 'video', 'title' => 'Motion', 'text' => 'Rotate slowly for side profile check'],
                            [
                                'icon' => 'camera',
                                'title' => 'Archive',
                                'text' => 'Capture to compare with other styles',
                            ],
                        ];
                    @endphp

                    @foreach ($steps as $step)
                        <div class="group flex flex-col gap-3">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white flex items-center justify-center flex-shrink-0 text-black border border-gray-100 shadow-sm group-hover:bg-black group-hover:text-white transition-all duration-500">
                                    <i class="fas fa-{{ $step['icon'] }} text-[11px]"></i>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-900">
                                    @lang('try.' . $step['title'])
                                </span>
                            </div>

                            <p class="text-[11px] text-gray-400 uppercase tracking-widest leading-relaxed pl-14">
                                @lang('try.' . $step['text'])
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gray-50 p-6 md:p-8 rounded-[2rem] space-y-8 text-center">
                <div class="space-y-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                        @lang('try.Live Calibration')
                    </p>

                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" id="debug-toggle" class="sr-only peer">

                        <div
                            class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full
                peer peer-checked:after:translate-x-full
                after:content-[''] after:absolute after:top-[4px]
                after:ltr:left-[4px] after:rtl:right-[4px]
                after:bg-white after:rounded-full after:h-5 after:w-5
                after:transition-all peer-checked:bg-black">
                        </div>

                        <span class="ltr:ml-3 rtl:mr-3 text-[10px] font-bold uppercase tracking-widest text-gray-900">
                            @lang('try.Debug Mode')
                        </span>
                    </label>
                </div>

                <div class="pt-4">
                    <a href="{{ route('products.show', $product->seo_url) }}"
                        class="block w-full py-4 text-[10px] text-[#2E3192]
           font-bold uppercase tracking-[0.2em]
           border border-[#2E3192]
           hover:bg-[#2E3192] hover:text-white
           transition-all rounded-xl">
                        @lang('try.Exit Studio')
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Load Face-API.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <!-- Virtual Try-On Script -->
    <script>
        // VIRTUAL TRY-ON IMPLEMENTATION
        class VirtualTryOn {
            constructor() {
                this.video = null;
                this.canvas = null;
                this.ctx = null;
                this.stream = null;
                this.glassesImage = null;
                this.calibration = {
                    offset_x: 0,
                    offset_y: 0,
                    scale: 1,
                    rotation: 0
                };
                this.isLoaded = false;
                this.animationId = null;
            }

            // Load Face-API Models
            async loadModels() {
                const MODEL_URL = '/models/face-api';

                try {
                    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                    console.log('@lang('try.models_loaded')');
                    this.isLoaded = true;
                    return true;
                } catch (error) {
                    console.error('@lang('try.models_error')', error);
                    return false;
                }
            }

            // Start Camera
            async startCamera() {
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            },
                            facingMode: 'user'
                        }
                    });

                    this.video = document.getElementById('video');
                    this.video.srcObject = this.stream;

                    return new Promise(resolve => {
                        this.video.onloadedmetadata = () => {
                            this.video.play();
                            this.setupCanvas();
                            resolve(true);
                        };
                    });
                } catch (error) {
                    console.error('@lang('try.camera_error')', error);
                    alert('@lang('try.camera_permission')');
                    return false;
                }
            }

            // Setup Canvas
            setupCanvas() {
                this.canvas = document.getElementById('canvas');
                this.canvas.width = this.video.videoWidth;
                this.canvas.height = this.video.videoHeight;
                this.ctx = this.canvas.getContext('2d');
                console.log('@lang('try.canvas_setup')', this.canvas.width, 'x', this.canvas.height);
            }

            // Load Glasses Image
            async loadGlasses(imagePath, calibrationData) {
                return new Promise((resolve, reject) => {
                    this.glassesImage = new Image();
                    this.glassesImage.crossOrigin = 'anonymous';

                    this.glassesImage.onload = () => {
                        this.calibration = {
                            offset_x: parseFloat(calibrationData.offset_x || 0),
                            offset_y: parseFloat(calibrationData.offset_y || 0),
                            scale: parseFloat(calibrationData.scale || 1),
                            rotation: parseFloat(calibrationData.rotation || 0)
                        };
                        console.log('@lang('try.glasses_loaded')', imagePath);
                        console.log('@lang('try.calibration_data')', this.calibration);
                        resolve(true);
                    };

                    this.glassesImage.onerror = (err) => {
                        console.error('@lang('try.glasses_load_error')', err);
                        reject(new Error('@lang('try.glasses_failed')'));
                    };

                    this.glassesImage.src = encodeURI(imagePath);
                });
            }

            // Get Eye Center
            getEyeCenter(eyePoints) {
                const x = eyePoints.reduce((sum, p) => sum + p.x, 0) / eyePoints.length;
                const y = eyePoints.reduce((sum, p) => sum + p.y, 0) / eyePoints.length;
                return {
                    x,
                    y
                };
            }

            // Calculate Glasses Position
            calculateGlassesPosition(landmarks) {
                const leftEye = landmarks.getLeftEye();
                const rightEye = landmarks.getRightEye();

                const leftCenter = this.getEyeCenter(leftEye);
                const rightCenter = this.getEyeCenter(rightEye);

                // Distance between eyes
                const eyeDistance = Math.hypot(
                    rightCenter.x - leftCenter.x,
                    rightCenter.y - leftCenter.y
                );

                // Glasses dimensions
                const width = eyeDistance * 2.5 * this.calibration.scale;
                const height = (this.glassesImage.height / this.glassesImage.width) * width;

                // Center position
                const centerX = (leftCenter.x + rightCenter.x) / 2;
                const centerY = (leftCenter.y + rightCenter.y) / 2;

                // Apply offsets
                const x = centerX - width / 2 + (this.calibration.offset_x * eyeDistance / 100);
                const y = centerY - height / 2 + (this.calibration.offset_y * eyeDistance / 100);

                // Calculate angle
                const angle = Math.atan2(
                    rightCenter.y - leftCenter.y,
                    rightCenter.x - leftCenter.x
                ) + (this.calibration.rotation * Math.PI / 180);

                return {
                    x,
                    y,
                    width,
                    height,
                    angle,
                    centerX,
                    centerY
                };
            }

            // Draw Glasses
            drawGlasses(position) {
                this.ctx.save();
                this.ctx.translate(position.centerX, position.centerY);
                this.ctx.rotate(position.angle);
                this.ctx.drawImage(
                    this.glassesImage,
                    -position.width / 2,
                    -position.height / 2,
                    position.width,
                    position.height
                );
                this.ctx.restore();
            }

            // Main Detection Loop
            async detect() {
                if (!this.video || !this.canvas || !this.isLoaded) return;

                const detections = await faceapi
                    .detectSingleFace(this.video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks();

                // Clear and redraw
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);

                if (detections && this.glassesImage) {
                    const position = this.calculateGlassesPosition(detections.landmarks);
                    this.drawGlasses(position);

                    // Debug mode
                    if (window.debugMode) {
                        faceapi.draw.drawFaceLandmarks(this.canvas, detections);
                    }
                } else {
                    // No face detected message
                    this.ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
                    this.ctx.fillRect(20, 20, 400, 50);
                    this.ctx.fillStyle = '#fff';
                    this.ctx.font = 'bold 18px Arial';
                    this.ctx.fillText('@lang('try.position_face')', 30, 50);
                }

                // Continue loop
                this.animationId = requestAnimationFrame(() => this.detect());
            }

            // Start Everything
            async start(glassesPath, calibrationData) {
                console.log('@lang('try.starting')');

                // 1. Load models
                if (!this.isLoaded) {
                    const loaded = await this.loadModels();
                    if (!loaded) throw new Error('@lang('try.models_failed')');
                }

                // 2. Load glasses
                await this.loadGlasses(glassesPath, calibrationData);

                // 3. Start camera
                const cameraStarted = await this.startCamera();
                if (!cameraStarted) throw new Error('@lang('try.camera_failed')');

                // 4. Start detection
                console.log('@lang('try.starting_detection')');
                this.detect();
            }

            // Stop
            stop() {
                if (this.animationId) {
                    cancelAnimationFrame(this.animationId);
                }
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                }
                if (this.ctx) {
                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                }
            }

            // Capture Photo
            capturePhoto() {
                const link = document.createElement('a');
                link.download = `virtual-tryon-${Date.now()}.png`;
                link.href = this.canvas.toDataURL('image/png');
                link.click();

                // Show success message
                alert('@lang('try.photo_captured')');
            }
        }

        // ============================================
        // AUTO-INITIALIZATION
        // ============================================

        document.addEventListener('DOMContentLoaded', async () => {
            const container = document.getElementById('tryon-container');
            if (!container) return;

            const productId = container.dataset.productId;
            const tryonInstance = new VirtualTryOn();

            const loader = document.getElementById('tryon-loader');

            try {
                // Fetch product try-on data
                console.log('@lang('try.fetching_data')', productId);

                const response = await fetch(`/tryon/data/${productId}`);
                const data = await response.json();

                if (data.success) {
                    await tryonInstance.start(
                        data.tryon.file_path,
                        data.tryon
                    );
                }

                console.log('@lang('try.data_received')', data.data);

                // Start try-on
                await tryonInstance.start(data.tryon.file_path, data.tryon);

                // Hide loader
                if (loader) loader.classList.add('hidden');

                // Setup buttons
                const captureBtn = document.getElementById('capture-btn');
                if (captureBtn) {
                    captureBtn.addEventListener('click', () => {
                        tryonInstance.capturePhoto();
                    });
                }

                const stopBtn = document.getElementById('stop-btn');
                if (stopBtn) {
                    stopBtn.addEventListener('click', () => {
                        tryonInstance.stop();
                        window.location.href = `/products/${productId}`;
                    });
                }

                const debugToggle = document.getElementById('debug-toggle');
                if (debugToggle) {
                    debugToggle.addEventListener('change', (e) => {
                        window.debugMode = e.target.checked;
                    });
                }

                console.log('@lang('try.initialized_success')');

            } catch (error) {
                console.error('@lang('try.init_error')', error);

                // Show error message to user
                if (loader) {
                    loader.innerHTML = `<div class="error-message">
                    <p>@lang('try.general_error')</p>
                </div>`;
                }
            }
        });
    </script>

    <style>
        /* Loading animation */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Smooth transitions */
        #tryon-container {
            transition: all 0.3s ease;
        }

        /* Canvas styling */
        #canvas {
            background: #000;
        }
    </style>
@endsection
