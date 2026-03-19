"use client";
// ✅ FIX: Perks and Jobs now call real API endpoints instead of local state only.
// Data is persisted to the database and survives page refresh.

import { useState, useEffect } from "react";
import { Loader2, Trash2, Plus } from "lucide-react";
import { perks as perksApi, jobs as jobsApi } from "@/lib/api";

const iconOptions = ["FaHeartbeat","FaCalendarAlt","FaGift","FaMoneyBillWave","FaUserShield","FaUsers","FaStar","FaHandsHelping"];

export default function CareerAdminPage() {
  const [msg, setMsg] = useState(null);
  const flash = (type, text) => { setMsg({ type, text }); setTimeout(() => setMsg(null), 4000); };

  /* ===================== PERKS ===================== */
  const [perks,      setPerks]      = useState([]);
  const [loadPerks,  setLoadPerks]  = useState(true);
  const [savePerk,   setSavePerk]   = useState(false);
  const [perkForm,   setPerkForm]   = useState({ title: "", icon: "FaGift" });

  const fetchPerks = async () => {
    setLoadPerks(true);
    try {
      const res = await perksApi.list();
      const raw = res?.data;
      setPerks(raw?.data ?? (Array.isArray(raw) ? raw : []));
    } catch (e) { flash("error", e.message); }
    finally { setLoadPerks(false); }
  };

  useEffect(() => { fetchPerks(); }, []);

  const addPerk = async () => {
    if (!perkForm.title.trim()) { flash("error", "Perk title is required."); return; }
    setSavePerk(true);
    try {
      await perksApi.create({ title: perkForm.title, icon: perkForm.icon, is_active: true });
      flash("success", "Perk added.");
      setPerkForm({ title: "", icon: "FaGift" });
      fetchPerks();
    } catch (e) { flash("error", e.message); }
    finally { setSavePerk(false); }
  };

  const deletePerk = async (id) => {
    if (!confirm("Delete this perk?")) return;
    try { await perksApi.remove(id); flash("success", "Perk deleted."); fetchPerks(); }
    catch (e) { flash("error", e.message); }
  };

  /* ===================== JOBS ===================== */
  const [jobs,      setJobs]      = useState([]);
  const [loadJobs,  setLoadJobs]  = useState(true);
  const [saveJob,   setSaveJob]   = useState(false);
  const [jobForm,   setJobForm]   = useState({
    title: "", description: "", experience_required: "", positions_available: "",
    qualification: "", location: "",
  });

  const fetchJobs = async () => {
    setLoadJobs(true);
    try {
      const res  = await jobsApi.list({ per_page: 50 });
      const raw  = res?.data;
      setJobs(raw?.data ?? (Array.isArray(raw) ? raw : []));
    } catch (e) { flash("error", e.message); }
    finally { setLoadJobs(false); }
  };

  useEffect(() => { fetchJobs(); }, []);

  const addJob = async () => {
    if (!jobForm.title.trim() || !jobForm.description.trim() || !jobForm.location.trim()) {
      flash("error", "Title, Description, and Location are required.");
      return;
    }
    setSaveJob(true);
    try {
      await jobsApi.create({
        title:                  jobForm.title,
        description:            jobForm.description,
        experience_required:    jobForm.experience_required,
        positions_available:    Number(jobForm.positions_available) || 1,
        qualification:          jobForm.qualification,
        location:               jobForm.location,
        is_active:              true,
      });
      flash("success", "Job opening added.");
      setJobForm({ title: "", description: "", experience_required: "", positions_available: "", qualification: "", location: "" });
      fetchJobs();
    } catch (e) { flash("error", e.message); }
    finally { setSaveJob(false); }
  };

  const deleteJob = async (id) => {
    if (!confirm("Delete this job?")) return;
    try { await jobsApi.remove(id); flash("success", "Job deleted."); fetchJobs(); }
    catch (e) { flash("error", e.message); }
  };

  /* ===================== UI ===================== */
  return (
    <div className="p-8 bg-[#0b0f1a] min-h-screen text-white space-y-16">
      <h1 className="text-2xl font-bold">Career Admin</h1>

      {msg && (
        <div className={`p-3 rounded-lg text-sm ${
          msg.type === "success" ? "bg-green-500/20 text-green-400 border border-green-500/30" : "bg-red-500/20 text-red-400 border border-red-500/30"
        }`}>{msg.text}</div>
      )}

      {/* ─── PERKS ─────────────────────────────────────────────────── */}
      <section>
        <h2 className="text-xl font-bold mb-6">Perks & Benefits</h2>

        <div className="bg-[#0f1525] p-4 rounded-lg mb-6 flex flex-wrap gap-3 items-end">
          <div>
            <label className="text-xs text-gray-400 mb-1 block">Title *</label>
            <input type="text" placeholder="Perk title" value={perkForm.title}
              onChange={(e) => setPerkForm({ ...perkForm, title: e.target.value })}
              className="bg-[#05080f] border border-white/10 px-3 py-2 rounded w-64 text-sm" />
          </div>
          <div>
            <label className="text-xs text-gray-400 mb-1 block">Icon</label>
            <select value={perkForm.icon} onChange={(e) => setPerkForm({ ...perkForm, icon: e.target.value })}
              className="bg-[#05080f] border border-white/10 px-3 py-2 rounded text-sm">
              {iconOptions.map((icon) => (<option key={icon} value={icon}>{icon}</option>))}
            </select>
          </div>
          <button onClick={addPerk} disabled={savePerk}
            className="bg-purple-600 px-5 py-2 rounded hover:bg-purple-700 transition flex items-center gap-2 disabled:opacity-50 text-sm">
            {savePerk && <Loader2 size={14} className="animate-spin" />}
            <Plus size={14} /> Add Perk
          </button>
        </div>

        <div className="overflow-x-auto bg-[#0f1525] rounded-lg">
          {loadPerks ? (
            <div className="p-8 text-center text-gray-400 flex items-center justify-center gap-2">
              <Loader2 size={18} className="animate-spin" /> Loading…
            </div>
          ) : (
            <table className="w-full border-collapse text-sm">
              <thead>
                <tr className="bg-[#05080f] text-gray-300">
                  <th className="p-3 text-left">#</th>
                  <th className="p-3 text-left">Title</th>
                  <th className="p-3 text-left">Icon</th>
                  <th className="p-3 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                {(Array.isArray(perks) ? perks : []).map((p, i) => (
                  <tr key={p.id} className="border-b border-white/5 hover:bg-white/5">
                    <td className="p-3">{i + 1}</td>
                    <td className="p-3">{p.title}</td>
                    <td className="p-3 text-purple-400">{p.icon}</td>
                    <td className="p-3 text-center">
                      <button onClick={() => deletePerk(p.id)} className="bg-red-600 px-3 py-1 rounded text-xs hover:bg-red-700 flex items-center gap-1 mx-auto">
                        <Trash2 size={12} /> Delete
                      </button>
                    </td>
                  </tr>
                ))}
                {(Array.isArray(perks) ? perks : []).length === 0 && (
                  <tr><td colSpan="4" className="text-center p-6 text-gray-400">No perks yet.</td></tr>
                )}
              </tbody>
            </table>
          )}
        </div>
      </section>

      {/* ─── JOBS ──────────────────────────────────────────────────── */}
      <section>
        <h2 className="text-xl font-bold mb-6">Job Openings</h2>

        <div className="bg-[#0f1525] p-4 rounded-lg mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
          {[
            { label: "Job Title *", key: "title", type: "text" },
            { label: "Experience",  key: "experience_required", type: "text" },
            { label: "Positions",   key: "positions_available", type: "number" },
            { label: "Qualification", key: "qualification", type: "text" },
            { label: "Location",    key: "location", type: "text" },
          ].map(({ label, key, type }) => (
            <div key={key}>
              <label className="text-xs text-gray-400 mb-1 block">{label}</label>
              <input type={type} placeholder={label.replace(" *", "")} value={jobForm[key]}
                onChange={(e) => setJobForm({ ...jobForm, [key]: e.target.value })}
                className="bg-[#05080f] border border-white/10 px-3 py-2 rounded w-full text-sm" />
            </div>
          ))}
          <div className="md:col-span-3">
            <label className="text-xs text-gray-400 mb-1 block">Job Description *</label>
            <textarea placeholder="Job description…" value={jobForm.description}
              onChange={(e) => setJobForm({ ...jobForm, description: e.target.value })}
              className="bg-[#05080f] border border-white/10 px-3 py-2 rounded w-full text-sm" rows={3} />
          </div>
          <button onClick={addJob} disabled={saveJob}
            className="md:col-span-3 bg-purple-600 px-6 py-2 rounded hover:bg-purple-700 transition flex items-center justify-center gap-2 disabled:opacity-50 text-sm">
            {saveJob && <Loader2 size={14} className="animate-spin" />}
            <Plus size={14} /> Add Job
          </button>
        </div>

        <div className="overflow-x-auto bg-[#0f1525] rounded-lg">
          {loadJobs ? (
            <div className="p-8 text-center text-gray-400 flex items-center justify-center gap-2">
              <Loader2 size={18} className="animate-spin" /> Loading…
            </div>
          ) : (
            <table className="w-full border-collapse text-sm">
              <thead>
                <tr className="bg-[#05080f] text-gray-300">
                  <th className="p-3 text-left">#</th>
                  <th className="p-3 text-left">Title</th>
                  <th className="p-3 text-left">Experience</th>
                  <th className="p-3 text-left">Positions</th>
                  <th className="p-3 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                {jobs.map((j, i) => (
                  <tr key={j.id} className="border-b border-white/5 hover:bg-white/5">
                    <td className="p-3">{i + 1}</td>
                    <td className="p-3 font-medium">{j.title}</td>
                    <td className="p-3 text-gray-400">{j.experience_required || "—"}</td>
                    <td className="p-3 text-gray-400">{j.positions_available ?? "—"}</td>
                    <td className="p-3 text-center">
                      <button onClick={() => deleteJob(j.id)} className="bg-red-600 px-3 py-1 rounded text-xs hover:bg-red-700 flex items-center gap-1 mx-auto">
                        <Trash2 size={12} /> Delete
                      </button>
                    </td>
                  </tr>
                ))}
                {jobs.length === 0 && (
                  <tr><td colSpan="5" className="text-center p-6 text-gray-400">No jobs yet.</td></tr>
                )}
              </tbody>
            </table>
          )}
        </div>
      </section>
    </div>
  );
}
