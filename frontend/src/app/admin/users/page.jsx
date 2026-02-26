"use client";

import { useEffect, useState } from "react";
import { supabase } from "@/integrations/supabase/client";
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Search, UserPlus, MoreVertical, Shield, User } from "lucide-react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";

const Users = () => {
  const [users, setUsers] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    const fetchUsers = async () => {
      try {
        const { data: profiles, error } = await supabase
          .from("profiles")
          .select("*")
          .order("created_at", { ascending: false });

        if (error) throw error;

        const usersWithRoles = await Promise.all(
          (profiles || []).map(async (profile) => {
            const { data: roleData } = await supabase
              .from("user_roles")
              .select("role")
              .eq("user_id", profile.user_id)
              .maybeSingle();

            return {
              ...profile,
              role: roleData?.role || "user",
            };
          })
        );

        setUsers(usersWithRoles);
      } catch (error) {
        console.error("Fetch users error:", error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchUsers();
  }, []);

  const filteredUsers = users.filter(
    (u) =>
      u.email?.toLowerCase().includes(searchQuery.toLowerCase()) ||
      u.full_name?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="space-y-6 text-gray-200">

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold">
            Users
          </h1>
          <p className="text-gray-400 mt-1">
            Manage user accounts and permissions
          </p>
        </div>

        <Button className="gap-2 bg-gradient-to-r from-purple-500 to-cyan-500 text-white">
          <UserPlus size={18} />
          Add User
        </Button>
      </div>

      {/* Users Table */}
      <Card className="bg-slate-950 border border-white/10">
        <CardHeader className="flex flex-col sm:flex-row sm:items-center gap-4">
          <CardTitle className="text-lg">
            All Users
          </CardTitle>

          <div className="relative flex-1 max-w-sm ml-auto">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <Input
              placeholder="Search users..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10 bg-slate-900 border-white/10 text-gray-200"
            />
          </div>
        </CardHeader>

        <CardContent>
          {isLoading ? (
            <div className="text-center py-8 text-gray-400">
              Loading users...
            </div>
          ) : filteredUsers.length === 0 ? (
            <div className="text-center py-8 text-gray-400">
              No users found.
            </div>
          ) : (
            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow className="border-white/10">
                    <TableHead>User</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Role</TableHead>
                    <TableHead>Joined</TableHead>
                    <TableHead />
                  </TableRow>
                </TableHeader>

                <TableBody>
                  {filteredUsers.map((user) => (
                    <TableRow
                      key={user.id}
                      className="border-white/10 hover:bg-white/5"
                    >
                      <TableCell>
                        <div className="flex items-center gap-3">
                          <div className="w-9 h-9 rounded-full bg-gradient-to-r from-purple-500 to-cyan-500 flex items-center justify-center">
                            <span className="text-white text-sm font-medium">
                              {user.full_name?.charAt(0) ||
                                user.email?.charAt(0)?.toUpperCase() ||
                                "U"}
                            </span>
                          </div>
                          <span className="font-medium">
                            {user.full_name || "No name"}
                          </span>
                        </div>
                      </TableCell>

                      <TableCell className="text-gray-400">
                        {user.email}
                      </TableCell>

                      <TableCell>
                        <Badge
                          variant="outline"
                          className={`gap-1 ${
                            user.role === "admin"
                              ? "bg-purple-500/20 text-purple-400 border-purple-500/30"
                              : "bg-white/5 text-gray-400 border-white/10"
                          }`}
                        >
                          {user.role === "admin" ? (
                            <Shield size={12} />
                          ) : (
                            <User size={12} />
                          )}
                          {user.role}
                        </Badge>
                      </TableCell>

                      <TableCell className="text-gray-400">
                        {new Date(user.created_at).toLocaleDateString()}
                      </TableCell>

                      <TableCell>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="hover:bg-white/5"
                        >
                          <MoreVertical size={16} />
                        </Button>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default Users;
