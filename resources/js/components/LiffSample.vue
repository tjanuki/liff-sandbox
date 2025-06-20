<template>
  <div class="liff-sample">
    <div class="header">
      <h1>LIFF Sample Page</h1>
      <p class="subtitle">Development with Vite HMR</p>
    </div>

    <div class="content">
      <div class="status-card">
        <h2>LIFF Status</h2>
        <div class="status-grid">
          <div class="status-item">
            <span class="label">Logged In:</span>
            <span :class="['status', isLoggedIn ? 'success' : 'error']">
              {{ isLoggedIn ? 'Yes' : 'No' }}
            </span>
          </div>
          <div class="status-item">
            <span class="label">In Client:</span>
            <span :class="['status', inClient ? 'success' : 'warning']">
              {{ inClient ? 'Yes' : 'No' }}
            </span>
          </div>
          <div class="status-item">
            <span class="label">OS:</span>
            <span class="status">{{ os }}</span>
          </div>
        </div>
      </div>

      <div class="user-card" v-if="isLoggedIn && profile">
        <h2>User Profile</h2>
        <div class="profile-info">
          <img v-if="profile.pictureUrl" :src="profile.pictureUrl" alt="Profile" class="profile-image">
          <div class="profile-details">
            <p><strong>Name:</strong> {{ profile.displayName }}</p>
            <p><strong>User ID:</strong> {{ profile.userId }}</p>
            <p v-if="profile.statusMessage"><strong>Status:</strong> {{ profile.statusMessage }}</p>
          </div>
        </div>
      </div>

      <div class="actions">
        <button v-if="!isLoggedIn" @click="login" class="btn btn-primary">
          Login with LINE
        </button>
        <button v-if="isLoggedIn" @click="logout" class="btn btn-secondary">
          Logout
        </button>
        <button v-if="inClient" @click="closeWindow" class="btn btn-success">
          Close Window
        </button>
        <button @click="sendMessage" class="btn btn-info" :disabled="!canSendMessage">
          Send Message
        </button>
      </div>

      <div class="debug-info">
        <h3>Debug Info</h3>
        <pre>{{ debugInfo }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';

const isLoggedIn = ref(false);
const inClient = ref(false);
const os = ref('Unknown');
const profile = ref<any>(null);
const debugInfo = ref<any>({});

const canSendMessage = computed(() => {
  return isLoggedIn.value && inClient.value;
});

const updateLiffStatus = async () => {
  const liff = (window as any).liff;
  
  if (!liff) {
    debugInfo.value = { error: 'LIFF SDK not loaded' };
    return;
  }

  try {
    isLoggedIn.value = liff.isLoggedIn();
    inClient.value = liff.isInClient();
    os.value = liff.getOS();

    if (isLoggedIn.value) {
      profile.value = await liff.getProfile();
    }

    debugInfo.value = {
      version: liff.getVersion(),
      lineVersion: liff.getLineVersion(),
      isApiAvailable: {
        shareTargetPicker: liff.isApiAvailable('shareTargetPicker'),
        multipleLiffTransition: liff.isApiAvailable('multipleLiffTransition'),
        subwindow: liff.isApiAvailable('subwindow'),
      },
      context: liff.getContext(),
    };
  } catch (error) {
    debugInfo.value = { error: error.message };
  }
};

const login = () => {
  const liff = (window as any).liff;
  if (liff) {
    liff.login();
  }
};

const logout = () => {
  const liff = (window as any).liff;
  if (liff) {
    liff.logout();
    location.reload();
  }
};

const closeWindow = () => {
  const liff = (window as any).liff;
  if (liff && liff.isInClient()) {
    liff.closeWindow();
  }
};

const sendMessage = async () => {
  const liff = (window as any).liff;
  if (!liff || !liff.isInClient()) return;

  try {
    await liff.sendMessages([
      {
        type: 'text',
        text: 'Hello from LIFF Sample Page! 🚀'
      }
    ]);
    alert('Message sent successfully!');
  } catch (error) {
    alert('Failed to send message: ' + error.message);
  }
};

// Hot reload support
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log('LIFF Sample component hot reloaded');
  });
}

onMounted(() => {
  updateLiffStatus();
  
  // Refresh status every 5 seconds in development
  if (import.meta.env.DEV) {
    setInterval(updateLiffStatus, 5000);
  }
});
</script>

<style scoped>
.liff-sample {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header {
  text-align: center;
  color: white;
  margin-bottom: 30px;
}

.header h1 {
  font-size: 2.5rem;
  margin: 0;
  font-weight: 700;
}

.subtitle {
  font-size: 1.2rem;
  opacity: 0.9;
  margin: 10px 0 0 0;
}

.content {
  max-width: 800px;
  margin: 0 auto;
}

.status-card, .user-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  margin-bottom: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.status-card h2, .user-card h2 {
  margin: 0 0 20px 0;
  color: #333;
  font-size: 1.5rem;
}

.status-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.status-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 8px;
}

.label {
  font-weight: 600;
  color: #666;
}

.status {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 600;
}

.status.success {
  background: #d4edda;
  color: #155724;
}

.status.error {
  background: #f8d7da;
  color: #721c24;
}

.status.warning {
  background: #fff3cd;
  color: #856404;
}

.profile-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.profile-image {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 3px solid #eee;
}

.profile-details p {
  margin: 5px 0;
  color: #555;
}

.actions {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 30px;
  justify-content: center;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 25px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 140px;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #0056b3;
  transform: translateY(-2px);
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #545b62;
  transform: translateY(-2px);
}

.btn-success {
  background: #28a745;
  color: white;
}

.btn-success:hover:not(:disabled) {
  background: #1e7e34;
  transform: translateY(-2px);
}

.btn-info {
  background: #17a2b8;
  color: white;
}

.btn-info:hover:not(:disabled) {
  background: #117a8b;
  transform: translateY(-2px);
}

.debug-info {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.debug-info h3 {
  margin: 0 0 15px 0;
  color: #333;
}

.debug-info pre {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  overflow-x: auto;
  font-size: 0.9rem;
  color: #666;
}

@media (max-width: 768px) {
  .header h1 {
    font-size: 2rem;
  }
  
  .actions {
    flex-direction: column;
    align-items: center;
  }
  
  .btn {
    width: 100%;
    max-width: 300px;
  }
  
  .profile-info {
    flex-direction: column;
    text-align: center;
  }
}
</style>