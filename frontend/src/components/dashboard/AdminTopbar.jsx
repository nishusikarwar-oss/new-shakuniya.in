"use client";
import { Bell } from "lucide-react";
import { Button } from "@/components/ui/button";
import { useAuth } from "@/hooks/useAuth";

export default function AdminTopbar() {
  const { user } = useAuth();
  // ✅ FIX: Laravel returns user.name, not user.user_metadata.full_name (Supabase field)
  const displayName   = user?.name  || "Admin User";
  const displayLetter = (user?.name || user?.email || "A").charAt(0).toUpperCase();

  return (
    <header className="h-16 sticky top-0 z-30 bg-slate-950 border-b border-white/10 backdrop-blur-xl">
      <div className="h-full flex items-center justify-between px-6">
        <div className="flex items-center gap-4 ml-auto">
          <Button variant="ghost" size="icon" className="relative text-gray-400 hover:text-white hover:bg-white/5">
            <Bell size={20} />
            <span className="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-[10px] flex items-center justify-center text-white">3</span>
          </Button>

          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-full bg-gradient-to-r from-purple-500 to-cyan-500 flex items-center justify-center">
              <span className="text-white font-medium text-sm">{displayLetter}</span>
            </div>
            <div className="hidden lg:block">
              <p className="text-sm font-medium text-gray-200 leading-tight">{displayName}</p>
              <p className="text-xs text-gray-400">Administrator</p>
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}
