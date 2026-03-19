"use client";
// ✅ FIX: Gallery now calls POST /api/gallery-images and DELETE /api/gallery-images/{id}
// Images were previously only stored in local state (lost on page refresh)

import { useState, useRef, useEffect } from "react";
import { Trash2, Eye, ImagePlus, X, Loader2 } from "lucide-react";
import { gallery as galleryApi } from "@/lib/api";

const API_BASE = process.env.NEXT_PUBLIC_API_URL?.replace("/api", "") || "http://127.0.0.1:8000";

export default function GalleryDashboard() {
  const [items,       setItems]       = useState([]);
  const [loading,     setLoading]     = useState(true);
  const [saving,      setSaving]      = useState(false);
  const [image_name,       setName]       = useState("");
  const [categoryId,  setCategoryId]  = useState("1");
  const [imageFile,   setImageFile]   = useState(null);
  const [preview,     setPreview]     = useState(null);
  const [statusMsg,   setStatusMsg]   = useState(null);
  const fileRef = useRef(null);

  const flash = (type, text) => {
    setStatusMsg({ type, text });
    setTimeout(() => setStatusMsg(null), 3500);
  };

  // ── fetch ──────────────────────────────────────────────────────────────────
  const fetchGallery = async () => {
    setLoading(true);
    try {
      const res   = await galleryApi.list({ per_page: 50 });
      const data  = res?.data ?? [];
      setItems(Array.isArray(data) ? data : []);
    } catch (e) {
      flash("error", e.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchGallery(); }, []);

  // ── image select ───────────────────────────────────────────────────────────
  const handleImageChange = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    if (!file.type.startsWith("image/")) { flash("error", "Please select an image file"); return; }
    setImageFile(file);
    setPreview(URL.createObjectURL(file));
  };

  // ── add image ──────────────────────────────────────────────────────────────
  const handleAdd = async () => {
    if (!image_name.trim() || !imageFile) {
      flash("error", "Title and image are required.");
      return;
    }
    setSaving(true);
    try {
      const fd = new FormData();
      fd.append("image_name",  image_name);
      fd.append("category_id", categoryId);
      fd.append("image_url",       imageFile);

      await galleryApi.create(fd);
      flash("success", "Image uploaded successfully.");
      setName(""); setImageFile(null); setPreview(null);
      if (fileRef.current) fileRef.current.value = "";
      fetchGallery();
    } catch (e) {
      flash("error", e.message);
    } finally {
      setSaving(false);
    }
  };

  // ── delete ─────────────────────────────────────────────────────────────────
  const handleDelete = async (id) => {
    if (!confirm("Delete this image?")) return;
    try {
      await galleryApi.remove(id);
      flash("success", "Image deleted.");
      fetchGallery();
    } catch (e) { flash("error", e.message); }
  };

  // ── image URL helper ───────────────────────────────────────────────────────
  const imgSrc = (item) => {
    if (!item.image_url) return null;
    if (item.image_url.startsWith("http")) return item.image_url;
    return `${API_BASE}/storage/${item.image_url}`;
  };

  return (
    <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6 space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-white">Gallery</h1>
      </div>

      {statusMsg && (
        <div className={`p-3 rounded-lg text-sm ${
          statusMsg.type === "success"
            ? "bg-green-500/20 text-green-400 border border-green-500/30"
            : "bg-red-500/20 text-red-400 border border-red-500/30"
        }`}>{statusMsg.text}</div>
      )}

      {/* ── ADD FORM ────────────────────────────────────────────────────── */}
      <div className="bg-[#111827] border border-gray-800 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="space-y-4">
          <div>
            <label className="text-sm text-gray-400">Title *</label>
            <input value={image_name} onChange={(e) => setName(e.target.value)}
              placeholder="Image title"
              className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label className="text-sm text-gray-400">Category ID</label>
            <input value={categoryId} onChange={(e) => setCategoryId(e.target.value)}
              type="number" min="1"
              className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm w-24" />
          </div>
          <button onClick={handleAdd} disabled={saving}
            className="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white px-4 py-2 rounded text-sm flex items-center gap-2">
            {saving && <Loader2 size={14} className="animate-spin" />}
            {saving ? "Uploading…" : "Add to Gallery"}
          </button>
        </div>

        <div className="space-y-3">
          <label className="text-sm text-gray-400">Upload Image *</label>
          <div onClick={() => fileRef.current?.click()}
            className="cursor-pointer h-48 border border-dashed border-gray-600 rounded flex flex-col items-center justify-center text-gray-400 hover:bg-[#0b1220]">
            <ImagePlus className="h-8 w-8 mb-2" />
            <span className="text-sm">Click to upload image</span>
            <input ref={fileRef} type="file" hidden accept="image/*" onChange={handleImageChange} />
          </div>

          {preview && (
            <div className="relative h-40">
              <img src={preview} alt="preview" className="w-full h-full object-cover rounded" />
              <button onClick={() => { setPreview(null); setImageFile(null); if (fileRef.current) fileRef.current.value = ""; }}
                className="absolute top-2 right-2 bg-red-600 p-1 rounded">
                <X size={14} />
              </button>
            </div>
          )}
        </div>
      </div>

      {/* ── GALLERY GRID ────────────────────────────────────────────────── */}
      {loading ? (
        <div className="flex justify-center py-12">
          <Loader2 size={32} className="animate-spin text-indigo-500" />
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          {items.map((item) => (
            <div key={item.id} className="bg-[#111827] border border-gray-800 rounded-lg overflow-hidden">
              {imgSrc(item) ? (
                <img src={imgSrc(item)} alt={item.image_name}
                  className="w-full h-40 object-cover" />
              ) : (
                <div className="w-full h-40 bg-gray-800 flex items-center justify-center text-gray-500 text-sm">
                  No Image
                </div>
              )}
              <div className="p-3 space-y-1">
                <h3 className="font-semibold text-white text-sm">{item.image_name}</h3>
                <div className="flex gap-2 mt-2">
                  <button onClick={() => handleDelete(item.id)}
                    className="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded">
                    <Trash2 size={14} />
                  </button>
                </div>
              </div>
            </div>
          ))}

          {items.length === 0 && (
            <div className="col-span-full text-center text-gray-500 py-10">
              No images yet. Upload one above.
            </div>
          )}
        </div>
      )}
    </div>
  );
}
