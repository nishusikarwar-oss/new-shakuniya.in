"use client";
// ✅ FIX: Replaced Supabase queries with GET /api/admin/dashboard

import { useEffect, useState } from "react";
import { Users, Mail, TrendingUp, Eye, ArrowUpRight, ArrowDownRight, Package, FileText, Image, HelpCircle, Briefcase } from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { dashboard } from "@/lib/api";

const ICON_MAP = {
  users: Users, package: Package, "file-text": FileText,
  image: Image, "help-circle": HelpCircle, briefcase: Briefcase,
  mail: Mail, activity: TrendingUp, eye: Eye,
};
const COLOR_MAP = {
  primary: "from-blue-500 to-blue-600",   success: "from-green-500 to-green-600",
  warning: "from-yellow-500 to-yellow-600", info:  "from-cyan-500 to-cyan-600",
  danger:  "from-red-500 to-red-600",     purple: "from-purple-500 to-purple-600",
  emerald: "from-emerald-500 to-emerald-600", rose: "from-rose-500 to-rose-600",
};

export default function Dashboard() {
  const [statsCards,  setStatsCards]  = useState([]);
  const [activities,  setActivities]  = useState([]);
  const [isLoading,   setIsLoading]   = useState(true);
  const [error,       setError]       = useState(null);

  useEffect(() => {
    dashboard.getData()
      .then((res) => {
        setStatsCards(res?.data?.stats_cards       || []);
        setActivities(res?.data?.recent_activities || []);
      })
      .catch((e) => setError(e.message))
      .finally(() => setIsLoading(false));
  }, []);

  const fallback = [
    { title: "Total Users",    icon: "users",     color: "primary", value: 0 },
    { title: "Products",       icon: "package",   color: "success", value: 0 },
    { title: "Blogs",          icon: "file-text", color: "warning", value: 0 },
    { title: "Services",       icon: "eye",       color: "info",    value: 0 },
  ];

  const cards = isLoading ? fallback : (statsCards.length ? statsCards : fallback);

  return (
    <div className="space-y-6 text-gray-200">
      <div>
        <h1 className="text-2xl md:text-3xl font-bold">Dashboard</h1>
        <p className="text-gray-400 mt-1">Welcome back! Here's an overview of your site.</p>
      </div>

      {error && (
        <div className="bg-red-500/10 border border-red-500/30 rounded-lg p-4 text-red-400 text-sm">
          ⚠ {error} — make sure the Laravel backend is running at{" "}
          <code className="bg-red-500/10 px-1 rounded">
            {process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000"}
          </code>
        </div>
      )}

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {cards.map((stat, i) => {
          const Icon  = ICON_MAP[stat.icon]  || TrendingUp;
          const color = COLOR_MAP[stat.color] || COLOR_MAP.primary;
          const up    = !stat.growth || stat.growth.startsWith("+");

          return (
            <Card key={i} className="bg-slate-950 border border-white/10 hover:border-blue-500/40 transition">
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm text-gray-400">{stat.title}</CardTitle>
                <div className={`w-10 h-10 rounded-lg bg-gradient-to-r ${color} flex items-center justify-center`}>
                  <Icon className="w-5 h-5 text-white" />
                </div>
              </CardHeader>
              <CardContent>
                <div className="flex items-end justify-between">
                  <div className="text-3xl font-bold">{isLoading ? "—" : (stat.value ?? 0)}</div>
                  {stat.growth ? (
                    <div className={`flex items-center text-sm ${up ? "text-green-400" : "text-red-400"}`}>
                      {up ? <ArrowUpRight size={16} /> : <ArrowDownRight size={16} />}
                      {stat.growth}
                    </div>
                  ) : (
                    <span className="text-xs text-gray-500">{stat.subtext}</span>
                  )}
                </div>
              </CardContent>
            </Card>
          );
        })}
      </div>

      {!isLoading && activities.length > 0 && (
        <div>
          <h2 className="text-lg font-semibold mb-3">Recent Activity</h2>
          <div className="bg-slate-900/60 border border-white/10 rounded-xl divide-y divide-white/5">
            {activities.slice(0, 8).map((a, i) => (
              <div key={i} className="flex items-start gap-3 px-4 py-3 hover:bg-white/5">
                <div className="w-2 h-2 rounded-full bg-purple-400 mt-2 shrink-0" />
                <div className="flex-1 min-w-0">
                  <p className="text-sm text-gray-200 truncate">
                    <span className="text-gray-400">{a.action}:</span> {a.details}
                  </p>
                </div>
                <span className="text-xs text-gray-500 shrink-0">{a.time}</span>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
