// src/lib/apiClient.js
// =====================================================================
// Uses axios for automatic JSON serialization, request/response
// interceptors, and centralized error handling.
// =====================================================================

import axios from "axios";

// ── Base URL ──────────────────────────────────────────────────────────
const BASE_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api";

// ── Create Axios Instance ─────────────────────────────────────────────
const apiClient = axios.create({
  baseURL: BASE_URL,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// ── Request Interceptor ───────────────────────────────────────────────
apiClient.interceptors.request.use(
  (config) => {
    if (typeof window !== "undefined") {
      const token = localStorage.getItem("auth_token");
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// ── Response Interceptor ──────────────────────────────────────────────
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    const { response } = error;

    if (!response) {
      return Promise.reject(
        new Error("Network error — cannot reach the API server.")
      );
    }

    switch (response.status) {
      case 401:
        if (typeof window !== "undefined") {
          localStorage.removeItem("auth_token");
          localStorage.removeItem("auth_user");
          if (!window.location.pathname.startsWith("/admin/login")) {
            window.location.href = "/admin/login";
          }
        }
        break;

      case 422: {
        const validationErrors = response.data?.errors || {};
        const messages = Object.values(validationErrors).flat().join(" | ");
        return Promise.reject(
          new Error(messages || response.data?.message || "Validation failed.")
        );
      }

      case 403:
        return Promise.reject(
          new Error(response.data?.message || "Access denied.")
        );

      case 500:
        return Promise.reject(
          new Error(response.data?.message || "Server error occurred.")
        );
    }

    return Promise.reject(error);
  }
);

export default apiClient;
