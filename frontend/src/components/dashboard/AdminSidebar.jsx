"use client";

import { useState } from "react";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import {
  LayoutDashboard,
  Users,
  Mail,
  Package,
  Image as ImageIcon,
  FileText,
  Settings,
  LogOut,
  CardSim,
  ChevronLeft,
  ChevronRight,
  ChevronDown,
} from "lucide-react";

import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { useAuth } from "@/hooks/useAuth";

const sidebarLinks = [
  { name: "Dashboard", href: "/admin/dashboard", icon: LayoutDashboard },
  { name: "Users", href: "/admin/users", icon: Users },
  {
    name: "Products",
    href: "/admin/products",
    icon: Package,
    // subLinks: [
    //   // { name: "Category", href: "/admin/products" },
    //   { name: "Product Details", href: "/admin/products" },
    // ],
  },
  {
    name: "Services",
    href: "/admin/services",
    icon: CardSim,
    subLinks: [
      { name: "Category", href: "/admin/services" },
      { name: "Services Details", href: "/admin/services/servicedetails" },
    ],
  },
  { name: "FAQ", href: "/admin/faq", icon: CardSim },
  { name: "Gallery", href: "/admin/gallery", icon: ImageIcon },
  { name: "Blogs", href: "/admin/blogs", icon: FileText },
  { name: "Carrer", href: "/admin/carrer", icon: FileText },
  { name: "Contact Messages", href: "/admin/contact-message", icon: Mail },
  { name: "Contact Enquiries", href: "/admin/contact-enquiries", icon: Mail },
];

const AdminSidebar = ({ collapsed, setCollapsed }) => {
  const pathname = usePathname();
  const router = useRouter();
  const { signOut, user } = useAuth();
  const [openMenus, setOpenMenus] = useState({});

  const toggleMenu = (name) => {
    setOpenMenus((prev) => ({
      ...prev,
      [name]: !prev[name],
    }));
  };

  const handleSignOut = async () => {
    await signOut();
    router.push("/login");
  };

  return (
    <aside
      className={cn(
        "fixed left-0 top-0 h-screen bg-slate-950 border-r border-white/10 flex flex-col transition-all duration-300 z-40",
        collapsed ? "w-14" : "w-64",
      )}
    >
      {/* Logo and Toggle Button */}
      <div
        className={cn(
          "h-16 flex items-center px-4 border-b border-white/10",
          collapsed ? "justify-center" : "justify-between",
        )}
      >
        {!collapsed && (
          <Link href="/admin/dashboard" className="flex items-center gap-2">
            <div className="w-8 h-8 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-lg flex items-center justify-center">
              <span className="text-white font-bold text-sm">S</span>
            </div>
            <span className="font-bold text-gray-200">Admin</span>
          </Link>
        )}

        <Button
          variant="ghost"
          size="icon"
          onClick={() => setCollapsed(!collapsed)}
          className={cn(
            "text-gray-400 hover:text-white transition-colors",
            collapsed ? "w-8 h-8" : "",
          )}
        >
          {collapsed ? <ChevronRight size={18} /> : <ChevronLeft size={18} />}
        </Button>
      </div>

      {/* Navigation */}
      <nav className="flex-1 py-4 px-2 space-y-1 overflow-y-auto">
        {sidebarLinks.map((link) => {
          const isParentActive = pathname.startsWith(link.href);
          const Icon = link.icon;
          const hasSubLinks = link.subLinks && link.subLinks.length > 0;
          const isOpen = openMenus[link.name] || isParentActive;

          if (hasSubLinks && !collapsed) {
            return (
              <div key={link.name} className="space-y-1">
                <button
                  onClick={() => toggleMenu(link.name)}
                  className={cn(
                    "w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg transition-all group text-gray-400 hover:text-white hover:bg-white/5",
                    isParentActive && "text-blue-400 bg-blue-500/5",
                  )}
                >
                  <div className="flex items-center gap-3 overflow-hidden">
                    <Icon size={20} className="flex-shrink-0" />
                    <span className="font-medium text-sm whitespace-nowrap overflow-hidden">
                      {link.name}
                    </span>
                  </div>
                  <ChevronDown
                    size={16}
                    className={cn(
                      "transition-transform duration-200",
                      isOpen && "rotate-180",
                    )}
                  />
                </button>

                {isOpen && (
                  <div className="pl-10 space-y-1">
                    {link.subLinks.map((sub) => {
                      const isSubActive = pathname === sub.href;
                      return (
                        <Link
                          key={sub.name}
                          href={sub.href}
                          className={cn(
                            "block px-3 py-2 rounded-lg text-sm transition-all whitespace-nowrap overflow-hidden",
                            isSubActive
                              ? "text-blue-400 bg-blue-500/10"
                              : "text-gray-500 hover:text-gray-200 hover:bg-white/5",
                          )}
                        >
                          {sub.name}
                        </Link>
                      );
                    })}
                  </div>
                )}
              </div>
            );
          }

          return (
            <Link
              key={link.name}
              href={link.href}
              className={cn(
                "flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group",
                pathname === link.href
                  ? "bg-blue-500/10 text-blue-400"
                  : "text-gray-400 hover:text-white hover:bg-white/5",
                collapsed && "justify-center",
              )}
            >
              <Icon size={20} className="flex-shrink-0" />
              {!collapsed && (
                <span className="font-medium text-sm whitespace-nowrap overflow-hidden">
                  {link.name}
                </span>
              )}
            </Link>
          );
        })}
      </nav>

      {/* User section */}
      <div className="p-4 border-t border-white/10">
        {!collapsed && (
          <p className="text-xs text-gray-400 truncate mb-3">{user?.email}</p>
        )}

        <Button
          variant="ghost"
          onClick={handleSignOut}
          className={cn(
            "w-full flex items-center gap-3 text-gray-400 hover:text-red-400 hover:bg-red-500/10",
            collapsed && "justify-center",
          )}
        >
          <LogOut size={18} />
          {!collapsed && <span>Sign Out</span>}
        </Button>
      </div>
    </aside>
  );
};

export default AdminSidebar;
