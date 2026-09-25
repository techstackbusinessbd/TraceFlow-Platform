import axios, { type AxiosError, type InternalAxiosRequestConfig } from 'axios';
import { useAuthStore } from '../stores/authStore';

/**
 * Centralized API Client (SRS_LOGIN & APPROVED-PACKAGES-REGISTRY)
 *
 * Configured with baseURL, automated Bearer Token injection,
 * and unified error interceptors.
 */
export const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api/v1',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  timeout: 30000,
});

// Request Interceptor: Attach Sanctum Bearer Token
apiClient.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = useAuthStore.getState().token;
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error: unknown) => Promise.reject(error)
);

// Response Interceptor: Handle 401 Unauthorized Session Expiration
apiClient.interceptors.response.use(
  (response) => response,
  (error: AxiosError) => {
    if (error.response && error.response.status === 401) {
      // Clear expired auth session and redirect to login
      const logout = useAuthStore.getState().logout;
      logout();
      if (window.location.pathname !== '/login') {
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);
