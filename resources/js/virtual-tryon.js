// ============================================
// VIRTUAL TRY-ON

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
            console.log('✅ Face API models loaded');
            this.isLoaded = true;
            return true;
        } catch (error) {
            console.error('❌ Error loading models:', error);
            throw new Error(`Model loading failed: ${error.message}`);
        }
    }

    // Start Camera
    async startCamera() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
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
            console.error('❌ Camera error:', error);
            throw new Error('Camera access denied. Please allow camera permissions.');
        }
    }

    // Setup Canvas
    setupCanvas() {
        this.canvas = document.getElementById('canvas');
        this.canvas.width = this.video.videoWidth;
        this.canvas.height = this.video.videoHeight;
        this.ctx = this.canvas.getContext('2d');
        console.log('✅ Canvas setup:', this.canvas.width, 'x', this.canvas.height);
    }

    // Load Glasses Image
    async loadGlasses(imagePath, calibrationData) {
        console.log('🔄 Loading glasses from:', imagePath);

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
                console.log('✅ Glasses loaded successfully');
                console.log('✅ Image dimensions:', this.glassesImage.width, 'x', this.glassesImage.height);
                console.log('✅ Calibration:', this.calibration);
                resolve(true);
            };

            this.glassesImage.onerror = (err) => {
                console.error('❌ Failed to load glasses image');
                console.error('❌ Image path:', imagePath);
                console.error('❌ Error details:', err);
                reject(new Error(`Failed to load glasses image from: ${imagePath}`));
            };

            // Essayer de charger l'image
            this.glassesImage.src = imagePath;
        });
    }

    // Get Eye Center
    getEyeCenter(eyePoints) {
        const x = eyePoints.reduce((sum, p) => sum + p.x, 0) / eyePoints.length;
        const y = eyePoints.reduce((sum, p) => sum + p.y, 0) / eyePoints.length;
        return { x, y };
    }

    // Calculate Glasses Position
    calculateGlassesPosition(landmarks) {
        const leftEye = landmarks.getLeftEye();
        const rightEye = landmarks.getRightEye();

        const leftCenter = this.getEyeCenter(leftEye);
        const rightCenter = this.getEyeCenter(rightEye);

        const eyeDistance = Math.hypot(
            rightCenter.x - leftCenter.x,
            rightCenter.y - leftCenter.y
        );

        const width = eyeDistance * 2.5 * this.calibration.scale;
        const height = (this.glassesImage.height / this.glassesImage.width) * width;

        const centerX = (leftCenter.x + rightCenter.x) / 2;
        const centerY = (leftCenter.y + rightCenter.y) / 2;

        const x = centerX - width / 2 + (this.calibration.offset_x * eyeDistance / 100);
        const y = centerY - height / 2 + (this.calibration.offset_y * eyeDistance / 100);

        const angle = Math.atan2(
            rightCenter.y - leftCenter.y,
            rightCenter.x - leftCenter.x
        ) + (this.calibration.rotation * Math.PI / 180);

        return { x, y, width, height, angle, centerX, centerY };
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

        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);

        if (detections && this.glassesImage) {
            const position = this.calculateGlassesPosition(detections.landmarks);
            this.drawGlasses(position);

            if (window.debugMode) {
                faceapi.draw.drawFaceLandmarks(this.canvas, detections);
            }
        } else {
            this.ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
            this.ctx.fillRect(20, 20, 350, 50);
            this.ctx.fillStyle = '#fff';
            this.ctx.font = 'bold 18px Arial';
            this.ctx.fillText('😊 Position your face in front of the camera', 30, 50);
        }

        this.animationId = requestAnimationFrame(() => this.detect());
    }

    // Start Everything
    async start(glassesPath, calibrationData) {
        console.log('🚀 Starting Virtual Try-On...');
        console.log('📁 Glasses path:', glassesPath);

        // 1. Load models
        if (!this.isLoaded) {
            await this.loadModels();
        }

        // 2. Load glasses
        await this.loadGlasses(glassesPath, calibrationData);

        // 3. Start camera
        await this.startCamera();

        // 4. Start detection
        console.log('✅ Starting detection loop...');
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
        alert('📸 Photo captured successfully!');
    }
}

// ============================================
// AUTO-INITIALIZATION - VERSION CORRIGÉE
// ============================================

document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('tryon-container');
    if (!container) return;

    const productId = container.dataset.productId;
    const tryonInstance = new VirtualTryOn();
    const loader = document.getElementById('tryon-loader');

    try {
        // Fetch product try-on data
        console.log('📡 Fetching try-on data for product:', productId);

        const response = await fetch(`/tryon/data/${productId}`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('📦 Received data:', data);

        if (!data.success) {
            throw new Error('Try-on data not available for this product');
        }

        // CORRECTION: Construire le chemin d'image correct
        let glassesPath;
        let calibrationData;

        // Gérer différentes structures de réponse API
        if (data.tryon) {
            calibrationData = data.tryon;
        } else if (data.data) {
            calibrationData = data.data;
        } else {
            throw new Error('Invalid data structure: no tryon data found');
        }

        // Construire le chemin complet de l'image
        if (calibrationData.file_path) {
            const filename = calibrationData.file_path.split('/').pop(); // Extraire juste le nom du fichier
            glassesPath = `/images/tryon-files/${filename}`;
        } else {
            throw new Error('file_path not found in response data');
        }

        console.log('🖼️  Final glasses path:', glassesPath);
        console.log('📊 Calibration data:', calibrationData);

        // Start try-on (UN SEUL APPEL)
        await tryonInstance.start(glassesPath, calibrationData);

        // Hide loader
        if (loader) {
            loader.classList.add('hidden');
        }

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

        console.log('🎉 Virtual Try-On initialized successfully!');

    } catch (error) {
        console.error('❌ Try-on initialization error:', error);

        if (loader) {
            loader.innerHTML = `
                <div class="text-center text-white p-8">
                    <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
                    <h3 class="text-2xl font-bold mb-2">Failed to Initialize</h3>
                    <p class="text-gray-400 mb-4">${error.message}</p>
                    <p class="text-sm text-gray-500 mb-4">Please check console for details</p>
                    <a href="/products/${productId}"
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        Back to Product
                    </a>
                </div>
            `;
        }
    }
});
