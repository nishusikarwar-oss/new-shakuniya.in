"use client";

import { Bell, Search } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useAuth } from "@/hooks/useAuth";

const AdminTopbar = () => {
  const { user } = useAuth();

  return (
    <header className="h-16 sticky top-0 z-30 bg-slate-950 border-b border-white/10 backdrop-blur-xl">
      <div className="h-full flex items-center justify-between px-6">

        {/* Search */}
        {/* <div className="relative w-96 hidden md:block">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <Input
            placeholder="Search..."
            className="pl-10 bg-slate-900 border-white/10 text-gray-200 placeholder:text-gray-500
                       focus:bg-slate-800 focus:border-blue-500"
          />
        </div> */}

        {/* Right Section */}
        <div className="flex items-center gap-4 ml-auto">

          {/* Notifications */}
          <Button
            variant="ghost"
            size="icon"
            className="relative text-gray-400 hover:text-white hover:bg-white/5"
          >
            <Bell size={20} />
            <span className="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full
                             text-[10px] flex items-center justify-center text-white">
              3
            </span>
          </Button>

          {/* User */}
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-full bg-gradient-to-r from-purple-500 to-cyan-500
                            flex items-center justify-center">
              <span className="text-white font-medium text-sm">
                {user?.email?.charAt(0).toUpperCase() || "A"}
              </span>
            </div>

            <div className="hidden lg:block">
              <p className="text-sm font-medium text-gray-200 leading-tight">
                {user?.user_metadata?.full_name || "Admin User"}
              </p>
              <p className="text-xs text-gray-400">
                Administrator
              </p>
            </div>
          </div>

        </div>
      </div>
    </header>
  );
};

export default AdminTopbar;
