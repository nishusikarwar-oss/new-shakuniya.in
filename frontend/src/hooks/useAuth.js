// "use client";

// import { useState, useEffect, createContext, useContext } from "react";
// import { supabase } from "@/integrations/supabase/client"

// const AuthContext = createContext(undefined);

// export const AuthProvider = ({ children }) => {
//   const [user, setUser] = useState(null);
//   const [session, setSession] = useState(null);
//   const [isAdmin, setIsAdmin] = useState(false);
//   const [isLoading, setIsLoading] = useState(true);

//   const checkAdminStatus = async (userId) => {
//     const { data } = await supabase
//       .from("user_roles")
//       .select("role")
//       .eq("user_id", userId)
//       .eq("role", "admin")
//       .maybeSingle();

//     return !!data;
//   };

//   useEffect(() => {
//     supabase.auth.getSession().then(async ({ data }) => {
//       setSession(data.session);
//       setUser(data.session?.user ?? null);

//       if (data.session?.user) {
//         setIsAdmin(await checkAdminStatus(data.session.user.id));
//       }

//       setIsLoading(false);
//     });

//     const {
//       data: { subscription },
//     } = supabase.auth.onAuthStateChange(async (_, session) => {
//       setSession(session);
//       setUser(session?.user ?? null);
//       setIsAdmin(
//         session?.user ? await checkAdminStatus(session.user.id) : false
//       );
//       setIsLoading(false);
//     });

//     return () => subscription.unsubscribe();
//   }, []);

//   const signIn = async (email, password) =>
//     supabase.auth.signInWithPassword({ email, password });

//   const signUp = async (email, password, fullName) =>
//     supabase.auth.signUp({
//       email,
//       password,
//       options: { data: { full_name: fullName } },
//     });

//   const signOut = async () => {
//     await supabase.auth.signOut();
//     setIsAdmin(false);
//   };

//   return (
//     <AuthContext.Provider
//       value={{ user, session, isAdmin, isLoading, signIn, signUp, signOut }}
//     >
//       {children}
//     </AuthContext.Provider>
//   );
// };

// export const useAuth = () => {
//   const ctx = useContext(AuthContext);
//   if (!ctx) {
//     throw new Error("useAuth must be used inside AuthProvider");
//   }
//   return ctx;
// };

"use client";

import { createContext, useContext, useEffect, useState } from "react";
import { supabase } from "@/integrations/supabase/client";

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [session, setSession] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // ✅ GUARD: Supabase not configured
    if (!supabase) {
      setUser(null);
      setSession(null);
      setLoading(false);
      return;
    }

    // get initial session
    supabase.auth.getSession().then(({ data }) => {
      setSession(data.session);
      setUser(data.session?.user ?? null);
      setLoading(false);
    });

    // listen to auth changes
    const {
      data: { subscription },
    } = supabase.auth.onAuthStateChange((_event, session) => {
      setSession(session);
      setUser(session?.user ?? null);
    });

    return () => {
      subscription?.unsubscribe();
    };
  }, []);

  const value = {
    user,
    session,
    loading,
    isAuthenticated: !!user,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error("useAuth must be used within AuthProvider");
  }
  return context;
};
