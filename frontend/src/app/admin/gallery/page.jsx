"use client";

import { useState, useRef } from "react";
import { Upload, Trash2, Eye, ImagePlus, X } from "lucide-react";


export default function GalleryDashboard() {
  const [gallery, setGallery] = useState([]);
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  const [imageFile, setImageFile] = useState(null);
  const [preview, setPreview] = useState(null);

  const fileRef = useRef(null);

  /* ---------------- Image Select ---------------- */
  const handleImageChange = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (!file.type.startsWith("image/")) {
      alert("Please select an image file");
      return;
    }

    setImageFile(file);
    setPreview(URL.createObjectURL(file));
  };

  /* ---------------- Add to Gallery ---------------- */
  const handleAdd = () => {
    if (!title || !imageFile) {
      alert("Title and Image are required");
      return;
    }

    const newItem = {
      id: Date.now(),
      title,
      description,
      image: preview,
    };

    setGallery((prev) => [newItem, ...prev]);
    setTitle("");
    setDescription("");
    setImageFile(null);
    setPreview(null);
    fileRef.current.value = "";
  };

  /* ---------------- Delete ---------------- */
  const handleDelete = (id) => {
    if (!confirm("Delete this image?")) return;
    setGallery((prev) => prev.filter((g) => g.id !== id));
  };

  /* ---------------- View ---------------- */
  const handleView = (item) => {
    alert(
      `TITLE: ${item.title}\n\nDESCRIPTION:\n${item.description || "N/A"}`
    );
  };

  return (
    <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6 space-y-6">
      {/* ================= HEADER ================= */}
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-white">Gallery Dashboard</h1>
      </div>

      {/* ================= ADD FORM ================= */}
      <div className="bg-[#111827] border border-gray-800 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* LEFT */}
        <div className="space-y-4">
          <div>
            <label className="text-sm text-gray-400">Title *</label>
            <input
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="Image title"
              className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm"
            />
          </div>

          <div>
            <label className="text-sm text-gray-400">Description</label>
            <textarea
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              placeholder="Image description"
              rows={4}
              className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm"
            />
          </div>

          <button
            onClick={handleAdd}
            className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm w-fit"
          >
            Add to Gallery
          </button>
        </div>

        {/* RIGHT */}
        <div className="space-y-3">
          <label className="text-sm text-gray-400">Upload Image *</label>

          <div
            onClick={() => fileRef.current.click()}
            className="cursor-pointer h-48 border border-dashed border-gray-600 rounded flex flex-col items-center justify-center text-gray-400 hover:bg-[#0b1220]"
          >
            <ImagePlus className="h-8 w-8 mb-2" />
            <span className="text-sm">Click to upload image</span>
            <input
              ref={fileRef}
              type="file"
              hidden
              accept="image/*"
              onChange={handleImageChange}
            />
          </div>

          {preview && (
            <div className="relative h-40">
              <img
                src={preview}
                alt="preview"
                className="w-full h-full object-cover rounded"
              />
              <button
                onClick={() => {
                  setPreview(null);
                  setImageFile(null);
                  fileRef.current.value = "";
                }}
                className="absolute top-2 right-2 bg-red-600 p-1 rounded"
              >
                <X size={14} />
              </button>
            </div>
          )}
        </div>
      </div>

      {/* ================= GALLERY GRID ================= */}
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        {gallery.map((item) => (
          <div
            key={item.id}
            className="bg-[#111827] border border-gray-800 rounded-lg overflow-hidden"
          >
            <img
              src={item.image}
              alt={item.title}
              className="w-full h-40 object-cover"
            />

            <div className="p-3 space-y-1">
              <h3 className="font-semibold text-white text-sm">
                {item.title}
              </h3>
              <p className="text-xs text-gray-400 line-clamp-2">
                {item.description || "No description"}
              </p>

              <div className="flex gap-2 mt-2">
                <button
                  onClick={() => handleView(item)}
                  className="bg-green-600 hover:bg-green-700 text-white p-1.5 rounded"
                >
                  <Eye size={14} />
                </button>
                <button
                  onClick={() => handleDelete(item.id)}
                  className="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded"
                >
                  <Trash2 size={14} />
                </button>
              </div>
            </div>
          </div>
        ))}

        {gallery.length === 0 && (
          <div className="col-span-full text-center text-gray-500 py-10">
            No images added yet
          </div>
        )}
      </div>
    </div>
  );
}
