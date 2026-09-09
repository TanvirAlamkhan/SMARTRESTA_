/**
 * SMARTRESTA Standardized Fetch/AJAX Wrapper
 * Connects Vanilla JS components seamlessly with PHP APIs.
 * Includes Safe JSON Parsing & HTML Response Fallback Guard.
 */

const SmartAPI = {
  isRedirecting: false,

  async safeParseJSON(response) {
    const rawText = await response.text();
    if (!rawText || !rawText.trim()) {
      return { success: response.ok, data: null };
    }
    
    try {
      return JSON.parse(rawText);
    } catch (e) {
      console.warn(`SmartAPI: Non-JSON response received from server (HTTP ${response.status}):`, rawText.substring(0, 200));
      
      let errorDetail = `Server returned invalid JSON format (HTTP ${response.status})`;
      if (rawText.includes('<title>')) {
        const titleMatch = rawText.match(/<title>(.*?)<\/title>/i);
        if (titleMatch && titleMatch[1]) {
          errorDetail = `Server Error: ${titleMatch[1].trim()} (HTTP ${response.status})`;
        }
      } else if (rawText.includes('<h1>')) {
        const h1Match = rawText.match(/<h1>(.*?)<\/h1>/i);
        if (h1Match && h1Match[1]) {
          errorDetail = `Server Error: ${h1Match[1].trim()} (HTTP ${response.status})`;
        }
      }
      
      return {
        success: false,
        statusCode: response.status,
        message: errorDetail,
        rawText: rawText
      };
    }
  },

  async request(endpoint, options = {}) {
    const defaultHeaders = {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    };

    const config = {
      method: options.method || 'GET',
      headers: { ...defaultHeaders, ...options.headers },
      body: options.body ? JSON.stringify(options.body) : null
    };

    try {
      const response = await fetch(endpoint, config);
      const data = await this.safeParseJSON(response);

      // Handle Unauthenticated Status (401) for non-login endpoints
      const isAuthEndpoint = endpoint.includes('/auth/login') || endpoint.includes('auth/login.php');
      if (!isAuthEndpoint && (response.status === 401 || data.statusCode === 401 || (data.message && data.message.includes('Authentication required')))) {
        if (!this.isRedirecting) {
          this.isRedirecting = true;
          if (window.SmartNotifications && typeof window.SmartNotifications.show === 'function') {
            SmartNotifications.show('Authentication required. Redirecting to login...', 'warning');
          }
          setTimeout(() => {
            const loginPath = window.location.pathname.includes('/public/') ? 'login.php' : 'public/login.php';
            window.location.href = loginPath;
          }, 800);
        }
        throw new Error('Authentication required');
      }

      if (!response.ok || !data.success) {
        const errMsg = data.message || `API Request Failed (HTTP ${response.status})`;
        throw new Error(errMsg);
      }

      return data;
    } catch (error) {
      console.error(`SmartAPI Error [${endpoint}]:`, error.message);
      
      // Do not flood toasts if already redirecting to login
      if (!this.isRedirecting && window.SmartNotifications && typeof window.SmartNotifications.show === 'function') {
        if (error.message !== 'Authentication required') {
          SmartNotifications.show(error.message || 'Network request failed', 'danger');
        }
      }
      throw error;
    }
  },

  get(endpoint) {
    return this.request(endpoint, { method: 'GET' });
  },

  post(endpoint, body) {
    return this.request(endpoint, { method: 'POST', body });
  },

  put(endpoint, body) {
    return this.request(endpoint, { method: 'PUT', body });
  },

  delete(endpoint) {
    return this.request(endpoint, { method: 'DELETE' });
  }
};

window.SmartAPI = SmartAPI;
