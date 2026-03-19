"use client";
// ✅ Enquiries page: reads contact form submissions from /api/contact-messages

import { useEffect, useState } from "react";
import { contactMessages as api } from "@/lib/api";
import { Search, Trash2, RefreshCw, Mail, Loader2, Eye } from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

const STATUS_COLOR = {
  unread:  "bg-yellow-500/20 text-yellow-400 border-yellow-500/30",
  read:    "bg-blue-500/20   text-blue-400   border-blue-500/30",
  replied: "bg-green-500/20  text-green-400  border-green-500/30",
};

export default function EnquiriesPage() {
  const [msgs,        setMsgs]        = useState([]);
  const [isLoading,   setIsLoading]   = useState(true);
  const [searchQuery, setSearchQuery] = useState("");
  const [stats,       setStats]       = useState({ total: 0, unread: 0, read: 0, replied: 0 });
  const [msg,         setMsg]         = useState(null);

  const flash = (type, text) => { setMsg({ type, text }); setTimeout(() => setMsg(null), 3500); };

  const fetchMessages = async () => {
    setIsLoading(true);
    try {
      const [listRes, statsRes] = await Promise.all([
        api.list({ per_page: 50 }),
        api.stats(),
      ]);
      const items = listRes?.data?.data ?? listRes?.data ?? [];
      setMsgs(Array.isArray(items) ? items : []);
      setStats(statsRes?.data ?? {});
    } catch (e) {
      flash("error", e.message);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => { fetchMessages(); }, []);

  const markStatus = async (id, status) => {
    try {
      await api.updateStatus(id, status);
      setMsgs((prev) => prev.map((m) => m.id === id ? { ...m, status } : m));
    } catch (e) { flash("error", e.message); }
  };

  const handleDelete = async (id) => {
    if (!confirm("Delete this message?")) return;
    try {
      await api.remove(id);
      flash("success", "Message deleted.");
      fetchMessages();
    } catch (e) { flash("error", e.message); }
  };

  const filtered = msgs.filter(
    (m) =>
      m.name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
      m.email?.toLowerCase().includes(searchQuery.toLowerCase()) ||
      m.message?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="space-y-6 text-gray-200">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl md:text-3xl font-bold">Enquiries</h1>
          <p className="text-gray-400 mt-1">Contact form messages</p>
        </div>
        <Button variant="outline" size="sm" onClick={fetchMessages}
          className="border-white/10 hover:bg-white/5 text-gray-200 flex items-center gap-2">
          <RefreshCw size={14} /> Refresh
        </Button>
      </div>

      {msg && (
        <div className={`p-3 rounded-lg text-sm ${
          msg.type === "success" ? "bg-green-500/20 text-green-400 border border-green-500/30" : "bg-red-500/20 text-red-400 border border-red-500/30"
        }`}>{msg.text}</div>
      )}

      {/* Stats */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {[
          { label: "Total",   val: stats.total,   color: "border-blue-500/30" },
          { label: "Unread",  val: stats.unread,  color: "border-yellow-500/30" },
          { label: "Read",    val: stats.read,    color: "border-cyan-500/30" },
          { label: "Replied", val: stats.replied, color: "border-green-500/30" },
        ].map((s) => (
          <Card key={s.label} className={`bg-slate-900 border ${s.color}`}>
            <CardContent className="pt-4 pb-3 text-center">
              <div className="text-2xl font-bold">{s.val ?? 0}</div>
              <div className="text-xs text-gray-400">{s.label}</div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Table */}
      <Card className="bg-slate-950 border border-white/10">
        <CardHeader className="flex flex-col sm:flex-row sm:items-center gap-4">
          <CardTitle className="text-lg">All Messages</CardTitle>
          <div className="relative flex-1 max-w-sm ml-auto">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <Input placeholder="Search messages…" value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10 bg-slate-900 border-white/10 text-gray-200" />
          </div>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="text-center py-8 flex items-center justify-center gap-2 text-gray-400">
              <Loader2 size={18} className="animate-spin" /> Loading…
            </div>
          ) : filtered.length === 0 ? (
            <p className="text-center py-8 text-gray-400">No messages found.</p>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-white/10 text-gray-400 text-xs uppercase tracking-wider">
                    <th className="px-4 py-3 text-left">Contact</th>
                    <th className="px-4 py-3 text-left">Message</th>
                    <th className="px-4 py-3 text-center">Status</th>
                    <th className="px-4 py-3 text-right">Date</th>
                    <th className="px-4 py-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-white/5">
                  {filtered.map((m) => (
                    <tr key={m.id} className="hover:bg-white/5">
                      <td className="px-4 py-3">
                        <p className="font-medium text-white">{m.name}</p>
                        <div className="flex items-center gap-1 text-xs text-gray-400 mt-0.5">
                          <Mail size={11} /> {m.email}
                        </div>
                      </td>
                      <td className="px-4 py-3 max-w-xs">
                        <p className="text-sm text-gray-300 line-clamp-2">{m.message}</p>
                      </td>
                      <td className="px-4 py-3 text-center">
                        <Badge variant="outline" className={STATUS_COLOR[m.status] || STATUS_COLOR.unread}>
                          {m.status || "unread"}
                        </Badge>
                      </td>
                      <td className="px-4 py-3 text-right text-gray-400 text-xs">
                        {m.created_at ? new Date(m.created_at).toLocaleDateString() : "—"}
                      </td>
                      <td className="px-4 py-3">
                        <div className="flex items-center justify-end gap-1">
                          {m.status === "unread" && (
                            <button onClick={() => markStatus(m.id, "read")} title="Mark as read"
                              className="text-blue-400 hover:text-blue-300 p-1 rounded hover:bg-white/5">
                              <Eye size={14} />
                            </button>
                          )}
                          {m.status !== "replied" && (
                            <button onClick={() => markStatus(m.id, "replied")}
                              className="text-xs text-green-400 hover:text-green-300 px-2 py-1 rounded hover:bg-white/5">
                              Reply
                            </button>
                          )}
                          <button onClick={() => handleDelete(m.id)}
                            className="text-red-400 hover:text-red-300 p-1 rounded hover:bg-white/5">
                            <Trash2 size={14} />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
