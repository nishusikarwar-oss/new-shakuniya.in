"use client";

import { usePathname, useRouter } from "next/navigation";
import { useAuth } from "@/hooks/useAuth";
import { Loader2 } from "lucide-react";
import { useEffect } from "react";

const ProtectedRoute = ({ children, requireAdmin = false }) => {
  const { user, isAdmin, isLoading } = useAuth();
  const router = useRouter();
  const pathname = usePathname();

  useEffect(() => {
    if (!isLoading && !user) {
      router.replace("/admin/login");
    }

    if (!isLoading && requireAdmin && !isAdmin) {
      router.replace("/");
    }
  }, [user, isAdmin, isLoading, requireAdmin, router]);

  if (isLoading || !user) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-900">
        <Loader2 className="w-8 h-8 animate-spin text-blue-500" />
      </div>
    );
  }

  return <>{children}</>;
};

export default ProtectedRoute;
