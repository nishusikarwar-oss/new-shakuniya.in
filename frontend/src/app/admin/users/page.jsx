"use client";
// ✅ FIX: Replaced supabase.from("profiles") with GET /api/users

import { useEffect, useState } from "react";
import { users as usersApi } from "@/lib/api";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input  } from "@/components/ui/input";
import { Badge  } from "@/components/ui/badge";
import { Search, UserPlus, Shield, User, Trash2, Loader2 } from "lucide-react";
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from "@/components/ui/table";

export default function UsersPage() {
  const [list,        setList]        = useState([]);
  const [isLoading,   setIsLoading]   = useState(true);
  const [searchQuery, setSearchQuery] = useState("");
  const [msg,         setMsg]         = useState(null);

  const flash = (type, text) => { setMsg({ type, text }); setTimeout(() => setMsg(null), 3500); };

  const fetchUsers = async () => {
    setIsLoading(true);
    try {
      const res  = await usersApi.list({ per_page: 50 });
      const raw  = res?.data;
      const data = raw?.data ?? (Array.isArray(raw) ? raw : []);
      setList(data);
    } catch (e) {
      flash("error", e.message);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => { fetchUsers(); }, []);

  const handleDelete = async (id) => {
    if (!confirm("Delete this user?")) return;
    try {
      await usersApi.remove(id);
      flash("success", "User deleted.");
      fetchUsers();
    } catch (e) { flash("error", e.message); }
  };

  const filtered = list.filter(
    (u) =>
      u.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
      u.email?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="space-y-6 text-gray-200">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold">Users</h1>
          <p className="text-gray-400 mt-1">Manage user accounts</p>
        </div>
      </div>

      {msg && (
        <div className={`p-3 rounded-lg text-sm ${
          msg.type === "success" ? "bg-green-500/20 text-green-400 border border-green-500/30" : "bg-red-500/20 text-red-400 border border-red-500/30"
        }`}>{msg.text}</div>
      )}

      <Card className="bg-slate-950 border border-white/10">
        <CardHeader className="flex flex-col sm:flex-row sm:items-center gap-4">
          <CardTitle className="text-lg">All Users ({filtered.length})</CardTitle>
          <div className="relative flex-1 max-w-sm ml-auto">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <Input placeholder="Search users…" value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10 bg-slate-900 border-white/10 text-gray-200" />
          </div>
        </CardHeader>

        <CardContent>
          {isLoading ? (
            <div className="text-center py-8 text-gray-400 flex items-center justify-center gap-2">
              <Loader2 size={18} className="animate-spin" /> Loading users…
            </div>
          ) : filtered.length === 0 ? (
            <div className="text-center py-8 text-gray-400">No users found.</div>
          ) : (
            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow className="border-white/10">
                    <TableHead>User</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Role</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Joined</TableHead>
                    <TableHead />
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filtered.map((user) => (
                    <TableRow key={user.id} className="border-white/10 hover:bg-white/5">
                      <TableCell>
                        <div className="flex items-center gap-3">
                          <div className="w-9 h-9 rounded-full bg-gradient-to-r from-purple-500 to-cyan-500 flex items-center justify-center">
                            <span className="text-white text-sm font-medium">
                              {(user.name || user.email || "U").charAt(0).toUpperCase()}
                            </span>
                          </div>
                          <span className="font-medium">{user.name || "—"}</span>
                        </div>
                      </TableCell>
                      <TableCell className="text-gray-400">{user.email}</TableCell>
                      <TableCell>
                        <Badge variant="outline" className={`gap-1 ${
                          user.is_admin
                            ? "bg-purple-500/20 text-purple-400 border-purple-500/30"
                            : "bg-white/5 text-gray-400 border-white/10"
                        }`}>
                          {user.is_admin ? <Shield size={12} /> : <User size={12} />}
                          {user.is_admin ? "Admin" : "User"}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <Badge variant="outline" className={user.is_active
                          ? "bg-green-500/20 text-green-400 border-green-500/30"
                          : "bg-red-500/20 text-red-400 border-red-500/30"}>
                          {user.is_active ? "Active" : "Inactive"}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-gray-400">
                        {user.created_at ? new Date(user.created_at).toLocaleDateString() : "—"}
                      </TableCell>
                      <TableCell>
                        <Button variant="ghost" size="icon" className="hover:bg-red-500/10 text-red-400"
                          onClick={() => handleDelete(user.id)}>
                          <Trash2 size={16} />
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
}
