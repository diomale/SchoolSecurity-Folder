<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner - CCSS</title>
    <!-- Modern Font: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/SecurityGuardStyleFolder/securityguard_style_dashboard.css', 'resources/css/SecurityGuardStyleFolder/securityguard_style_scanner.css'])
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">CCSS</div>
                <h2 style="font-size:1.1rem; line-height:1.2;">Columban College<br><small style="font-weight: 500; font-size: 0.85rem; color: var(--text-muted);">Security System</small></h2>
            </div>
            <nav class="sidebar-nav">
                <!-- Direct linking instead of SPA tabs since we are out of the dashboard -->
                <a href="{{ route('security.dashboard') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">📊</span> Back to Command
                </a>
                <a href="{{ route('security.scanner.show') }}" class="tab-button active" style="text-decoration: none;">
                    <span class="nav-icon">🔍</span> QR Scanner
                </a>
                <a href="{{ route('security.quick-pass.list') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">🚗</span> Quick Pass
                </a>
                <a href="{{ route('security.entry.logs') }}" class="tab-button" style="text-decoration: none;">
                    <span class="nav-icon">📜</span> Entry Logs
                </a>
            </nav>
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('security.logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="nav-icon">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1 class="fade-in">QR <span class="highlight">Scanner</span></h1>
                    <p class="subtitle fade-in" style="animation-delay: 0.1s;">Scan digital passes or temporary visitor slips.</p>
                </div>
            </header>

            <div class="scanner-container fade-in" style="animation-delay: 0.2s;">
                <!-- Left Col: Scanner Area -->
                <div class="glass-card">
                    <div class="scanner-tabs">
                        <button id="camera-tab" class="scanner-tab-btn active" onclick="switchScannerTab('camera')">Camera Scan</button>
                        <button id="image-tab" class="scanner-tab-btn" onclick="switchScannerTab('image')">Upload Image</button>
                    </div>

                    <!-- Camera View -->
                    <div id="camera-view">
                        <div id="reader"></div>
                    </div>

                    <!-- Image Upload View -->
                    <div id="image-view" class="hidden">
                        <input type="file" id="qr-image-input" accept="image/*" class="hidden" onchange="scanImageFile(event)">
                        <label for="qr-image-input" class="upload-container" style="display: block;">
                            <div class="upload-icon">📷</div>
                            <p style="font-weight: 600; color: var(--text-main); margin-bottom: 5px;">Click to upload QR code image</p>
                            <p style="font-size: 0.85rem; color: var(--text-muted);">PNG, JPG, GIF up to 10MB</p>
                        </label>
                        
                        <div id="image-reader" class="hidden"></div>
                        <div id="image-preview" class="hidden mt-4 text-center">
                            <img id="preview-img" style="max-width: 100%; max-height: 300px; border-radius: var(--radius-md); border: 1px solid rgba(0,0,0,0.1); display: inline-block;">
                            <button onclick="clearImage()" style="margin-top: 15px; color: var(--danger); background: none; border: none; font-weight: 600; cursor: pointer;">Cancel Upload</button>
                        </div>
                    </div>

                    <!-- Feedback Box -->
                    <div id="scan-result" class="scan-result-box hidden">
                        <h3 style="margin-bottom: 5px; font-size: 1.1rem; color: var(--text-main);">Scanned: <span id="qr-value" style="color: var(--primary);"></span></h3>
                        <p id="scan-message" style="color: var(--text-muted); font-weight: 500;"></p>
                    </div>
                </div>

                <!-- Right Col: History Feed -->
                <div class="glass-card" style="height: fit-content;">
                    <h3 style="border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 15px; margin-bottom: 20px;">Recent Scans</h3>
                    <div id="scan-history" class="history-feed">
                        <!-- Loaded dynamically -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Html5-Qrcode Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
    let html5QrcodeScanner;
    let lastScanTime = null;
    let lastQrValue = null;
    const SCAN_COOLDOWN = 3000; 

    function switchScannerTab(tab) {
        const cameraTab = document.getElementById('camera-tab');
        const imageTab = document.getElementById('image-tab');
        const cameraView = document.getElementById('camera-view');
        const imageView = document.getElementById('image-view');

        if (tab === 'camera') {
            cameraTab.classList.add('active');
            imageTab.classList.remove('active');
            cameraView.classList.remove('hidden');
            imageView.classList.add('hidden');
            
            if (!html5QrcodeScanner) initCamera();
        } else {
            imageTab.classList.add('active');
            cameraTab.classList.remove('active');
            imageView.classList.remove('hidden');
            cameraView.classList.add('hidden');
        }
    }

    function initCamera() {
        html5QrcodeScanner = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 600, height: 600 }, aspectRatio: 1.0 };

        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Failed to start scanner", err);
            document.getElementById('reader').innerHTML = `
                <div style="text-align: center; padding: 40px; color: var(--danger);">
                    <p>Failed to start camera. Please ensure camera permissions are granted.</p>
                    <button onclick="location.reload()" style="margin-top: 20px; padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: var(--radius-sm); cursor: pointer;">Retry</button>
                </div>
            `;
        });
    }

    function scanImageFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        preview.classList.remove('hidden');
        previewImg.src = URL.createObjectURL(file);

        document.getElementById('scan-result').classList.add('hidden');

        const html5QrCode = new Html5Qrcode("image-reader");
        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                processScanResult(decodedText);
                html5QrCode.clear().catch(err => console.error('Failed to clear image reader', err));
            }).catch(err => {
                showScanFeedback(false, 'Unknown', 'Could not read QR from image.');
            });
    }

    function clearImage() {
        document.getElementById('qr-image-input').value = '';
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('preview-img').src = '';
    }

    function onScanSuccess(decodedText) {
        const currentTime = new Date().getTime();
        if (lastScanTime && (currentTime - lastScanTime) < SCAN_COOLDOWN && lastQrValue === decodedText) {
            return;
        }
        lastScanTime = currentTime;
        lastQrValue = decodedText;
        processScanResult(decodedText);
    }

    function processScanResult(decodedText) {
        let qrValue = decodedText;
        const urlMatch = decodedText.match(/\/scan\/(.+)$/);
        if (urlMatch && urlMatch[1]) qrValue = urlMatch[1];
        
        const eventMatch = decodedText.match(/\/event\/scan\/(.+)$/);
        if (eventMatch && eventMatch[1]) qrValue = eventMatch[1];

        // UI Reset
        showScanFeedback('info', 'Scanning...', 'Please wait.');

        fetch("{{ route('security.scan.qr') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_value: qrValue })
        })
        .then(response => response.json())
        .then(data => {
            let fullname = 'Unknown User';
            if (data.inside_user && data.inside_user.fullname) fullname = data.inside_user.fullname;
            else if (data.event_registration && data.event_registration.fullname) fullname = data.event_registration.fullname;
            else if (data.quick_pass && data.quick_pass.visitor_name) fullname = data.quick_pass.visitor_name;
            else if (data.outside_user && data.outside_user.first_name) fullname = data.outside_user.first_name + ' ' + data.outside_user.last_name;

            if (data.success) {
                let userTypeLabel = 'Staff/Student';
                if (data.user_type === 'quick_pass') userTypeLabel = 'Quick Pass';
                else if (data.user_type === 'outside') userTypeLabel = 'Visitor';
                else if (data.user_type === 'event') userTypeLabel = 'Event Attendee';
                
                showScanFeedback('success', fullname, `${data.message} (${userTypeLabel})`);
                addToHistory(data);
            } else {
                showScanFeedback('warning', fullname, data.message || 'User not found or access denied.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showScanFeedback('error', 'Error', 'Failed to process scan. Server error.');
        });
    }

    function onScanFailure(error) { /* silence scan noise */ }

    function showScanFeedback(type, title, message) {
        const box = document.getElementById('scan-result');
        box.className = 'scan-result-box'; // reset
        if(type === 'success') box.classList.add('scan-result-success');
        if(type === 'warning') box.classList.add('scan-result-warning');
        if(type === 'error') box.classList.add('scan-result-error');
        if(type === 'info') box.classList.add('scan-result-info');
        
        document.getElementById('qr-value').textContent = title;
        document.getElementById('scan-message').textContent = message;
        
        // Hide after 5 sec
        setTimeout(() => box.classList.add('hidden'), 5000);
    }

    function addToHistory(data) {
        if (!data || (!data.inside_user && !data.event_registration && !data.quick_pass && !data.outside_user)) return;

        const historyDiv = document.getElementById('scan-history');
        const d = new Date(data.scan_at || new Date());
        const timeStr = d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        let fullname = 'Unknown';
        if (data.event_registration && data.event_registration.fullname) fullname = data.event_registration.fullname;
        else if (data.inside_user && data.inside_user.fullname) fullname = data.inside_user.fullname;
        else if (data.quick_pass && data.quick_pass.visitor_name) fullname = data.quick_pass.visitor_name;
        else if (data.outside_user && data.outside_user.first_name) fullname = data.outside_user.first_name + ' ' + data.outside_user.last_name;

        const sType = data.scan_type ? data.scan_type.toLowerCase() : '';
        const userType = data.user_type || '';

        let badgeClass = 'badge-outline';
        let badgeText = sType.toUpperCase();
        let boxClass = 'history-item-unknown';

        if (userType === 'event') {
            boxClass = 'history-item-event';
            badgeClass = 'badge-blue';
            badgeText = sType === 'entry' ? 'IN' : 'OUT';
        } else if (sType === 'entry') {
            boxClass = 'history-item-entry';
            badgeClass = 'badge-success';
        } else if (sType === 'exit') {
            boxClass = 'history-item-exit';
            badgeClass = 'badge-warning';
        }

        const itemHTML = `
            <div class="history-item ${boxClass}">
                <div class="history-details">
                    <p style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">${fullname}</p>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">${timeStr} • ${userType.toUpperCase()}</p>
                </div>
                <span class="badge ${badgeClass}">${badgeText}</span>
            </div>
        `;
        
        historyDiv.insertAdjacentHTML('afterbegin', itemHTML);
        while (historyDiv.children.length > 10) { historyDiv.removeChild(historyDiv.lastChild); }
    }

    initCamera();

    fetch("{{ route('security.scan.history') }}", { headers: {'Accept': 'application/json'} })
    .then(r => r.json())
    .then(data => {
        if (data.scans && data.scans.length > 0) {
            data.scans.reverse().forEach(scan => addToHistory(scan));
        }
    }).catch(e => console.error(e));
    </script>
</body>
</html>
