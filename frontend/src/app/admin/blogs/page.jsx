"use client";

import { useState, useEffect } from "react";
import { Loader2, Pencil, Trash2 } from "lucide-react";
import { blogs } from "@/lib/api";
import CustomEditor from "../../../components/CustomEditor";

export default function BlogsPage() {

  // ── list state ─────────────────────────────
  const [list, setList] = useState([]);
  const [loadingList, setLoadingList] = useState(true);

  // ── form state ─────────────────────────────
  const [editId, setEditId] = useState(null);
  const [title, setTitle] = useState("");
  const [slug, setSlug] = useState("");
  const [content, setContent] = useState("");
  const [tags, setTags] = useState("");
  const [metaTitle, setMetaTitle] = useState("");
  const [metaDesc, setMetaDesc] = useState("");
  const [status, setStatus] = useState("published");

  const [featuredImage, setFeaturedImage] = useState(null); // File object
  const [preview, setPreview] = useState(null);     // preview URL

  const [saving, setSaving] = useState(false);
  const [msg, setMsg] = useState(null);

  const flash = (type, text) => {
    setMsg({ type, text });
    setTimeout(() => setMsg(null), 4000);
  };

  // ── fetch blogs ─────────────────────────────
  const fetchBlogs = async () => {
    setLoadingList(true);
    try {
      const res = await blogs.list({ per_page: 50 });
      const data = res?.data ?? [];
      setList(Array.isArray(data) ? data : []);
    } catch (e) {
      flash("error", e.message);
    } finally {
      setLoadingList(false);
    }
  };

  useEffect(() => {
    fetchBlogs();
  }, []);

  // ── auto slug ─────────────────────────────
  const handleTitleChange = (val) => {
    setTitle(val);
    if (!editId) {
      const generatedSlug = val
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/(^-|-$)/g, "");
      setSlug(generatedSlug);
    }
  };

  // ── image change ─────────────────────────────
  const handleImageChange = (e) => {
    const file = e.target.files[0];

    if (file) {
      setFeaturedImage(file); // real file
      setPreview(URL.createObjectURL(file)); // preview
    }
  };

  // ── save blog ─────────────────────────────
  const handleSave = async () => {

    if (!title.trim()) {
      flash("error", "Blog title is required.");
      return;
    }

    setSaving(true);

    try {

      const tagsArray = tags
        .split(",")
        .map((tag) => tag.trim())
        .filter(Boolean);

      const formData = new FormData();

      formData.append("title", title);
      formData.append("slug", slug || "");
      formData.append("content", content);
      formData.append("meta_title", metaTitle);
      formData.append("meta_description", metaDesc);
      formData.append("status", status);

      // tags array
      tagsArray.forEach((tag) => {
        formData.append("tags[]", tag);
      });

      // image
      if (featuredImage) {
        formData.append("image", featuredImage);
      }

if (editId !== null) {

  formData.append("_method", "PUT");

  await blogs.update(editId, formData);

  flash("success", "Blog updated successfully.");

} else {

  await blogs.create(formData);

  flash("success", "Blog created successfully.");

}

      resetForm();
      fetchBlogs();

    } catch (e) {
      flash("error", e.message);
    } finally {
      setSaving(false);
    }
  };

  // ── delete ─────────────────────────────
  const handleDelete = async (id) => {

    if (!confirm("Delete this blog?")) return;

    try {
      await blogs.remove(id);
      flash("success", "Blog deleted.");
      fetchBlogs();
    } catch (e) {
      flash("error", e.message);
    }
  };

  // ── edit ─────────────────────────────
  const startEdit = (blog) => {

    setEditId(blog.id);
    setTitle(blog.title || "");
    setSlug(blog.slug || "");
    setContent(blog.content || "");
    setTags(blog.tags || "");
    // setPublishedAt(blog.published_at || "");
    setMetaTitle(blog.meta_title || "");
    setMetaDesc(blog.meta_description || "");
    setStatus(blog.status || "published");

    if (blog.image) {
      setPreview(blog.image);
    }

    window.scrollTo({
      top: 0,
      behavior: "smooth"
    });
  };

  // ── reset form ─────────────────────────────
  const resetForm = () => {

    setEditId(null);
    setTitle("");
    setSlug("");
    setContent("");
    setTags("");
    setMetaTitle("");
    setMetaDesc("");
    setStatus("published");

    setFeaturedImage(null);
    setPreview(null);
  };

  return (

    <div className="min-h-screen bg-slate-950 text-gray-200 p-6">

      <h1 className="text-xl font-semibold mb-2 text-white">
        {editId ? "Edit Blog" : "Add Blog"}
      </h1>

      <p className="text-sm text-slate-400 mb-6">
        Admin / Blog Management
      </p>

      {msg && (
        <div className={`mb-4 p-3 rounded-lg text-sm ${
          msg.type === "success"
            ? "bg-green-500/20 text-green-400 border border-green-500/30"
            : "bg-red-500/20 text-red-400 border border-red-500/30"
        }`}>
          {msg.text}
        </div>
      )}

      {/* FORM */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {/* LEFT */}
        <div className="lg:col-span-2 bg-slate-900 rounded-lg border border-white/10 p-5 space-y-4">

          <div>
            <label className="text-sm font-medium">Blog Title</label>

            <input
              value={title}
              onChange={(e) => handleTitleChange(e.target.value)}
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2"
            />
          </div>

          <div>
            <label className="text-sm font-medium">Slug</label>

            <input
              value={slug}
              onChange={(e) => setSlug(e.target.value)}
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2"
            />
          </div>

          <div>
            <CustomEditor value={content} onChange={setContent} />
          </div>

          <div>
            <label className="text-sm font-medium">Tags</label>

            <input
              value={tags}
              onChange={(e) => setTags(e.target.value)}
              placeholder="seo, marketing, ai"
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2"
            />
          </div>
       {/* ── SEO ──────────────────────────────────────────────────────── */}
      <div className="bg-slate-900 rounded-xl border border-slate-800 p-6 mb-8 space-y-4">
        <h2 className="text-lg font-semibold text-white">SEO Metadata</h2>
        <div>
          <label className="block text-sm font-medium mb-1 text-slate-300">Meta Title</label>
          <input value={metaTitle} onChange={(e) => setMetaTitle(e.target.value)}
            placeholder="50–60 characters"
            className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        </div>
        <div>
          <label className="block text-sm font-medium mb-1 text-slate-300">Meta Description</label>
          <textarea rows={3} value={metaDesc} onChange={(e) => setMetaDesc(e.target.value)}
            placeholder="150–160 characters"
            className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        </div>
      </div>

        </div>

        {/* RIGHT */}
        <div className="bg-slate-900 rounded-lg border border-white/10 p-5 space-y-4">

          <div>

            <label className="text-sm font-medium">
             Featured Image
            </label>

            <input
              type="file"
              accept="image/*"
              className="block mt-2 text-sm"
              onChange={handleImageChange}
            />

            <div className="mt-3 w-40 h-28 border border-white/10 flex items-center justify-center bg-slate-950 rounded overflow-hidden">

              {preview ? (
                <img
                  src={preview}
                  className="object-cover w-full h-full"
                />
              ) : (
                "PREVIEW"
              )}

            </div>

          </div>

          <div>

            <label className="text-sm font-medium">
              Status
            </label>

            <div className="flex gap-4 mt-2">

              {["published", "draft"].map((s) => (

                <label key={s} className="flex gap-2">

                  <input
                    type="radio"
                    checked={status === s}
                    onChange={() => setStatus(s)}
                  />

                  {s}

                </label>

              ))}

            </div>

          </div>

        </div>

      </div>

      {/* SAVE BUTTON */}

      <div className="flex justify-end gap-3 mb-12">

        {editId && (
          <button
            onClick={resetForm}
            className="px-6 py-2 bg-slate-700 rounded"
          >
            Cancel
          </button>
        )}

        <button
          onClick={handleSave}
          disabled={saving}
          className="bg-indigo-600 px-8 py-2 rounded flex items-center gap-2"
        >

          {saving && <Loader2 size={16} className="animate-spin" />}

          {editId ? "Update Blog" : "Save Blog"}

        </button>

      </div>

      {/* BLOG TABLE */}

      <div className="bg-slate-900 rounded-xl border border-slate-800 p-6">

        <h2 className="text-lg font-semibold text-white mb-4">
          All Blogs ({list.length})
        </h2>

        {loadingList ? (

          <div className="text-center py-8">
            Loading...
          </div>

        ) : (

          <table className="w-full text-sm">

            <thead className="bg-slate-800 text-center">
              <tr>
                <th className="px-4 py-3">#</th>
                <th className="px-4 py-3">Title</th>
                <th className="px-4 py-3">Slug</th>
                <th className="px-4 py-3">Status</th>
                <th className="px-4 py-3">Actions</th>
              </tr>
            </thead>

            <tbody>

              {list.map((blog, i) => (

                <tr key={blog.id} className="text-center">

                  <td className="px-4 py-3">
                    {i + 1}
                  </td>

                  <td className="px-4 py-3">
                    {blog.title}
                  </td>

                  <td className="px-4 py-3">
                    {blog.slug}
                  </td>

                  <td className="px-4 py-3">
                    {blog.status}
                  </td>

                  <td className="px-4 py-3 flex gap-2">

                    <button
                      onClick={() => startEdit(blog)}
                      className="bg-blue-600 p-1 rounded"
                    >
                      <Pencil size={13} />
                    </button>

                    <button
                      onClick={() => handleDelete(blog.id)}
                      className="bg-red-600 p-1 rounded"
                    >
                      <Trash2 size={13} />
                    </button>

                  </td>

                </tr>

              ))}

            </tbody>

          </table>

        )}

      </div>

    </div>
  );
}