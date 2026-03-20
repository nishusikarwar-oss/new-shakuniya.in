"use client";

import { useState, useEffect } from "react";
import dynamic from "next/dynamic";
import {
  Eye,
  Pencil,
  Trash2,
  Search,
  Plus,
  X,
  Save,
  Image as ImageIcon,
  DollarSign,
  FileText,
  Link,
  ChevronDown,
  ChevronUp,
  Star,
  Clock,
  Zap,
  MessageSquare,
  Infinity,
  Award,
  Loader2,
  AlertCircle,
  FolderPlus,
  Tag
} from "lucide-react";

const CustomEditor = dynamic(() => import("@/components/CustomEditor"), {
  ssr: false,
  loading: () => (
    <div className="h-96 bg-slate-950 border border-white/10 rounded-lg flex items-center justify-center text-gray-400">
      Loading editor...
    </div>
  )
});

// ─── Helper: resolve any image_url value to a full src string ───────────────
const resolveImageUrl = (image_url, storageBase) => {
  if (!image_url) return null;
  if (image_url.startsWith("http://") || image_url.startsWith("https://")) {
    return image_url;
  }
  return `${storageBase}/${image_url}`;
};

export default function ProductDashboard() {
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [products, setProducts] = useState([]);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [showAddForm, setShowAddForm] = useState(false);
  const [expandedProduct, setExpandedProduct] = useState(null);
  const [message, setMessage] = useState(null);

  // Features state
  const [showFeatureModal, setShowFeatureModal] = useState(false);
  const [features, setFeatures] = useState([]);
  const [savingFeature, setSavingFeature] = useState(false);
  const [featureFormData, setFeatureFormData] = useState({
    title: "",
    description: "",
    icon_name: "Star"
  });

  // Form state
  const [formData, setFormData] = useState({
    title: "",
    slug: "",
    short_description: "",
    full_description: "",
    price_usd: "",
    price_inr: "",
    image: null,
    video_url: "",
    meta_title: "",
    meta_description: "",
    meta_keywords: "",
    is_active: true
  });

  const [imagePreview, setImagePreview] = useState(null);
  const [status, setStatus] = useState("active");

  const API_URL = "http://127.0.0.1:8000/api";
  const STORAGE_URL = "http://127.0.0.1:8000/storage";

  const iconOptions = [
    { name: "Star", icon: <Star size={20} /> },
    { name: "Zap", icon: <Zap size={20} /> },
    { name: "MessageSquare", icon: <MessageSquare size={20} /> },
    { name: "Clock", icon: <Clock size={20} /> },
    { name: "Infinity", icon: <Infinity size={20} /> },
    { name: "Award", icon: <Award size={20} /> }
  ];

  // Show flash message
  const showMessage = (type, text) => {
    setMessage({ type, text });
    setTimeout(() => setMessage(null), 4000);
  };

  // Fetch all products
  const fetchProducts = async () => {
    try {
      setLoading(true);
      const response = await fetch(`${API_URL}/products`);
      const result = await response.json();

      console.log("API Response:", result);

      let productsData = [];
      if (result?.data?.data) {
        productsData = result.data.data;
      } else if (result?.data) {
        productsData = result.data;
      } else if (Array.isArray(result)) {
        productsData = result;
      } else if (result?.products) {
        productsData = result.products;
      }

      setProducts(productsData);
    } catch (error) {
      console.error("Error fetching products:", error);
      showMessage("error", "Failed to fetch products: " + error.message);
    } finally {
      setLoading(false);
    }
  };

  const fetchProductFeatures = async (productId) => {
    try {
      const response = await fetch(`${API_URL}/products/${productId}/features`);
      const result = await response.json();

      if (response.ok) {
        const featuresData = result.data || result;
        setFeatures(Array.isArray(featuresData) ? featuresData : []);
      }
    } catch (error) {
      console.error("Error fetching features:", error);
    }
  };

  // Fetch single product details
  const fetchProductDetails = async (id) => {
    try {
      const response = await fetch(`${API_URL}/products/${id}`);
      await fetchProductFeatures(id);
      const result = await response.json();

      if (response.ok) {
        const product = result.data || result;
        setSelectedProduct(product);
        setFormData({
          title: product.title || "",
          slug: product.slug || "",
          short_description: product.short_description || "",
          full_description: product.full_description || "",
          price_usd: product.price_usd || "",
          price_inr: product.price_inr || "",
          image: product.image_url || "",          // file input always starts empty
          video_url: product.video_url || "",
          meta_title: product.meta_title || "",
          meta_description: product.meta_description || "",
          meta_keywords: product.meta_keywords || "",
          is_active: product.is_active === 1 || product.is_active === true
        });
        setStatus(product.is_active ? "active" : "inactive");

        // FIX: use helper so absolute URLs and relative paths both work
        // setImagePreview(resolveImageUrl(product.image_url, STORAGE_URL));

        setShowAddForm(true);
      } else {
        showMessage("error", "Failed to fetch product details");
      }
    } catch (error) {
      console.error("Error fetching product details:", error);
      showMessage("error", "Error fetching product details");
    }
  };

  const handleEdit = (product) => fetchProductDetails(product.id);
  const handleView = (product) => fetchProductDetails(product.id);

  // Handle delete product
  const handleDelete = async (id) => {
    if (!confirm("Are you sure you want to delete this product?")) return;

    try {
      const response = await fetch(`${API_URL}/products/${id}`, { method: "DELETE" });

      if (response.ok) {
        showMessage("success", "Product deleted successfully");
        fetchProducts();
      } else {
        showMessage("error", "Failed to delete product");
      }
    } catch (error) {
      console.error("Error deleting product:", error);
      showMessage("error", "Error deleting product");
    }
  };

  // Handle add new product
  const handleAddNew = () => {
    setSelectedProduct(null);
    setFormData({
      title: "",
      slug: "",
      short_description: "",
      full_description: "",
      price_usd: "",
      price_inr: "",
      image_url: "",
      video_url: "",
      meta_title: "",
      meta_description: "",
      meta_keywords: "",
      is_active: true
    });
    setFeatures([]);
    setImagePreview(null);
    setStatus("active");
    setShowAddForm(true);
  };

  // Handle form input changes
  const handleInputChange = (field, value) => {
    setFormData((prev) => ({ ...prev, [field]: value }));

    if (field === "title" && !selectedProduct) {
      const slug = value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-|-$/g, "");
      setFormData((prev) => ({ ...prev, slug }));
    }
  };

  // Handle image upload
 const handleImageUpload = (e) => {
  const file = e.target.files[0];

  if (file) {
    setFormData((prev) => ({
      ...prev,
      image: file
    }));

    const preview = URL.createObjectURL(file);
    setImagePreview(preview);
  }
};

  // Handle form submit
  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!formData.title.trim()) {
      showMessage("error", "Product title is required");
      return;
    }

    setSaving(true);

    try {
      const url = selectedProduct
        ? `${API_URL}/products/${selectedProduct.id}`
        : `${API_URL}/products`;

      const submitData = new FormData();

      if (selectedProduct) {
        submitData.append("_method", "PUT");
      }

      submitData.append("title", formData.title);
      submitData.append(
        "slug",
        formData.slug ||
          formData.title
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-|-$/g, "")
      );
      submitData.append("short_description", formData.short_description || "");
      submitData.append("full_description", formData.full_description || "");
      submitData.append("price_usd", formData.price_usd || "0");
      submitData.append("price_inr", formData.price_inr || "0");
      submitData.append("video_url", formData.video_url || "");
      submitData.append("meta_title", formData.meta_title || "");
      submitData.append("meta_description", formData.meta_description || "");
      submitData.append("meta_keywords", formData.meta_keywords || "");
      submitData.append("is_active", status === "active" ? "1" : "0");

      // Only append image when the user actually picked a new file
      if (formData.image) {
        submitData.append("image", formData.image);
      }

      const response = await fetch(url, { method: "POST", body: submitData });
      const result = await response.json();

      if (response.ok) {
        showMessage(
          "success",
          selectedProduct ? "Product updated successfully" : "Product created successfully"
        );
        fetchProducts();

        if (!selectedProduct && result.data?.id) {
          await fetchProductFeatures(result.data.id);
          setSelectedProduct(result.data);
        }

        setShowAddForm(false);
        setSelectedProduct(null);
        setImagePreview(null);
        setFeatures([]);
      } else {
        showMessage("error", result.message || result.error || "Failed to save product");
      }
    } catch (error) {
      console.error("Error saving product:", error);
      showMessage("error", "Error saving product: " + error.message);
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    setShowAddForm(false);
    setSelectedProduct(null);
    setImagePreview(null);
    setFeatures([]);
  };

  const getIcon = (iconName) => {
    const icons = {
      MessageSquare: <MessageSquare size={16} className="text-blue-400" />,
      Zap: <Zap size={16} className="text-yellow-400" />,
      Clock: <Clock size={16} className="text-green-400" />,
      Infinity: <Infinity size={16} className="text-purple-400" />,
      Award: <Award size={16} className="text-orange-400" />,
      Star: <Star size={16} className="text-yellow-400" />
    };
    return icons[iconName] || <Tag size={16} className="text-gray-400" />;
  };

  const openFeatureModal = () => {
    setFeatureFormData({ title: "", description: "", icon_name: "Star" });
    setShowFeatureModal(true);
  };

  const closeFeatureModal = () => {
    setShowFeatureModal(false);
    setFeatureFormData({ title: "", description: "", icon_name: "Star" });
  };

  const handleFeatureChange = (field, value) => {
    setFeatureFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleAddFeature = async (e) => {
    e.preventDefault();

    if (!featureFormData.title.trim()) {
      showMessage("error", "Feature title is required");
      return;
    }
    if (!featureFormData.description.trim()) {
      showMessage("error", "Feature description is required");
      return;
    }
    if (!selectedProduct) {
      showMessage("error", "Please save the product first before adding features");
      return;
    }

    setSavingFeature(true);

    try {
      const response = await fetch(`${API_URL}/products/${selectedProduct.id}/features`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          title: featureFormData.title,
          description: featureFormData.description,
          icon_name: featureFormData.icon_name
        })
      });

      const result = await response.json();

      if (response.ok) {
        showMessage("success", "Feature added successfully");
        await fetchProductFeatures(selectedProduct.id);
        closeFeatureModal();
      } else {
        showMessage("error", result.message || "Failed to add feature");
      }
    } catch (error) {
      console.error("Error adding feature:", error);
      showMessage("error", "Error adding feature: " + error.message);
    } finally {
      setSavingFeature(false);
    }
  };

  const handleDeleteFeature = async (featureId) => {
    if (!confirm("Are you sure you want to delete this feature?")) return;

    try {
      const response = await fetch(
        `${API_URL}/products/${selectedProduct.id}/features/${featureId}`,
        { method: "DELETE" }
      );

      if (response.ok) {
        showMessage("success", "Feature deleted successfully");
        await fetchProductFeatures(selectedProduct.id);
      } else {
        showMessage("error", "Failed to delete feature");
      }
    } catch (error) {
      console.error("Error deleting feature:", error);
      showMessage("error", "Error deleting feature");
    }
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  const filteredProducts = products.filter(
    (product) =>
      product.title?.toLowerCase().includes(search.toLowerCase()) ||
      product.slug?.toLowerCase().includes(search.toLowerCase())
  );

  if (loading && !showAddForm) {
    return (
      <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6 flex items-center justify-center">
        <div className="flex items-center gap-2">
          <Loader2 size={24} className="animate-spin text-indigo-500" />
          <span>Loading products...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-white">Product Dashboard</h1>
          <p className="text-sm text-gray-400 mt-1">Manage your products</p>
        </div>
        <button
          onClick={handleAddNew}
          className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition"
        >
          <Plus size={16} />
          Add New Product
        </button>
      </div>

      {/* Flash Message */}
      {message && (
        <div
          className={`p-3 rounded-lg text-sm flex items-center gap-2 ${
            message.type === "success"
              ? "bg-green-500/20 text-green-400 border border-green-500/30"
              : "bg-red-500/20 text-red-400 border border-red-500/30"
          }`}
        >
          <AlertCircle size={16} />
          {message.text}
        </div>
      )}

      {!showAddForm ? (
        <>
          {/* Search */}
          <div className="bg-[#111827] rounded-lg p-4 border border-gray-800">
            <div className="flex flex-col md:flex-row gap-3">
              <div className="flex-1 relative">
                <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                <input
                  type="text"
                  placeholder="Search products by title or slug..."
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="w-full bg-[#0b1220] border border-gray-700 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>
            </div>
          </div>

          {/* Products Table */}
          <div className="bg-[#111827] rounded-lg border border-gray-800 overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-[#0b1220] text-gray-300 border-b border-gray-800">
                <tr>
                  <th className="p-3 text-left">ID</th>
                  <th className="p-3 text-left">Image</th>
                  <th className="p-3 text-left">Title</th>
                  <th className="p-3 text-left">Slug</th>
                  <th className="p-3 text-right">Price (USD)</th>
                  <th className="p-3 text-right">Price (INR)</th>
                  <th className="p-3 text-center">Status</th>
                  <th className="p-3 text-center">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-800">
                {filteredProducts.length === 0 ? (
                  <tr>
                    <td colSpan="8" className="p-8 text-center text-gray-400">
                      No products found
                    </td>
                  </tr>
                ) : (
                  filteredProducts.map((product) => (
                    <tr key={product.id} className="hover:bg-[#0b1220] transition">
                      <td className="p-3 font-mono text-xs text-gray-400">
                        {product.id?.toString().substring(0, 8)}...
                      </td>
                      <td className="p-3">
                        <div className="w-10 h-10 bg-gray-800 rounded-lg overflow-hidden flex items-center justify-center">
                          {/* FIX: use resolveImageUrl so both absolute and relative paths work */}
                          {product.image_url ? (
                            <img
                              src={product.image_url}
                              alt={product.title}
                              className="w-full h-full object-cover"
                              onError={(e) => {
                                e.target.style.display = "none";
                              }}
                            />
                          ) : (
                            <ImageIcon size={16} className="text-gray-600" />
                          )}
                        </div>
                      </td>
                      <td className="p-3 font-medium text-white">
                        {product.title || "Untitled"}
                      </td>
                      <td className="p-3 text-xs text-gray-400 font-mono">
                        {product.slug || "—"}
                      </td>
                      <td className="p-3 text-right text-green-400">
                        {product.price_usd
                          ? `$${parseFloat(product.price_usd).toLocaleString()}`
                          : "—"}
                      </td>
                      <td className="p-3 text-right text-orange-400">
                        {product.price_inr
                          ? `₹${parseFloat(product.price_inr).toLocaleString()}`
                          : "—"}
                      </td>
                      <td className="p-3 text-center">
                        <span
                          className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${
                            product.is_active
                              ? "bg-green-500/20 text-green-400"
                              : "bg-red-500/20 text-red-400"
                          }`}
                        >
                          {product.is_active ? "Active" : "Inactive"}
                        </span>
                      </td>
                      <td className="p-3">
                        <div className="flex justify-center gap-2">
                          <button
                            onClick={() => handleView(product)}
                            className="bg-green-600 hover:bg-green-700 text-white p-1.5 rounded transition"
                            title="View"
                          >
                            <Eye size={14} />
                          </button>
                          <button
                            onClick={() => handleEdit(product)}
                            className="bg-blue-600 hover:bg-blue-700 text-white p-1.5 rounded transition"
                            title="Edit"
                          >
                            <Pencil size={14} />
                          </button>
                          <button
                            onClick={() => handleDelete(product.id)}
                            className="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded transition"
                            title="Delete"
                          >
                            <Trash2 size={14} />
                          </button>
                          <button
                            onClick={() => {
                              fetchProductDetails(product.id);
                              openFeatureModal();
                              setImagePreview(product.image_url);
                            }}
                            className="bg-purple-600 hover:bg-purple-700 text-white p-1.5 rounded transition"
                            title="Add Features"
                          >
                            <FolderPlus size={14} />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </>
      ) : (
        /* Add/Edit Product Form */
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-xl font-semibold text-white">
              {selectedProduct ? "Edit Product" : "Add New Product"}
            </h1>
            <button onClick={handleCancel} className="text-gray-400 hover:text-white transition">
              <X size={24} />
            </button>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Basic Information */}
            <div className="bg-[#111827] rounded-lg border border-gray-800 p-6">
              <h2 className="text-lg font-semibold text-white mb-4">Basic Information</h2>

              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Left Column */}
                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium mb-1 text-gray-300">
                      Product Title <span className="text-red-400">*</span>
                    </label>
                    <input
                      type="text"
                      placeholder="Enter product title"
                      value={formData.title}
                      onChange={(e) => handleInputChange("title", e.target.value)}
                      className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-1 text-gray-300">
                      URL Slug
                    </label>
                    <input
                      type="text"
                      placeholder="auto-generated from title"
                      value={formData.slug}
                      onChange={(e) => handleInputChange("slug", e.target.value)}
                      className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                    <p className="text-xs text-gray-500 mt-1">
                      SEO-friendly URL. Leave empty to auto-generate.
                    </p>
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-1 text-gray-300">
                      Short Description
                    </label>
                    <textarea
                      rows={3}
                      placeholder="Brief description of the product"
                      value={formData.short_description}
                      onChange={(e) => handleInputChange("short_description", e.target.value)}
                      className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium mb-1 text-gray-300">
                        Price (USD)
                      </label>
                      <input
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        value={formData.price_usd}
                        onChange={(e) => handleInputChange("price_usd", e.target.value)}
                        className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium mb-1 text-gray-300">
                        Price (INR)
                      </label>
                      <input
                        type="number"
                        step="0.01"
                        placeholder="0.00"
                        value={formData.price_inr}
                        onChange={(e) => handleInputChange("price_inr", e.target.value)}
                        className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      />
                    </div>
                  </div>
                </div>

                {/* Right Column */}
                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium mb-1 text-gray-300">
                      Product Image
                    </label>
                    <input
                      type="file"
                      accept="image/*"
                      onChange={handleImageUpload}
                      className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-indigo-600 file:text-white hover:file:bg-indigo-700"
                    />
                    {/* FIX: imagePreview is already a full resolved URL — works for existing
                        DB images (set via resolveImageUrl in fetchProductDetails) and new
                        local file picks (set via URL.createObjectURL in handleImageUpload) */}
                   <div className="mt-3 w-full h-40 bg-[#0b1220] border border-gray-700 rounded-lg overflow-hidden flex items-center justify-center">
  {imagePreview || formData.image ? (
    <img
      src={imagePreview || formData.image}
      alt="Preview"
      className="h-full w-full object-cover"
    />
  ) : (
    <div className="text-center text-gray-500">
      <ImageIcon size={32} className="mx-auto mb-2 opacity-50" />
      <p className="text-xs">Image preview</p>
    </div>
  )}
</div>
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-1 text-gray-300">
                      Video URL
                    </label>
                    <input
                      type="url"
                      placeholder="https://youtube.com/watch?v=..."
                      value={formData.video_url}
                      onChange={(e) => handleInputChange("video_url", e.target.value)}
                      className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                    <p className="text-xs text-gray-500 mt-1">
                      YouTube, Vimeo, or direct video URL
                    </p>
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-1 text-gray-300">Status</label>
                    <div className="flex gap-6">
                      <label className="flex items-center gap-2 cursor-pointer">
                        <input
                          type="radio"
                          checked={status === "active"}
                          onChange={() => setStatus("active")}
                          className="accent-indigo-500"
                        />
                        <span className="text-gray-300">Active</span>
                      </label>
                      <label className="flex items-center gap-2 cursor-pointer">
                        <input
                          type="radio"
                          checked={status === "inactive"}
                          onChange={() => setStatus("inactive")}
                          className="accent-indigo-500"
                        />
                        <span className="text-gray-300">Inactive</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Full Description */}
            <div className="bg-[#111827] rounded-lg border border-gray-800 p-6">
              <h2 className="text-lg font-semibold text-white mb-4">Full Description</h2>
              <div className="min-h-[400px]">
                <CustomEditor
                  value={formData.full_description}
                  onChange={(content) => handleInputChange("full_description", content)}
                />
              </div>
            </div>

            {/* Product Features Section */}
            {selectedProduct && (
              <div className="bg-[#111827] rounded-lg border border-gray-800 p-6">
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-lg font-semibold text-white">Product Features</h2>
                  <button
                    type="button"
                    onClick={openFeatureModal}
                    className="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg text-sm flex items-center gap-2 transition"
                  >
                    <Plus size={14} />
                    Add Feature
                  </button>
                </div>

                {features.length === 0 ? (
                  <div className="text-center py-8 text-gray-400">
                    <p className="text-sm">
                      No features added yet. Click the "Add Feature" button to add features.
                    </p>
                  </div>
                ) : (
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {features.map((feature) => (
                      <div
                        key={feature.id}
                        className="bg-[#0b1220] p-4 rounded-lg border border-gray-700 hover:border-gray-600 transition"
                      >
                        <div className="flex items-start justify-between">
                          <div className="flex items-start gap-3 flex-1">
                            <div className="p-2 bg-[#1f2937] rounded-lg">
                              {getIcon(feature.icon_name || "Star")}
                            </div>
                            <div className="flex-1">
                              <h3 className="text-sm font-medium text-white">{feature.title}</h3>
                              <p className="text-xs text-gray-400 mt-1">{feature.description}</p>
                            </div>
                          </div>
                          <button
                            type="button"
                            onClick={() => handleDeleteFeature(feature.id)}
                            className="text-red-400 hover:text-red-300 transition ml-2"
                            title="Delete feature"
                          >
                            <Trash2 size={14} />
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            )}

            {/* SEO Metadata */}
            <div className="bg-[#111827] rounded-lg border border-gray-800 p-6">
              <h2 className="text-lg font-semibold text-white mb-4">SEO Metadata</h2>

              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium mb-1 text-gray-300">
                    Meta Title
                  </label>
                  <input
                    type="text"
                    placeholder="Meta title (50-60 characters)"
                    value={formData.meta_title}
                    onChange={(e) => handleInputChange("meta_title", e.target.value)}
                    className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  <p className="text-xs text-gray-500 mt-1">Recommended: 50-60 characters</p>
                </div>

                <div>
                  <label className="block text-sm font-medium mb-1 text-gray-300">
                    Meta Description
                  </label>
                  <textarea
                    rows={3}
                    placeholder="Meta description (150-160 characters)"
                    value={formData.meta_description}
                    onChange={(e) => handleInputChange("meta_description", e.target.value)}
                    className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  <p className="text-xs text-gray-500 mt-1">Recommended: 150-160 characters</p>
                </div>

                <div>
                  <label className="block text-sm font-medium mb-1 text-gray-300">
                    Meta Keywords
                  </label>
                  <textarea
                    rows={2}
                    placeholder="Comma separated keywords"
                    value={formData.meta_keywords}
                    onChange={(e) => handleInputChange("meta_keywords", e.target.value)}
                    className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                </div>
              </div>
            </div>

            {/* Form Actions */}
            <div className="flex justify-end gap-4">
              <button
                type="button"
                onClick={handleCancel}
                className="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition"
              >
                Cancel
              </button>
              <button
                type="submit"
                disabled={saving}
                className="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2 disabled:opacity-50"
              >
                {saving && <Loader2 size={16} className="animate-spin" />}
                {saving ? "Saving..." : selectedProduct ? "Update Product" : "Create Product"}
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Feature Modal */}
      {showFeatureModal && (
        <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
          <div className="bg-[#111827] rounded-xl border border-gray-700 w-full max-w-md">
            <div className="flex items-center justify-between p-5 border-b border-gray-700">
              <h2 className="text-lg font-semibold text-white">Add Product Feature</h2>
              <button
                onClick={closeFeatureModal}
                className="text-gray-400 hover:text-white transition"
              >
                <X size={20} />
              </button>
            </div>

            <form onSubmit={handleAddFeature} className="p-5 space-y-4">
              <div>
                <label className="block text-sm font-medium mb-1 text-gray-300">
                  Feature Title <span className="text-red-400">*</span>
                </label>
                <input
                  type="text"
                  placeholder="e.g., Cross Platform"
                  value={featureFormData.title}
                  onChange={(e) => handleFeatureChange("title", e.target.value)}
                  className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  required
                />
              </div>

              <div>
                <label className="block text-sm font-medium mb-1 text-gray-300">
                  Feature Description <span className="text-red-400">*</span>
                </label>
                <textarea
                  rows={3}
                  placeholder="Describe the feature..."
                  value={featureFormData.description}
                  onChange={(e) => handleFeatureChange("description", e.target.value)}
                  className="w-full bg-[#0b1220] border border-gray-700 rounded-lg px-4 py-2 text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  required
                />
              </div>

              <div>
                <label className="block text-sm font-medium mb-2 text-gray-300">
                  Select Icon
                </label>
                <div className="grid grid-cols-3 gap-2">
                  {iconOptions.map((iconOption) => (
                    <button
                      key={iconOption.name}
                      type="button"
                      onClick={() => handleFeatureChange("icon_name", iconOption.name)}
                      className={`p-3 rounded-lg border transition flex items-center justify-center gap-2 ${
                        featureFormData.icon_name === iconOption.name
                          ? "bg-indigo-600 border-indigo-500 text-white"
                          : "bg-[#0b1220] border-gray-700 text-gray-400 hover:border-gray-500"
                      }`}
                    >
                      {iconOption.icon}
                      <span className="text-xs">{iconOption.name}</span>
                    </button>
                  ))}
                </div>
              </div>

              <div className="flex gap-3 pt-3">
                <button
                  type="button"
                  onClick={closeFeatureModal}
                  className="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg transition"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  disabled={savingFeature}
                  className="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg transition flex items-center justify-center gap-2 disabled:opacity-50"
                >
                  {savingFeature && <Loader2 size={16} className="animate-spin" />}
                  {savingFeature ? "Adding..." : "Add Feature"}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}