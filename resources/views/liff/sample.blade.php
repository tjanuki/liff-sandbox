<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LIFF Sample - Development</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="LIFF Sample | Development">
    <meta property="og:description" content="LIFF Sample page with Vite HMR for development">
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ request()->url() }}" />

    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script>
        // Expose LIFF ID to the frontend
        window.LIFF_ID = '{{ $liffId }}';
        
        // Debug logging function
        function addDebugLog(message, isError = false) {
            const debugEl = document.getElementById('debug-log');
            if (debugEl) {
                const logEntry = document.createElement('div');
                logEntry.style.cssText = `
                    padding: 5px;
                    margin: 2px 0;
                    font-size: 12px;
                    font-family: monospace;
                    background: ${isError ? 'rgba(255,0,0,0.2)' : 'rgba(0,255,0,0.2)'};
                    border-radius: 4px;
                    color: ${isError ? '#ff6b6b' : '#333'};
                `;
                logEntry.textContent = `${new Date().toLocaleTimeString()}: ${message}`;
                debugEl.appendChild(logEntry);
                debugEl.scrollTop = debugEl.scrollHeight;
            }
        }

        // Initialize LIFF directly (fallback)
        async function initLiffFallback() {
            const liffId = '{{ $liffId }}';
            addDebugLog(`Starting LIFF initialization with ID: ${liffId}`);
            
            try {
                await liff.init({ liffId: liffId });
                addDebugLog('LIFF initialization successful!');
                
                // Get profile if logged in
                let profileInfo = '';
                if (liff.isLoggedIn()) {
                    try {
                        const profile = await liff.getProfile();
                        profileInfo = `
                            <div style="margin: 20px 0; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                                <h3>👋 Hello, ${profile.displayName}!</h3>
                                <p>User ID: ${profile.userId}</p>
                                ${profile.statusMessage ? `<p>Status: ${profile.statusMessage}</p>` : ''}
                            </div>
                        `;
                        addDebugLog(`Profile loaded: ${profile.displayName}`);
                    } catch (profileError) {
                        addDebugLog(`Failed to get profile: ${profileError.message}`, true);
                    }
                }
                
                // Hide loading, show success
                document.getElementById('liff-app').innerHTML = `
                    <div style="padding: 20px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; min-height: 100vh;">
                        <h1>🎉 LIFF Sample Works!</h1>
                        <p>LIFF ID: ${liffId}</p>
                        <p>Logged In: ${liff.isLoggedIn() ? 'Yes' : 'No'}</p>
                        <p>In Client: ${liff.isInClient() ? 'Yes' : 'No'}</p>
                        <p>OS: ${liff.getOS()}</p>
                        
                        ${profileInfo}
                        
                        <div style="margin-top: 30px;">
                            <button onclick="testLogin()" style="padding: 10px 20px; margin: 5px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                ${liff.isLoggedIn() ? 'Logout' : 'Login'}
                            </button>
                            ${liff.isInClient() ? '<button onclick="liff.closeWindow()" style="padding: 10px 20px; margin: 5px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>' : ''}
                        </div>
                        
                        <div id="debug-log" style="margin-top: 30px; background: rgba(255,255,255,0.9); border-radius: 8px; padding: 10px; color: #333; text-align: left; max-height: 200px; overflow-y: auto;"></div>
                    </div>
                `;
                
                addDebugLog('LIFF app interface loaded successfully!');
                
                // Try to load Vue app if available
                setTimeout(() => {
                    addDebugLog('Attempting to load Vue.js app with HMR...');
                    // The Vue app will replace this content if Vite is working
                }, 2000);
                
            } catch (error) {
                addDebugLog(`LIFF initialization failed: ${error.message}`, true);
            }
        }
        
        function testLogin() {
            if (liff.isLoggedIn()) {
                liff.logout();
                location.reload();
            } else {
                liff.login();
            }
        }

        // Wait for LIFF SDK and initialize
        function waitForLiff() {
            if (typeof liff !== 'undefined') {
                addDebugLog('LIFF SDK loaded, initializing...');
                initLiffFallback();
            } else {
                addDebugLog('Waiting for LIFF SDK...');
                setTimeout(waitForLiff, 100);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            addDebugLog('Basic JavaScript is working! LIFF ID: {{ $liffId }}');
            waitForLiff();
        });
    </script>

    @vite(['resources/js/liff-sample.ts'])
</head>
<body>
    <div id="liff-app">
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px;">
            <div style="text-align: center;">
                <div style="width: 40px; height: 40px; border: 4px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                <h2>Loading LIFF Sample...</h2>
                <p>Initializing with Vite HMR support</p>
            </div>
            
            <div id="debug-log" style="
                margin-top: 30px;
                width: 100%;
                max-width: 600px;
                height: 200px;
                background: rgba(255,255,255,0.9);
                border-radius: 8px;
                padding: 10px;
                overflow-y: auto;
                font-size: 12px;
                color: #333;
            ">
                <div style="font-weight: bold; margin-bottom: 10px;">Debug Log:</div>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
