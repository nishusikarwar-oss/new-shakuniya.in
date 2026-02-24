"use client";

import { useEffect, useState } from "react";
import { supabase } from "@/integrations/supabase/client";
import {
  Users,
  Mail,
  TrendingUp,
  Eye,
  ArrowUpRight,
  ArrowDownRight,
} from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

const Dashboard = () => {
  const [stats, setStats] = useState({
    totalUsers: 0,
    // totalEnquiries: 0,
    // pendingEnquiries: 0,
    recentActivity: 0,
  });
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {

        const { count: usersCount } = await supabase
          .from("users")
          .select("*", { count: "exact", head: true });

        const { count: activityCount } = await supabase
          .from("activity_logs")
          .select("*", { count: "exact", head: true });

        setStats({
          totalUsers: usersCount || 0,
          recentActivity: activityCount || 0,
        });
      } catch (error) {
        console.error("Dashboard stats error:", error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchStats();
  }, []);

  const statCards = [
    {
      title: "Total Users",
      value: stats.totalUsers,
      icon: Users,
      change: "+12%",
      trend: "up",
      color: "from-purple-500 to-purple-600",
    },
    {
      title: "E-mail Messages",
      value: stats.messages,
      icon: Mail,
      change: "+8%",
      trend: "up",
      color: "from-cyan-500 to-cyan-600",
    },
    {
      title: "Views",
      value: stats.views,
      icon: Eye,
      change: "-3%",
      trend: "down",
      color: "from-orange-500 to-orange-600",
    },
    {
      title: "Activity Logs",
      value: stats.recentActivity,
      icon: TrendingUp,
      change: "+15%",
      trend: "up",
      color: "from-green-500 to-green-600",
    },
  ];

  return (
    <div className="space-y-6 text-gray-200">
      {/* Header */}
      <div>
        <h1 className="text-2xl md:text-3xl font-bold">Dashboard</h1>
        <p className="text-gray-400 mt-1">
          Welcome back! Here's an overview of your site.
        </p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {statCards.map((stat, index) => {
          const Icon = stat.icon;
          return (
            <Card
              key={index}
              className="bg-slate-950 border border-white/10 hover:border-blue-500/40 transition"
            >
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm text-gray-400">
                  {stat.title}
                </CardTitle>
                <div
                  className={`w-10 h-10 rounded-lg bg-gradient-to-r ${stat.color} flex items-center justify-center`}
                >
                  <Icon className="w-5 h-5 text-white" />
                </div>
              </CardHeader>

              <CardContent>
                <div className="flex items-end justify-between">
                  <div className="text-3xl font-bold">
                    {isLoading ? "—" : stat.value}
                  </div>

                  <div
                    className={`flex items-center text-sm ${
                      stat.trend === "up" ? "text-green-400" : "text-red-400"
                    }`}
                  >
                    {stat.trend === "up" ? (
                      <ArrowUpRight size={16} />
                    ) : (
                      <ArrowDownRight size={16} />
                    )}
                    {stat.change}
                  </div>
                </div>
              </CardContent>
            </Card>
          );
        })}
      </div>
    </div>
  );
};

export default Dashboard;
