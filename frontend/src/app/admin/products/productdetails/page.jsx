"use client";

import { useState } from "react";
import {
  Eye,
  Pencil,
  Trash2,
  Search,
  Download,
  FileText,
  FileSpreadsheet,
  Printer,
} from "lucide-react";
import CustomEditor from "@/components/CustomEditor";

export default function ProductListPage() {
  const [search, setSearch] = useState("");

  const [products, setProducts] = useState([
    {
      id: "SR45",
      offerType: "Normal",
      date: "22-01-2026",
      name: "Shakuniya ERP",
      category: "Shakuniya ERP",
      leadRate: "$10",
      vendor: "Shakuniya ERP",
      image: "/demo/logo1.png",
    },
    {
      id: "SR44",
      offerType: "Normal",
      date: "12-01-2026",
      name: "Cloud Infrastructure",
      category: "Hosting",
      leadRate: "$10",
      vendor: "SSPL",
      image: "/demo/logo2.png",
    },
  ]);

  /* ---------------- ACTIONS ---------------- */
  const handleView = (product) => {
    alert(
      `VIEW PRODUCT\n\nName: ${product.name}\nVendor: ${product.vendor}\nOffer: ${product.offerType}`
    );
  };

  const handleEdit = (product) => {
    alert(`EDIT PRODUCT\n\nProduct ID: ${product.id}`);
  };

  const handleDelete = (id) => {
    if (!confirm("Are you sure you want to delete this product?")) return;
    setProducts((prev) => prev.filter((s) => s.id !== id));
  };

    const [logo, setLogo] = useState(null);
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
    <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6 space-y-6">
      {/* ================= HEADER ================= */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 className="text-2xl font-bold text-white">Products Details</h1>
        <span className="text-sm text-gray-400">Admin</span>
      </div>

      {/* ================= FILTER SECTION ================= */}
      <div className="bg-[#111827] rounded-lg p-4 space-y-4 border border-gray-800">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-3">
          <select className="bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm text-gray-300">
            <option>-- Select Products --</option>
          </select>

          <select className="bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm text-gray-300">
            <option>-- Select Product Type --</option>
          </select>

          <button className="bg-indigo-600 hover:bg-indigo-700 text-white rounded px-4 py-2 text-sm">
            Filter
          </button>

          <div className="relative">
            <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
            <input
              type="text"
              placeholder="Search..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="bg-[#0b1220] border border-gray-700 rounded pl-9 pr-3 py-2 w-full text-sm text-gray-300"
            />
          </div>
        </div>

        {/* ================= EXPORT BUTTONS ================= */}
        {/* <div className="flex flex-wrap gap-2">
          {["Copy", "CSV", "Excel", "PDF", "Print"].map((btn) => (
            <button
              key={btn}
              className="border border-gray-700 px-3 py-1 text-sm rounded hover:bg-gray-800"
            >
              {btn}
            </button>
          ))}
        </div> */}
      </div>

      {/* ================= TABLE SECTION ================= */}


    <div className="min-h-screen bg-slate-950 text-gray-200 p-6">
      <h1 className="text-xl font-semibold mb-6 text-white">Add Products</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* LEFT: Product Details */}
        <div className="lg:col-span-2 bg-slate-900 rounded-lg border border-white/10 p-5">
          <h2 className="font-medium mb-4 text-white">Products Details</h2>

          {/* Product Title */}
          <div className="mb-4">
            <label className="text-sm font-medium">
            Product Title<span className="text-red-400">*</span>
            </label>
            <input
              type="text"
              placeholder="Enter Product Title"
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          {/* URL Slug */}
          <div className="mb-4">
            <label className="text-sm font-medium">URL Slug</label>
            <input
              type="text"
              placeholder="Enter URL Slug (e.g., my-product-post)"
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p className="text-xs text-gray-400 mt-1">
              SEO-friendly URL. If left empty, will be generated from title.
            </p>
          </div>

          {/* Product Description */}
          <div className="mb-4">
          
        <h1 className="text-xl font-bold mb-2 mt-6 bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400 bg-clip-text text-transparent">
          Product Description
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

        {/* RIGHT: Product Images */}
        <div className="bg-slate-900 rounded-lg border border-white/10 p-5">
          <h2 className="font-medium mb-4 text-white">Product Images</h2>

          {/* Thumbnail Image */}
          <div className="mb-6">
            <label className="text-sm font-medium">
              Logo Image{" "}
              <span className="text-red-400">(300 × 250px)*</span>
            </label>

            <input
              type="file"
              className="block mt-2 text-sm text-gray-400"
              onChange={(e) =>
                setLogo(URL.createObjectURL(e.target.files[0]))
              }
            />

            <p className="text-xs text-gray-400 mt-1">
              Appears in product listings
            </p>

            <div className="mt-3 w-40 h-28 border border-white/10 flex items-center justify-center text-xs text-gray-500 bg-slate-950 rounded">
              {logo ? (
                <img
                  src={logo}
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
              Appears at top of product post
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
    Save Product
  </button>
</div>

    </div>
    </div>



      {/* <div className="bg-[#111827] rounded-lg border border-gray-800 overflow-x-auto">
        <table className="w-full text-sm">
          <thead className="bg-[#0b1220] text-gray-300">
            <tr>
              {[
                "Id",
                "Offer Type",
                "Date",
                "Name",
                "Category",
                "Lead Rate",
                "Vendor Name",
                "Thumbnail",
                "Action",
              ].map((h) => (
                <th key={h} className="p-3 border border-gray-800 text-left">
                  {h}
                </th>
              ))}
            </tr>
          </thead>

          <tbody>
            {products.map((item) => (
              <tr
                key={item.id}
                className="hover:bg-[#0b1220] transition"
              >
                <td className="p-3 border border-gray-800">{item.id}</td>
                <td className="p-3 border border-gray-800">
                  {item.offerType}
                </td>
                <td className="p-3 border border-gray-800">{item.date}</td>
                <td className="p-3 border border-gray-800">{item.name}</td>
                <td className="p-3 border border-gray-800">{item.category}</td>
                <td className="p-3 border border-gray-800">{item.leadRate}</td>
                <td className="p-3 border border-gray-800">{item.vendor}</td>
                <td className="p-3 border border-gray-800">
                  <img
                    src={item.image}
                    alt=""
                    className="w-10 h-10 object-contain bg-white rounded"
                  />
                </td>
                <td className="p-3 border border-gray-800">
                  <div className="flex gap-1">
                    <button
                      onClick={() => handleView(item)}
                      className="bg-green-600 hover:bg-green-700 text-white p-1.5 rounded"
                    >
                      <Eye size={14} />
                    </button>
                    <button
                      onClick={() => handleEdit(item)}
                      className="bg-blue-600 hover:bg-blue-700 text-white p-1.5 rounded"
                    >
                      <Pencil size={14} />
                    </button>
                    <button
                      onClick={() => handleDelete(item.id)}
                      className="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded"
                    >
                      <Trash2 size={14} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div> */}
    </div>
  );
}
