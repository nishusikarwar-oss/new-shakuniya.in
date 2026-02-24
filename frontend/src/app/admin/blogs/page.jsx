"use client";

import { useState } from "react";
import CustomEditor from "@/components/CustomEditor";

export default function AddBlogPage() {

  const [thumbnail, setThumbnail] = useState(null);
  const [featured, setFeatured] = useState(null);

   const [status, setStatus] = useState("active");
  const [ogImage, setOgImage] = useState(null);
  const [twitterImage, setTwitterImage] = useState(null);
  const [tab, setTab] = useState("openGraph");

  const previewImage = (file) => {
    if (!file) return null;
    return URL.createObjectURL(file);
  };

  return (
    <div className="min-h-screen bg-slate-950 text-gray-200 p-6">
      <h1 className="text-xl font-semibold mb-6 text-white">Add Blog</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* LEFT: Blog Details */}
        <div className="lg:col-span-2 bg-slate-900 rounded-lg border border-white/10 p-5">
          <h2 className="font-medium mb-4 text-white">Blog Details</h2>

          {/* Blog Title */}
          <div className="mb-4">
            <label className="text-sm font-medium">
              Blog Title<span className="text-red-400">*</span>
            </label>
            <input
              type="text"
              placeholder="Enter Blog Title"
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          {/* URL Slug */}
          <div className="mb-4">
            <label className="text-sm font-medium">URL Slug</label>
            <input
              type="text"
              placeholder="Enter URL Slug (e.g., my-blog-post)"
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p className="text-xs text-gray-400 mt-1">
              SEO-friendly URL. If left empty, will be generated from title.
            </p>
          </div>

          {/* Blog Description */}
          <div className="mb-4">
          
        <h1 className="text-xl font-bold mb-2 mt-6 bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400 bg-clip-text text-transparent">
          Blog Description
        </h1>
      <div className="w-full max-w-5xl bg-[#111118] border border-white/10 rounded-2xl shadow-2xl">
        <CustomEditor />
      </div>
          </div>

          {/* Tags */}
          <div>
            <label className="text-sm font-medium">
              Tags<span className="text-red-400">*</span>
            </label>
            <input
              type="text"
              placeholder="Enter comma separated tags"
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p className="text-xs text-gray-400 mt-1">
              Maximum of 15 keywords. Use lowercase.
            </p>
          </div>
        </div>

        {/* RIGHT: Blog Images */}
        <div className="bg-slate-900 rounded-lg border border-white/10 p-5">
          <h2 className="font-medium mb-4 text-white">Blog Images</h2>

          {/* Thumbnail Image */}
          <div className="mb-6">
            <label className="text-sm font-medium">
              Thumbnail Image{" "}
              <span className="text-red-400">(300 × 250px)*</span>
            </label>

            <input
              type="file"
              className="block mt-2 text-sm text-gray-400"
              onChange={(e) =>
                setThumbnail(URL.createObjectURL(e.target.files[0]))
              }
            />

            <p className="text-xs text-gray-400 mt-1">
              Appears in blog listings
            </p>

            <div className="mt-3 w-40 h-28 border border-white/10 flex items-center justify-center text-xs text-gray-500 bg-slate-950 rounded">
              {thumbnail ? (
                <img
                  src={thumbnail}
                  className="object-cover w-full h-full rounded"
                />
              ) : (
                "IMAGE PREVIEW"
              )}
            </div>
          </div>

          {/* Alt Text */}
          <div className="mb-6">
            <label className="text-sm font-medium">Image Alt Text</label>
            <input
              type="text"
              placeholder="Describe the image for accessibility"
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p className="text-xs text-gray-400 mt-1">
              Important for SEO & accessibility
            </p>
          </div>

          {/* Featured Image */}
          <div className="mb-6">
            <label className="text-sm font-medium">
              Featured Image{" "}
              <span className="text-red-400">(925 × 661px)*</span>
            </label>

            <input
              type="file"
              className="block mt-2 text-sm text-gray-400"
              onChange={(e) =>
                setFeatured(URL.createObjectURL(e.target.files[0]))
              }
            />

            <p className="text-xs text-gray-400 mt-1">
              Appears at top of blog post
            </p>

            <div className="mt-3 w-full h-36 border border-white/10 flex items-center justify-center text-xs text-gray-500 bg-slate-950 rounded">
              {featured ? (
                <img
                  src={featured}
                  className="object-cover w-full h-full rounded"
                />
              ) : (
                "IMAGE PREVIEW"
              )}
            </div>
          </div>

          {/* Open Graph Image */}
          <div>
            <label className="text-sm font-medium">
              Open Graph Image{" "}
              <span className="text-red-400">(1200 × 630px)</span>
            </label>

            <input type="file" className="block mt-2 text-sm text-gray-400" />
          </div>
        </div>
      </div>

      <div className="mt-8 bg-slate-900 rounded-xl shadow-xl p-6 border border-slate-800">
    <h1 className="text-2xl font-semibold mb-6 text-white">
      SEO Metadata
    </h1>

    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
      {/* LEFT SIDE */}
      <div className="lg:col-span-2 space-y-6">
        {/* Meta Title */}
        <div>
          <label className="block text-sm font-medium mb-1 text-slate-300">
            Meta Title
          </label>
          <input
            type="text"
            placeholder="Meta Title (50-60 characters)"
            className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <p className="text-xs text-slate-500 mt-1">
            Recommended: 50–60 characters
          </p>
        </div>

        {/* Meta Description */}
        <div>
          <label className="block text-sm font-medium mb-1 text-slate-300">
            Meta Description
          </label>
          <textarea
            rows={4}
            placeholder="Meta Description (150-160 characters)"
            className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <p className="text-xs text-slate-500 mt-1">
            Recommended: 150–160 characters
          </p>
        </div>

        {/* Meta Keywords */}
        <div>
          <label className="block text-sm font-medium mb-1 text-slate-300">
            Meta Keywords
          </label>
          <textarea
            rows={2}
            placeholder="Comma separated keywords"
            className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        {/* Canonical URL */}
        <div>
          <label className="block text-sm font-medium mb-1 text-slate-300">
            Canonical URL
          </label>
          <input
            type="url"
            placeholder="https://example.com/blog-post"
            className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <p className="text-xs text-slate-500 mt-1">
            Use if this content appears on multiple URLs
          </p>
        </div>
      </div>

      {/* RIGHT SIDE */}
      <div className="space-y-6">
        {/* Open Graph Image */}
        <div>
          <label className="text-sm font-medium text-slate-300">
            Open Graph Image
            <span className="text-red-400"> (1200 × 630px)</span>
          </label>

          <input
            type="file"
            accept="image/*"
            onChange={(e) => setOgImage(e.target.files[0])}
            className="mt-2 text-sm text-slate-400"
          />

          <div className="mt-3 border border-slate-700 rounded-lg h-40 flex items-center justify-center bg-slate-800">
            {ogImage ? (
              <img
                src={previewImage(ogImage)}
                alt="OG Preview"
                className="h-full object-contain"
              />
            ) : (
              <span className="text-slate-500">IMAGE PREVIEW</span>
            )}
          </div>
        </div>

        {/* Twitter Image */}
        <div>
          <label className="text-sm font-medium text-slate-300">
            Twitter Card Image
            <span className="text-red-400"> (1200 × 675px)</span>
          </label>

          <input
            type="file"
            accept="image/*"
            onChange={(e) => setTwitterImage(e.target.files[0])}
            className="mt-2 text-sm text-slate-400"
          />

          <div className="mt-3 border border-slate-700 rounded-lg h-40 flex items-center justify-center bg-slate-800">
            {twitterImage ? (
              <img
                src={previewImage(twitterImage)}
                alt="Twitter Preview"
                className="h-full object-contain"
              />
            ) : (
              <span className="text-slate-500">IMAGE PREVIEW</span>
            )}
          </div>
        </div>

        {/* Status */}
        <div>
          <label className="block text-sm font-medium mb-2 text-slate-300">
            Status
          </label>
          <div className="flex gap-6 text-slate-300">
            <label className="flex items-center gap-2">
              <input
                type="radio"
                checked={status === "active"}
                onChange={() => setStatus("active")}
                className="accent-indigo-500"
              />
              Active
            </label>

            <label className="flex items-center gap-2">
              <input
                type="radio"
                checked={status === "inactive"}
                onChange={() => setStatus("inactive")}
                className="accent-indigo-500"
              />
              Inactive
            </label>
          </div>
        </div>
      </div>
    </div>

   {/* SOCIAL MEDIA & SCHEMA */}
<div className="mt-12">
  <h2 className="text-lg font-semibold text-white mb-4">
    Social Media & Schema
  </h2>

  {/* Tabs */}
  <div className="flex flex-wrap gap-2 border-b border-slate-700 mb-6">
    {[
      { key: "openGraph", label: "Open Graph" },
      { key: "twitter", label: "Twitter" },
      { key: "schema", label: "Schema Markup" },
    ].map((item) => (
      <button
        key={item.key}
        onClick={() => setTab(item.key)}
        className={`px-4 py-2 rounded-t-lg text-sm font-medium transition
          ${
            tab === item.key
              ? "bg-slate-800 text-white border border-b-0 border-slate-700"
              : "text-slate-400 hover:text-white"
          }`}
      >
        {item.label}
      </button>
    ))}
  </div>

  {/* Content Box */}
  <div className="bg-slate-800 border border-slate-700 rounded-lg p-6 space-y-6">
    {/* Open Graph */}
    {tab === "openGraph" && (
      <>
        <div>
          <label className="block text-sm font-medium text-slate-300 mb-1">
            OG Title
          </label>
          <input
            type="text"
            placeholder="Open Graph Title"
            className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <p className="text-xs text-slate-500 mt-1">
            Title for Facebook / LinkedIn sharing
          </p>
        </div>

        <div>
          <label className="block text-sm font-medium text-slate-300 mb-1">
            OG Description
          </label>
          <textarea
            rows={4}
            placeholder="Open Graph Description"
            className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <p className="text-xs text-slate-500 mt-1">
            Description for Facebook / LinkedIn sharing
          </p>
        </div>
      </>
    )}

    {/* Twitter */}
    {tab === "twitter" && (
      <>
        <div>
          <label className="block text-sm font-medium text-slate-300 mb-1">
            Twitter Title
          </label>
          <input
            type="text"
            placeholder="Twitter Card Title"
            className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-slate-300 mb-1">
            Twitter Description
          </label>
          <textarea
            rows={4}
            placeholder="Twitter Card Description"
            className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>
      </>
    )}

    {/* Schema */}
    {tab === "schema" && (
      <div>
        <label className="block text-sm font-medium text-slate-300 mb-1">
          Schema Markup (JSON-LD)
        </label>
        <textarea
          rows={6}
          placeholder='{
  "@context": "https://schema.org",
  "@type": "Article"
}'
          className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 font-mono text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />
      </div>
    )}
  </div>
</div>

{/* ACTION */}
<div className="mt-8 flex justify-end">
  <button className="bg-indigo-600 hover:bg-indigo-700 transition text-white px-8 py-2 rounded-lg">
    Save Blog
  </button>
</div>

    </div>
    </div>
  );
}

