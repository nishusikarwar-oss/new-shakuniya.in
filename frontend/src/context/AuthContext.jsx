"use client";

// src/context/AuthContext.jsx
// =====================================================================
// Replaces the old Supabase auth provider.
// Provides: user, isLoading, isAdmin, signIn, signOut
// =====================================================================

import { createContext, useContext, useEffect, useState } from "react";
import authService from "@/lib/authService";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser]         = useState(null);
  const [isLoading, setLoading] = useState(true);

  // On mount: try to restore session from localStorage, then verify with server
  useEffect(() => {
    const restore = async () => {
      // Fast path: read from localStorage first so UI doesn't flash
      const stored = authService.getStoredUser();
      if (stored) setUser(stored);

      if (authService.isAuthenticated()) {
        try {
          const serverUser = await authService.me();
          setUser(serverUser);
          if (serverUser) {
            localStorage.setItem("auth_user", JSON.stringify(serverUser));
          }
        } catch {
          // Token invalid / expired → clear everything
          authService.logout().catch(() => {});
          setUser(null);
        }
      } else {
        setUser(null);
      }

      setLoading(false);
    };

    restore();
  }, []);

  const signIn = async (email, password) => {
    try {
      const data = await authService.login(email, password);
      setUser(data.user);
      return { error: null };
    } catch (err) {
      return { error: { message: err.response?.data?.message || err.message } };
    }
  };

  const adminSignIn = async (email, password) => {
    try {
      const data = await authService.adminLogin(email, password);
      setUser(data.user);
      return { error: null };
    } catch (err) {
      return { error: { message: err.response?.data?.message || err.message } };
    }
  };

  const signOut = async () => {
    await authService.logout();
    setUser(null);
  };

  // Check if user has admin role
  const isAdmin = user?.role === 'admin' || user?.is_admin === true;
  const isAuthenticated = !!user;

  return (
    <AuthContext.Provider value={{ user, isLoading, isAdmin, isAuthenticated, signIn, adminSignIn, signOut }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error("useAuth must be used inside <AuthProvider>");
  return ctx;
}
