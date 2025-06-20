import { createApp } from 'vue';
import LiffSample from './components/LiffSample.vue';

// Debug logging to page
function addDebugLog(message: string, isError = false) {
    const debugEl = document.getElementById('debug-log');
    if (debugEl) {
        const logEntry = document.createElement('div');
        logEntry.style.cssText = `
            padding: 5px;
            margin: 2px 0;
            font-size: 12px;
            font-family: monospace;
            background: ${isError ? 'rgba(255,0,0,0.2)' : 'rgba(0,0,0,0.1)'};
            border-radius: 4px;
            color: ${isError ? '#ff6b6b' : '#333'};
        `;
        logEntry.textContent = `${new Date().toLocaleTimeString()}: ${message}`;
        debugEl.appendChild(logEntry);
        debugEl.scrollTop = debugEl.scrollHeight;
    }
    console.log(message);
}

// Initialize LIFF and mount Vue app
async function initializeLiff() {
    addDebugLog('Starting LIFF initialization...');
    const liffId = (window as any).LIFF_ID;
    
    addDebugLog(`LIFF ID: ${liffId}`);
    
    if (!liffId) {
        addDebugLog('LIFF ID not found', true);
        // Still mount Vue app for debugging
        const app = createApp(LiffSample);
        app.mount('#liff-app');
        return;
    }

    try {
        addDebugLog(`Initializing LIFF with ID: ${liffId}`);
        await (window as any).liff.init({ liffId });
        
        addDebugLog('LIFF initialization successful, mounting Vue app...');
        // Mount Vue app after LIFF initialization
        const app = createApp(LiffSample);
        app.mount('#liff-app');
        
        addDebugLog('LIFF initialized and Vue app mounted');
    } catch (error) {
        addDebugLog(`LIFF initialization failed: ${error.message}`, true);
        // Still mount Vue app to show error state
        const app = createApp(LiffSample);
        app.mount('#liff-app');
    }
}

// Wait for LIFF SDK to load
function waitForLiff() {
    if (typeof (window as any).liff !== 'undefined') {
        addDebugLog('LIFF SDK loaded, initializing...');
        initializeLiff();
    } else {
        addDebugLog('Waiting for LIFF SDK to load...');
        setTimeout(waitForLiff, 100);
    }
}

// Start checking for LIFF SDK
addDebugLog('Document ready, starting LIFF check...');
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', waitForLiff);
} else {
    waitForLiff();
}