"use client";
// ✅ FIX: Replaced Supabase queries with real API calls to /api/services
// Previously used supabase.from("service_categories") which doesn't exist.

import { useState, useEffect } from "react";
import { Loader2, Pencil, Trash2 } from "lucide-react";
import { services as serviceApi } from "@/lib/api";

export default function ServicesAdminPage() {
  const [list,      setList]      = useState([]);
  const [loading,   setLoading]   = useState(true);
  const [saving,    setSaving]    = useState(false);
  const [search,    setSearch]    = useState("");
  const [editId,    setEditId]    = useState(null);
  const [msg,       setMsg]       = useState(null);

  // form
  const [title,     setTitle]     = useState("");
  const [slug,      setSlug]      = useState("");
  const [shortDesc, setShortDesc] = useState("");
  const [isActive,  setIsActive]  = useState(true);
  const [isFeatured,setIsFeatured]= useState(false);

  const flash = (type, text) => { setMsg({ type, text }); setTimeout(() => setMsg(null), 4000); };

  // ── fetch ──────────────────────────────────────────────────────────────────
  const fetchServices = async () => {
    setLoading(true);
    try {
      const res  = await serviceApi.list({ per_page: 50 });
      const raw  = res?.data;
      const data = raw?.data ?? (Array.isArray(raw) ? raw : []);
      setList(data);
    } catch (e) {
      flash("error", e.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchServices(); }, []);

  // ── auto slug ──────────────────────────────────────────────────────────────
  const handleTitleChange = (v) => {
    setTitle(v);
    if (!editId) setSlug(v.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/(^-|-$)/g, ""));
  };

  // ── save ───────────────────────────────────────────────────────────────────
  const handleSave = async (e) => {
    e.preventDefault();
    if (!title.trim()) { flash("error", "Service title is required."); return; }
    setSaving(true);
    try {
      const payload = { title, slug: slug || undefined, short_description: shortDesc, is_active: isActive, is_featured: isFeatured };
      if (editId) {
        await serviceApi.update(editId, payload);
        flash("success", "Service updated.");
      } else {
        await serviceApi.create(payload);
        flash("success", "Service created.");
      }
      resetForm();
      fetchServices();
    } catch (e) {
      flash("error", e.message);
    } finally {
      setSaving(false);
    }
  };

  // ── delete ─────────────────────────────────────────────────────────────────
  const handleDelete = async (id) => {
    if (!confirm("Delete this service?")) return;
    try {
      await serviceApi.remove(id);
      flash("success", "Service deleted.");
      fetchServices();
    } catch (e) { flash("error", e.message); }
  };

  const startEdit = (s) => {
    setEditId(s.id); setTitle(s.title || ""); setSlug(s.slug || "");
    setShortDesc(s.short_description || ""); setIsActive(s.is_active ?? true); setIsFeatured(s.is_featured ?? false);
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const resetForm = () => { setEditId(null); setTitle(""); setSlug(""); setShortDesc(""); setIsActive(true); setIsFeatured(false); };

  const filtered = list.filter((s) => s.title?.toLowerCase().includes(search.toLowerCase()));

  return (
    <div className="min-h-screen bg-slate-950 text-gray-200 p-6 space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-white">{editId ? "Edit Service" : "Add Service"}</h1>
        <p className="text-sm text-slate-400 mt-1">Admin / Service Management</p>
      </div>

      {msg && (
        <div className={`p-3 rounded-lg text-sm ${
          msg.type === "success" ? "bg-green-500/20 text-green-400 border border-green-500/30" : "bg-red-500/20 text-red-400 border border-red-500/30"
        }`}>{msg.text}</div>
      )}

      {/* ── FORM ──────────────────────────────────────────────────────── */}
      <form onSubmit={handleSave} className="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium mb-1 text-slate-300">Service Title *</label>
            <input value={title} onChange={(e) => handleTitleChange(e.target.value)} required
              className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>
          <div>
            <label className="block text-sm font-medium mb-1 text-slate-300">URL Slug</label>
            <input value={slug} onChange={(e) => setSlug(e.target.value)}
              className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>
        </div>
        <div>
          <label className="block text-sm font-medium mb-1 text-slate-300">Short Description</label>
          <textarea rows={3} value={shortDesc} onChange={(e) => setShortDesc(e.target.value)}
            className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        </div>
        <div className="flex gap-6">
          <label className="flex items-center gap-2 text-sm text-slate-300">
            <input type="checkbox" checked={isActive} onChange={(e) => setIsActive(e.target.checked)} className="accent-indigo-500 w-4 h-4" /> Active
          </label>
          <label className="flex items-center gap-2 text-sm text-slate-300">
            <input type="checkbox" checked={isFeatured} onChange={(e) => setIsFeatured(e.target.checked)} className="accent-indigo-500 w-4 h-4" /> Featured
          </label>
        </div>
        <div className="flex justify-end gap-3">
          {editId && (<button type="button" onClick={resetForm} className="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg">Cancel</button>)}
          <button type="submit" disabled={saving}
            className="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white px-8 py-2 rounded-lg flex items-center gap-2">
            {saving && <Loader2 size={16} className="animate-spin" />}
            {editId ? "Update Service" : "Save Service"}
          </button>
        </div>
      </form>

      {/* ── LIST TABLE ────────────────────────────────────────────────── */}
      <div className="bg-slate-900 rounded-xl border border-slate-800 p-6">
        <div className="flex flex-col md:flex-row md:items-center gap-4 mb-4">
          <h2 className="text-lg font-semibold text-white flex-1">Services ({filtered.length})</h2>
          <input placeholder="Search…" value={search} onChange={(e) => setSearch(e.target.value)}
            className="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 w-full md:w-64 focus:outline-none" />
        </div>

        {loading ? (
          <div className="text-center py-8 flex items-center justify-center gap-2 text-slate-400">
            <Loader2 size={18} className="animate-spin" /> Loading…
          </div>
        ) : filtered.length === 0 ? (
          <p className="text-center text-slate-400 py-8">No services found.</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-slate-800 text-slate-300">
                <tr>
                  <th className="px-4 py-3 text-left">#</th>
                  <th className="px-4 py-3 text-left">Title</th>
                  <th className="px-4 py-3 text-left">Slug</th>
                  <th className="px-4 py-3 text-center">Active</th>
                  <th className="px-4 py-3 text-center">Featured</th>
                  <th className="px-4 py-3 text-center">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800">
                {filtered.map((s, i) => (
                  <tr key={s.id} className="hover:bg-slate-800/50">
                    <td className="px-4 py-3">{i + 1}</td>
                    <td className="px-4 py-3 font-medium">{s.title}</td>
                    <td className="px-4 py-3 text-slate-400 text-xs">{s.slug}</td>
                    <td className="px-4 py-3 text-center">
                      <span className={`px-2 py-0.5 rounded text-xs ${s.is_active ? "bg-green-500/20 text-green-400" : "bg-red-500/20 text-red-400"}`}>{s.is_active ? "Yes" : "No"}</span>
                    </td>
                    <td className="px-4 py-3 text-center">
                      <span className={`px-2 py-0.5 rounded text-xs ${s.is_featured ? "bg-purple-500/20 text-purple-400" : "bg-slate-600/40 text-slate-400"}`}>{s.is_featured ? "Yes" : "No"}</span>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex justify-center gap-2">
                        <button onClick={() => startEdit(s)} className="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded"><Pencil size={13} /></button>
                        <button onClick={() => handleDelete(s.id)} className="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded"><Trash2 size={13} /></button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
