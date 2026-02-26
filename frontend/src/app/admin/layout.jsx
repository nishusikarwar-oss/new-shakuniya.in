"use client";

import { useState } from "react";
import AdminSidebar from "@/components/dashboard/AdminSidebar";
import AdminTopbar from "@/components/dashboard/AdminTopbar";
import { cn } from "@/lib/utils";

export default function AdminLayout({ children }) {
  const [collapsed, setCollapsed] = useState(false);

  return (
    <div className="min-h-screen bg-slate-900 text-gray-200 flex">
      
      {/* Sidebar */}
      <AdminSidebar collapsed={collapsed} setCollapsed={setCollapsed} />

      {/* Main Content */}
      <div className={cn(
        "flex-1 transition-all duration-300",
        collapsed ? "ml-14" : "ml-64"
      )}>
        <AdminTopbar />

        <main className="p-6">
          {children}
        </main>
      </div>

    </div>
  );
}
