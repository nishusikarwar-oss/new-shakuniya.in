"use client";
// ✅ FIX: Replaced hardcoded dummy data with real API calls to GET/POST/PUT/DELETE /api/faqs

import { useState, useEffect } from "react";
import { faqs as faqApi } from "@/lib/api";
import { Loader2, Pencil, Trash2, CheckCircle, XCircle } from "lucide-react";

export default function FaqPage() {
  const [list,       setList]       = useState([]);
  const [loading,    setLoading]    = useState(true);
  const [saving,     setSaving]     = useState(false);
  const [search,     setSearch]     = useState("");
  const [editId,     setEditId]     = useState(null);   // null = add mode
  const [question,   setQuestion]   = useState("");
  const [answer,     setAnswer]     = useState("");
  const [statusMsg,  setStatusMsg]  = useState(null);  // { type, text }

  // ── fetch ──────────────────────────────────────────────────────────────────
  const fetchFaqs = async () => {
    setLoading(true);
    try {
      const res  = await faqApi.list();
      // API returns { success, data: [...], meta } or just array
      const data = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : [];
      setList(data);
    } catch (e) {
      flash("error", e.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchFaqs(); }, []);

  const flash = (type, text) => {
    setStatusMsg({ type, text });
    setTimeout(() => setStatusMsg(null), 3500);
  };

  // ── save (create or update) ───────────────────────────────────────────────
  const handleSave = async () => {
    if (!question.trim() || !answer.trim()) {
      flash("error", "Question and Answer are required.");
      return;
    }
    setSaving(true);
    try {
      if (editId) {
        await faqApi.update(editId, { question, answer });
        flash("success", "FAQ updated successfully.");
      } else {
        await faqApi.create({ question, answer, status: true });
        flash("success", "FAQ created successfully.");
      }
      setQuestion(""); setAnswer(""); setEditId(null);
      fetchFaqs();
    } catch (e) {
      flash("error", e.message);
    } finally {
      setSaving(false);
    }
  };

  // ── delete ────────────────────────────────────────────────────────────────
  const handleDelete = async (id) => {
    if (!confirm("Delete this FAQ?")) return;
    try {
      await faqApi.remove(id);
      flash("success", "FAQ deleted.");
      fetchFaqs();
    } catch (e) { flash("error", e.message); }
  };

  // ── toggle active ─────────────────────────────────────────────────────────
  const handleToggle = async (id) => {
    try {
      const faq = list.find(f => f.id === id);
      if (faq) {
        await faqApi.update(id, { ...faq, is_active: !faq.is_active });
        fetchFaqs();
      }
    } catch (e) { flash("error", e.message); }
  };

  // ── populate edit form ────────────────────────────────────────────────────
  const startEdit = (faq) => {
    setEditId(faq.id);
    setQuestion(faq.question);
    setAnswer(faq.answer);
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const cancelEdit = () => { setEditId(null); setQuestion(""); setAnswer(""); };

  const filtered = list.filter(
    (f) =>
      f.question?.toLowerCase().includes(search.toLowerCase()) ||
      f.answer?.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-slate-950 p-4 md:p-8 text-slate-200">
      <div className="mb-6">
        <h1 className="text-2xl font-semibold text-white">{editId ? "Edit FAQ" : "Add FAQ"}</h1>
        <p className="text-sm text-slate-400 mt-1">Admin / FAQ Management</p>
      </div>

      {statusMsg && (
        <div className={`mb-4 p-3 rounded-lg text-sm ${
          statusMsg.type === "success"
            ? "bg-green-500/20 text-green-400 border border-green-500/30"
            : "bg-red-500/20 text-red-400 border border-red-500/30"
        }`}>
          {statusMsg.text}
        </div>
      )}

      <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {/* ── ADD / EDIT FORM ───────────────────────────────────────────── */}
        <div className="bg-slate-900 border border-slate-800 rounded-xl p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-white">FAQ Details</h2>
            {editId && (
              <span className="text-sm bg-indigo-600 text-white px-3 py-1 rounded">
                FAQ #{list.findIndex(f => f.id === editId) + 1}
              </span>
            )}
          </div>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium mb-1 text-slate-300">
                Question <span className="text-red-500">*</span>
              </label>
              <textarea rows={3} placeholder="Enter Question" value={question}
                onChange={(e) => setQuestion(e.target.value)}
                className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
              <label className="block text-sm font-medium mb-1 text-slate-300">
                Answer <span className="text-red-500">*</span>
              </label>
              <textarea rows={5} placeholder="Enter Answer" value={answer}
                onChange={(e) => setAnswer(e.target.value)}
                className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div className="flex gap-2">
              <button onClick={handleSave} disabled={saving}
                className="flex-1 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 transition text-white py-2 rounded-lg font-medium flex items-center justify-center gap-2">
                {saving && <Loader2 size={16} className="animate-spin" />}
                {editId ? "Update FAQ" : "Save FAQ"}
              </button>
              {editId && (
                <button onClick={cancelEdit}
                  className="px-4 bg-slate-700 hover:bg-slate-600 text-white rounded-lg">
                  Cancel
                </button>
              )}
            </div>
          </div>
        </div>

        {/* ── FAQ LIST ──────────────────────────────────────────────────── */}
        <div className="xl:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6">
          <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
            <h2 className="text-lg font-semibold text-white">FAQ List ({filtered.length})</h2>
            <input type="text" placeholder="Search…" value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full md:w-64" />
          </div>

          <div className="overflow-x-auto">
            {loading ? (
              <div className="py-10 text-center text-slate-400 flex items-center justify-center gap-2">
                <Loader2 size={18} className="animate-spin" /> Loading…
              </div>
            ) : filtered.length === 0 ? (
              <p className="text-center py-8 text-slate-400">No FAQs found.</p>
            ) : (
              <table className="w-full text-sm">
                <thead className="bg-slate-800 text-slate-300">
                  <tr>
                    <th className="px-4 py-3 text-left">#</th>
                    <th className="px-4 py-3 text-left">Question</th>
                    <th className="px-4 py-3 text-left">Answer</th>
                    <th className="px-4 py-3 text-center">Active</th>
                    <th className="px-4 py-3 text-center">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800">
                  {filtered.map((faq, i) => (
                    <tr key={faq.id} className="hover:bg-slate-800/50 transition">
                      <td className="px-4 py-3">{i + 1}</td>
                      <td className="px-4 py-3 max-w-xs">
                        <p className="line-clamp-2">{faq.question}</p>
                      </td>
                      <td className="px-4 py-3 max-w-md">
                        <p className="text-slate-400 text-xs line-clamp-2">{faq.answer}</p>
                      </td>
                      <td className="px-4 py-3 text-center">
                        <button onClick={() => handleToggle(faq.id)} title="Toggle active">
                          {faq.is_active
                            ? <CheckCircle size={18} className="text-green-400 mx-auto" />
                            : <XCircle    size={18} className="text-slate-500 mx-auto" />}
                        </button>
                      </td>
                      <td className="px-4 py-3">
                        <div className="flex justify-center gap-2">
                          <button onClick={() => startEdit(faq)}
                            className="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                            <Pencil size={13} />
                          </button>
                          <button onClick={() => handleDelete(faq.id)}
                            className="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                            <Trash2 size={13} />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
