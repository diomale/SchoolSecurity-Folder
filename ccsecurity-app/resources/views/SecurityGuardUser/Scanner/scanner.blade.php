<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner - Security Guard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">QR Scanner</h1>
            <a href="{{ route('security.dashboard') }}" class="text-blue-600 hover:text-blue-800 no-underline">Back to Dashboard</a>
        </div>
        
        <!-- Scanner Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <!-- Tabs -->
            <div class="flex mb-4 border-b">
                <button id="camera-tab" class="px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600" onclick="switchTab('camera')">
                    📷 Camera Scan
                </button>
                <button id="image-tab" class="px-4 py-2 font-semibold text-gray-600" onclick="switchTab('image')">
                    🖼️ Upload Image
                </button>
            </div>

            <!-- Camera View -->
            <div id="camera-view">
                <div id="reader" class="w-full mb-4 rounded-lg overflow-hidden bg-black"></div>
            </div>

            <!-- Image Upload View -->
            <div id="image-view" class="hidden">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center mb-4">
                    <input type="file" id="qr-image-input" accept="image/*" class="hidden" onchange="scanImageFile(event)">
                    <label for="qr-image-input" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Click to upload QR code image</p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                    </label>
                </div>
                <div id="image-preview" class="hidden mb-4">
                    <img id="preview-img" class="max-w-full h-auto rounded-lg border" style="max-height: 300px;">
                    <button onclick="clearImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">Clear Image</button>
                </div>
            </div>
            
            <!-- Scan Result -->
            <div id="scan-result" class="hidden mt-4 p-4 rounded-lg">
                <p class="text-lg font-semibold">Scanned QR Value: <span id="qr-value" class="text-blue-600"></span></p>
                <p id="scan-message" class="mt-2"></p>
            </div>
            
            <div id="scan-error" class="hidden mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <p id="error-message"></p>
            </div>
        </div>

        <!-- Scan History -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Scans</h2>
            <div id="scan-history" class="space-y-3">
                <!-- Scan history will be loaded here -->
            </div>
        </div>
    </div>

    <!-- html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
    let html5QrcodeScanner;
    let lastScanTime = null;
    let lastQrValue = null;
    const SCAN_COOLDOWN = 3000; // 3 seconds cooldown to prevent duplicate scans

    // Tab switching
    function switchTab(tab) {
        const cameraTab = document.getElementById('camera-tab');
        const imageTab = document.getElementById('image-tab');
        const cameraView = document.getElementById('camera-view');
        const imageView = document.getElementById('image-view');

        if (tab === 'camera') {
            cameraTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            cameraTab.classList.remove('text-gray-600');
            imageTab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            imageTab.classList.add('text-gray-600');
            cameraView.classList.remove('hidden');
            imageView.classList.add('hidden');
            
            // Resume camera
            if (!html5QrcodeScanner) {
                initCamera();
            }
        } else {
            imageTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            imageTab.classList.remove('text-gray-600');
            cameraTab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            cameraTab.classList.add('text-gray-600');
            imageView.classList.remove('hidden');
            cameraView.classList.add('hidden');
            
            // Pause camera to save resources
            if (html5QrcodeScanner) {
                html5QrcodeScanner.pause();
            }
        }
    }

    function initCamera() {
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };
        
        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Failed to start scanner", err);
            document.getElementById('reader').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <p>Failed to start camera. Please ensure camera permissions are granted.</p>
                    <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Retry
                    </button>
                </div>
            `;
        });
    }

    function scanImageFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Show preview
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        preview.classList.remove('hidden');
        previewImg.src = URL.createObjectURL(file);

        // Hide previous results
        document.getElementById('scan-result').classList.add('hidden');
        document.getElementById('scan-error').classList.add('hidden');

        // Scan the image
        const html5QrCode = new Html5Qrcode("reader");
        
        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                processScanResult(decodedText);
            })
            .catch(err => {
                console.error("Error scanning image", err);
                const errorDiv = document.getElementById('scan-error');
                errorDiv.classList.remove('hidden');
                document.getElementById('error-message').textContent = 'No QR code found in the image. Please try with a clearer image.';
                
                setTimeout(() => {
                    errorDiv.classList.add('hidden');
                }, 5000);
            });
    }

    function clearImage() {
        document.getElementById('qr-image-input').value = '';
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('preview-img').src = '';
    }

    function onScanSuccess(decodedText, decodedResult) {
        const currentTime = new Date().getTime();
        
        // Prevent duplicate scans within cooldown period
        if (lastScanTime && (currentTime - lastScanTime) < SCAN_COOLDOWN && lastQrValue === decodedText) {
            return;
        }
        
        lastScanTime = currentTime;
        lastQrValue = decodedText;

        processScanResult(decodedText);
    }

    function processScanResult(decodedText) {
        // Visual feedback
        const resultDiv = document.getElementById('scan-result');
        const errorDiv = document.getElementById('scan-error');
        const qrValueSpan = document.getElementById('qr-value');
        const scanMessage = document.getElementById('scan-message');
        
        resultDiv.classList.remove('hidden');
        resultDiv.classList.add('bg-green-100', 'border', 'border-green-400');
        errorDiv.classList.add('hidden');
        qrValueSpan.textContent = decodedText;
        scanMessage.textContent = 'Processing...';

        // Send to server
        fetch("{{ route('security.scan.qr') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_value: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.classList.remove('bg-green-100', 'border', 'border-green-400');
                resultDiv.classList.add('bg-blue-100', 'border', 'border-blue-400');
                scanMessage.textContent = `${data.message} - ${data.inside_user.fullname}`;
                
                // Add to scan history
                addToHistory(data);
                
                // Hide result after 5 seconds
                setTimeout(() => {
                    resultDiv.classList.add('hidden');
                }, 5000);
            } else {
                resultDiv.classList.remove('bg-green-100', 'border', 'border-green-400');
                resultDiv.classList.add('bg-yellow-100', 'border', 'border-yellow-400');
                scanMessage.textContent = data.message || 'User not found';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.classList.add('hidden');
            errorDiv.classList.remove('hidden');
            document.getElementById('error-message').textContent = 'Error processing scan. Please try again.';
            
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        });
    }

    function onScanFailure(error) {
        // Handle scan failure (usually just noise, don't show error)
        console.warn(`Code scan error = ${error}`);
    }

    function addToHistory(data) {
        const historyDiv = document.getElementById('scan-history');
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const dateString = now.toLocaleDateString();
        
        const isEntry = data.scan_type === 'entry';
        const typeColor = isEntry ? 'text-green-600' : 'text-orange-600';
        const typeLabel = isEntry ? 'ENTRY' : 'EXIT';
        const bgColor = isEntry ? 'bg-green-50' : 'bg-orange-50';
        
        const historyItem = `
            <div class="${bgColor} border rounded-lg p-3 animate-fade-in">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800">${data.inside_user.fullname}</p>
                        <p class="text-sm text-gray-600">QR: ${data.inside_user.qr_value}</p>
                        <p class="text-sm text-gray-500">${dateString} ${timeString}</p>
                    </div>
                    <span class="${typeColor} font-bold px-3 py-1 rounded-full bg-white border">${typeLabel}</span>
                </div>
            </div>
        `;
        
        historyDiv.insertAdjacentHTML('afterbegin', historyItem);
        
        // Keep only last 10 scans
        while (historyDiv.children.length > 10) {
            historyDiv.removeChild(historyDiv.lastChild);
        }
    }

    // Initialize camera on page load
    initCamera();

    // Load recent scans on page load
    fetch("{{ route('security.scan.history') }}", {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const historyDiv = document.getElementById('scan-history');
        if (data.scans && data.scans.length > 0) {
            data.scans.forEach(scan => {
                addToHistory({
                    inside_user: scan.inside_user,
                    scan_type: scan.scan_type,
                    created_at: scan.created_at
                });
            });
        }
    })
    .catch(error => console.error('Error loading history:', error));
    </script>
</body>
</html>
