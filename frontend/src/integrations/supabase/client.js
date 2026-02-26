// import { createClient } from "@supabase/supabase-js";

// const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL || "https://placeholder.supabase.co";
// const supabaseAnonKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY || "placeholder-key";

// if (!process.env.NEXT_PUBLIC_SUPABASE_URL || !process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY) {
//   console.error(
//     "Missing Supabase env vars. Check NEXT_PUBLIC_SUPABASE_URL and NEXT_PUBLIC_SUPABASE_ANON_KEY"
//   );
// }

// export const supabase = createClient(
//     supabaseUrl,
//   supabaseAnonKey,
//   {
//     auth: {
//       persistSession: true,
//       autoRefreshToken: true,
//       storage:
//         typeof window !== "undefined" ? window.localStorage : undefined,
//     },
//   }
// );


import { createClient } from "@supabase/supabase-js";

const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL;
const supabaseAnonKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY;

let supabase = null;

if (supabaseUrl && supabaseAnonKey) {
  supabase = createClient(supabaseUrl, supabaseAnonKey, {
    auth: {
      persistSession: true,
      autoRefreshToken: true,
      storage:
        typeof window !== "undefined" ? window.localStorage : undefined,
    },
  });
} else {
  // ⚠️ No env file → no crash
  console.warn(
    "Supabase not configured. App running without Supabase."
  );
}

export { supabase };

