// src/lib/authService.js
// =====================================================================
// All authentication calls to the Laravel Sanctum API.
// =====================================================================

import apiClient from "./apiClient";

const authService = {
  /**
   * POST /api/login
   * Returns { success, data: { user, token } }
   */
  async login(email, password) {
    const response = await apiClient.post("/login", { email, password });
    const { data } = response.data;

    // Persist token and user info in localStorage
    if (data?.token) {
      localStorage.setItem("auth_token", data.token);
      localStorage.setItem("auth_user", JSON.stringify(data.user));
    }

    return data;
  },

  /**
   * POST /api/admin/login
   * Returns { success, data: { user, token } }
   */
  async adminLogin(email, password) {
    const response = await apiClient.post("/admin/login", { email, password });
    const { data } = response.data;

    // Persist token and user info in localStorage
    if (data?.token) {
      localStorage.setItem("auth_token", data.token);
      localStorage.setItem("auth_user", JSON.stringify(data.user));
    }

    return data;
  },

  /**
   * POST /api/logout
   * Revokes the current Sanctum token on the server.
   */
  async logout() {
    try {
      await apiClient.post("/logout");
    } finally {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("auth_user");
    }
  },

  /**
   * GET /api/user
   * Returns the currently authenticated user.
   */
  async me() {
    const response = await apiClient.get("/user");
    return response.data?.data?.user || null;
  },

  /**
   * Read the persisted user from localStorage (no network call).
   */
  getStoredUser() {
    if (typeof window === "undefined") return null;
    try {
      const raw = localStorage.getItem("auth_user");
      return raw ? JSON.parse(raw) : null;
    } catch {
      return null;
    }
  },

  /**
   * Is there a token in localStorage?
   */
  isAuthenticated() {
    if (typeof window === "undefined") return false;
    return !!localStorage.getItem("auth_token");
  },
};

export default authService;
