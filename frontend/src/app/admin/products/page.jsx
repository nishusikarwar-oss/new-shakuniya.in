"use client";

import { useState, useEffect, useRef } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { supabase } from "@/integrations/supabase/client";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { toast } from "sonner";
import {
  Eye,
  Pencil,
  Trash2,
  Search,
  Upload,
  ImagePlus,
  X,
  Loader2,
} from "lucide-react";

/* ---------------- Schema ---------------- */
const formSchema = z.object({
  name: z.string().min(1, "Category name is required").max(100),
  // category_type: z.enum(["service", "product"]),
  description: z.string().max(500).optional(),
});

const ProductCategories = () => {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [editingId, setEditingId] = useState(null);
  const [imageFile, setImageFile] = useState(null);
  const [imagePreview, setImagePreview] = useState(null);
  const [uploading, setUploading] = useState(false);
  const fileInputRef = useRef(null);

  const form = useForm({
    resolver: zodResolver(formSchema),
    defaultValues: {
      name: "",
      // category_type: "product",
      description: "",
    },
  });

  /* ---------------- Fetch Categories ---------------- */
  const fetchCategories = async () => {
    try {
      const { data, error } = await supabase
        .from("product_categories")
        .select("*")
        .order("created_at", { ascending: false });

      if (error) throw error;
      setCategories(data || []);
    } catch (error) {
      toast.error("Failed to fetch categories");
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchCategories();
  }, []);

  /* ---------------- Image Handling ---------------- */

  const [logo, setLogo] = useState(null);
  const [featured, setFeatured] = useState(null);

  const handleImageChange = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (!file.type.startsWith("image/")) {
      toast.error("Please select an image file");
      return;
    }

    setImageFile(file);
    const reader = new FileReader();
    reader.onloadend = () => setImagePreview(reader.result);
    reader.readAsDataURL(file);
  };

  const uploadImage = async () => {
    if (!imageFile) return null;

    setUploading(true);
    try {
      const fileExt = imageFile.name.split(".").pop();
      const fileName = `categories/${Date.now()}.${fileExt}`;

      const { error } = await supabase.storage
        .from("admin-uploads")
        .upload(fileName, imageFile);

      if (error) throw error;

      const { data } = supabase.storage
        .from("admin-uploads")
        .getPublicUrl(fileName);

      return data.publicUrl;
    } catch (error) {
      toast.error("Failed to upload image");
      console.error(error);
      return null;
    } finally {
      setUploading(false);
    }
  };

  const [vendor, setVendor] = useState("");
  const [category, setCategory] = useState("");
  const [name, setName] = useState("");
  const [description, setDescription] = useState("");
  const [price, setPrice] = useState("");
  const [offerType, setOfferType] = useState("normal");

  const [thumbFile, setThumbFile] = useState(null);
  const [thumbPreview, setThumbPreview] = useState(null);

  const [fullFile, setFullFile] = useState(null);
  const [fullPreview, setFullPreview] = useState(null);

  const thumbRef = useRef(null);
  const fullRef = useRef(null);

  /* ---------------- IMAGE HANDLERS ---------------- */
  const handleImage = (file, setFile, setPreview) => {
    if (!file || !file.type.startsWith("image/")) return;
    setFile(file);
    setPreview(URL.createObjectURL(file));
  };

  /* ---------------- SUBMIT ---------------- */
  const handleSubmit = (e) => {
    e.preventDefault();

    if (!vendor || !category || !name || !price || !thumbFile || !fullFile) {
      alert("Please fill all required fields");
      return;
    }

    const payload = {
      vendor,
      category,
      name,
      description,
      price,
      offerType,
      logo: logoFile,
      fullImage: fullFile,
    };

    console.log("PRODUCT PAYLOAD:", payload);
    alert("Product saved successfully (check console)");

    // reset
    setVendor("");
    setCategory("");
    setName("");
    setDescription("");
    setPrice("");
    setOfferType("normal");
    setLogoFile(null);
    setLogoPreview(null);
    setFullFile(null);
    setFullPreview(null);
  };

  const [products, setProducts] = useState([
    {
      id: "SC25",
      name: "Shakuniya ERP",
      type: "Product",
      description:
        "Shakuniya ERP is a unified business management platform designed to help modern companies. ",
      image: "/demo/logo1.png",
    },
    {
      id: "SC24",
      name: "Akshar",
      type: "Product",
      description:
        "Akshar is a simple and secure messaging app that works just like WhatsApp for your business.",
      image: "/demo/logo2.png",
    },
    {
      id: "SC23",
      name: "Niya Meet",
      type: "Product",
      description:
        "Niya Meet is a high-performance video conferencing tool built for ultra-low latency.",
      image: "/demo/logo3.png",
    },
  ]);

  /* ---------------- ACTIONS ---------------- */
  const handleViewProduct = (item) => {
    alert(
      `VIEW PRODUCT\n\nName: ${item.name}\nType: ${item.type}\n\n${item.description}`,
    );
  };

  const handleEditProduct = (item) => {
    alert(`EDIT MODE\n\nProduct ID: ${item.id}`);
  };

  const handleDeleteProduct = (id) => {
    if (!confirm("Are you sure you want to delete this item?")) return;
    setProducts((prev) => prev.filter((s) => s.id !== id));
  };

  /* ---------------- Reset ---------------- */
  const resetForm = () => {
    form.reset({
      name: "",
      // category_type: "product",
      description: "",
    });
    setEditingId(null);
    setImageFile(null);
    setImagePreview(null);
    if (fileInputRef.current) fileInputRef.current.value = "";
  };

  /* ---------------- Submit ---------------- */
  const onSubmit = async (values) => {
    setSaving(true);
    try {
      let imageUrl = null;
      if (imageFile) imageUrl = await uploadImage();

      if (editingId) {
        const updateData = {
          name: values.name,
          // category_type: values.category_type,
          description: values.description || null,
        };
        if (imageUrl) updateData.image_url = imageUrl;

        const { error } = await supabase
          .from("product_categories")
          .update(updateData)
          .eq("id", editingId);

        if (error) throw error;
        toast.success("Category updated successfully");
      } else {
        const { error } = await supabase.from("product_categories").insert({
          name: values.name,
          // category_type: values.category_type,
          description: values.description || null,
          image_url: imageUrl,
        });

        if (error) throw error;
        toast.success("Category created successfully");
      }

      resetForm();
      fetchCategories();
    } catch (error) {
      toast.error("Failed to save category");
      console.error(error);
    } finally {
      setSaving(false);
    }
  };

  /* ---------------- Edit / Delete ---------------- */
  const handleEdit = (category) => {
    setEditingId(category.id);
    form.reset({
      name: category.name,
      // category_type: category.category_type,
      description: category.description || "",
    });
    setImagePreview(category.image_url || null);
  };

  const handleDelete = async (id) => {
    if (!confirm("Are you sure you want to delete this category?")) return;

    try {
      const { error } = await supabase
        .from("product_categories")
        .delete()
        .eq("id", id);

      if (error) throw error;
      toast.success("Category deleted");
      fetchCategories();
    } catch (error) {
      toast.error("Failed to delete category");
      console.error(error);
    }
  };

  const filteredCategories = categories.filter(
    (cat) =>
      cat.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      cat.category_type.toLowerCase().includes(searchQuery.toLowerCase()),
  );

  /* ---------------- UI ---------------- */
  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Product Categories</h1>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* FORM */}
        <Card>
          <CardHeader>
            <CardTitle>
              {editingId ? "Edit Category" : "Add Product"}
            </CardTitle>
          </CardHeader>
          <CardContent>
            <Form {...form}>
              <form
                onSubmit={form.handleSubmit(onSubmit)}
                className="space-y-4"
              >
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Product Name *</FormLabel>
                      <FormControl>
                        <Input {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {/* <FormField
                  control={form.control}
                  name="category_type"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Category Type *</FormLabel>
                      <Select
                        onValueChange={field.onChange}
                        value={field.value}
                      >
                        <FormControl>
                          <SelectTrigger>
                            <SelectValue />
                          </SelectTrigger>
                        </FormControl>
                        <SelectContent>
                          <SelectItem value="service">Service</SelectItem>
                          <SelectItem value="product">Product</SelectItem>
                        </SelectContent>
                      </Select>
                    </FormItem>
                  )}
                /> */}
                {/* <div className="mb-4">
                  <h1 className="text-xl font-bold mb-2 mt-6 bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400 bg-clip-text text-transparent">
                    Product Description
                  </h1>
                  <div className="w-full max-w-5xl bg-[#111118] border border-white/10 rounded-2xl shadow-2xl">
                    <CustomEditor />
                  </div>
                </div> */}

                <FormField
                  control={form.control}
                  name="description"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel> Short Description</FormLabel>
                      <FormControl>
                        <Textarea rows={3} {...field} />
                      </FormControl>
                    </FormItem>
                  )}
                />

                <input
                  type="file"
                  hidden
                  ref={fileInputRef}
                  accept="image/*"
                  onChange={handleImageChange}
                />

                <Button
                  type="button"
                  variant="outline"
                  onClick={() => fileInputRef.current?.click()}
                >
                  <Upload className="mr-2 h-4 w-4" />
                  Choose Image
                </Button>
                {imagePreview && (
                  <div className="relative">
                    <img
                      src={imagePreview}
                      alt="Preview"
                      className="w-full h-60 object-cover rounded"
                    />
                    <Button
                      type="button"
                      size="icon"
                      variant="destructive"
                      className="absolute top-2 right-2"
                      onClick={() => {
                        setImageFile(null);
                        setImagePreview(null);
                      }}
                    >
                      <X className="h-4 w-4" />
                    </Button>
                  </div>
                )}

                {/* <div className="flex justify-between items-center mb-6 pt-9">
                  <h1 className="text-2xl font-bold text-white">
                    Add Products
                  </h1>
                  <span className="text-sm text-gray-400">
                    Admin / Prouct Management
                  </span>
                </div> */}

                {/* <div className="bg-[#111827] border border-gray-800 rounded-lg p-4 space-y-4">
                  <h2 className="font-semibold text-white">Product Details</h2>

                  <div>
                    <label className="text-sm text-gray-400">
                      Select Vendor *
                    </label>
                    <select
                      value={vendor}
                      onChange={(e) => setVendor(e.target.value)}
                      className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm"
                    >
                      <option value="">Select Vendor</option>
                      <option>Shakuniya</option>
                      <option>Akshar</option>
                      <option>Niya Meet</option>
                    </select>
                  </div>

                  <div>
                    <label className="text-sm text-gray-400">
                      Select Category *
                    </label>
                    <select
                      value={category}
                      onChange={(e) => setCategory(e.target.value)}
                      className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm"
                    >
                      <option value="">Select Product Category</option>
                      <option>Product</option>
                      <option>Service</option>
                    </select>
                  </div>

                  <div>
                    <label className="text-sm text-gray-400">Name *</label>
                    <input
                      value={name}
                      onChange={(e) => setName(e.target.value)}
                      placeholder="Enter product Name"
                      className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm"
                    />
                  </div>

                  <div>
                    <label className="text-sm text-gray-400">
                      Descriptions *
                    </label>
                    <textarea
                      value={description}
                      onChange={(e) => setDescription(e.target.value)}
                      rows={6}
                      placeholder="Enter product description..."
                      className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm"
                    />
                  </div>
                </div> */}

                <div className="flex gap-2">
                  <Button type="submit" disabled={saving || uploading}>
                    {saving || uploading ? (
                      <Loader2 className="h-4 w-4 animate-spin" />
                    ) : editingId ? (
                      "Update"
                    ) : (
                      "Save"
                    )}
                  </Button>
                  {editingId && (
                    <Button type="button" variant="outline" onClick={resetForm}>
                      Cancel
                    </Button>
                  )}
                </div>
              </form>
            </Form>
          </CardContent>
        </Card>

        {/* TABLE */}
        {/* <Card className="lg:col-span-2 bg-slate-900 border border-slate-800">
          <CardHeader className="flex-row justify-between items-center">
            <CardTitle className="text-slate-100">Category List</CardTitle>

            <div className="relative w-64">
              <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
              <Input
                className="pl-9 bg-slate-800 border-slate-700 text-slate-100 placeholder:text-slate-500 focus-visible:ring-indigo-500"
                placeholder="Search..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
              />
            </div>
          </CardHeader>

          <CardContent>
            <Table>
              <TableHeader>
                <TableRow className="border-b border-slate-800">
                  <TableHead className="text-slate-300">ID</TableHead>
                  <TableHead className="text-slate-300">Name</TableHead>
                  <TableHead className="text-slate-300">Type</TableHead>
                  <TableHead className="text-slate-300">Descriptions</TableHead>
                  <TableHead className="text-slate-300">Image</TableHead>
                  <TableHead className="text-slate-300 text-center">
                    Action
                  </TableHead>
                </TableRow>
              </TableHeader>

               <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6">
    
      <h1 className="text-2xl font-bold text-white mb-6">
        Service Categories
      </h1>

      ================= TABLE =================
      <div className="bg-[#111827] border border-gray-800 rounded-lg overflow-x-auto">
        <table className="w-full text-sm min-w-[900px]">
          <thead className="bg-[#0b1220] text-gray-300">
            <tr>
              {["Id", "Name", "Type", "Descriptions", "Image", "Action"].map(
                (h) => (
                  <th
                    key={h}
                    className="p-3 border border-gray-800 text-left"
                  >
                    {h}
                  </th>
                )
              )}
            </tr>
          </thead>

          <tbody>
            {services.map((item) => (
              <tr
                key={item.id}
                className="hover:bg-[#0b1220] transition"
              >
                <td className="p-3 border border-gray-800">{item.id}</td>

                <td className="p-3 border border-gray-800 font-medium">
                  {item.name}
                </td>

                <td className="p-3 border border-gray-800">{item.type}</td>

                <td className="p-3 border border-gray-800 max-w-md">
                  <p className="text-gray-400 text-xs leading-relaxed line-clamp-4">
                    {item.description}
                  </p>
                </td>

                <td className="p-3 border border-gray-800">
                  <img
                    src={item.image}
                    alt={item.name}
                    className="w-12 h-12 object-contain bg-white rounded"
                  />
                </td>

                <td className="p-3 border border-gray-800">
                  <div className="flex gap-1">
                    <button
                      onClick={() => handleViewService(item)}
                      className="bg-green-600 hover:bg-green-700 text-white p-1.5 rounded"
                    >
                      <Eye size={14} />
                    </button>

                    <button
                      onClick={() => handleEditService(item)}
                      className="bg-blue-600 hover:bg-blue-700 text-white p-1.5 rounded"
                    >
                      <Pencil size={14} />
                    </button>

                    <button
                      onClick={() => handleDeleteService(item.id)}
                      className="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded"
                    >
                      <Trash2 size={14} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}

            {services.length === 0 && (
              <tr>
                <td
                  colSpan={6}
                  className="p-6 text-center text-gray-500"
                >
                  No records found
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
            </Table>
          </CardContent>
        </Card> */}

        <div className="bg-slate-900 rounded-lg border border-white/10 p-5">

          {/* Thumbnail Image */}
          {/* <div className="mb-6">
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
          </div> */}

          {/* Alt Text */}
          {/* <div className="mb-6">
            <label className="text-sm font-medium">Image Alt Text</label>
            <input
              type="text"
              placeholder="Describe the image for accessibility"
              className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p className="text-xs text-gray-400 mt-1">
              Important for SEO & accessibility
            </p>
          </div> */}

          {/* Featured Image */}
          {/* <div className="mb-6">
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

            <div className="mt-3 w-40 h-28 border border-white/10 flex items-center justify-center text-xs text-gray-500 bg-slate-950 rounded">
              {featured ? (
                <img
                  src={featured}
                  className="object-cover w-full h-full rounded"
                  alt="featured"
                />
              ) : (
                "Image preview"
              )}
            </div>
          </div> */}

          {/* Open Graph Image */}
          {/* <div>
            <label className="text-sm font-medium">
              Open Graph Image{" "}
              <span className="text-red-400">(1200 × 630px)</span>
            </label>

            <input type="file" className="block mt-2 text-sm text-gray-400" />
          </div> */}

          {/* <div className="flex justify-between items-center mb-6 pt-20">
            <h1 className="text-2xl font-bold text-white">Add Products</h1>
            <span className="text-sm text-gray-400">
              Admin / Product Management
            </span>
          </div> */}

          <form onSubmit={handleSubmit} className="gap-6">
            {/* ================= LEFT ================= */}

            {/* ================= RIGHT ================= */}
            <div className="h-auto bg-[#0b1220] text-gray-200 p-4 md:p-6 space-y-6">
             <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 className="text-2xl font-bold text-white">Products List</h1>
        <span className="text-sm text-gray-400">Admin</span>

      </div>

  <div className="bg-[#111827] rounded-lg border border-gray-800 overflow-x-auto">
        <table className="w-full text-sm">
          <thead className="bg-[#0b1220] text-gray-300">
            <tr>
              {[
                "Id",
                "Name",
                "Description",
                "Logo",
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
                <td className="p-3 border border-gray-800">{item.name}</td>
                <td className="p-3 border border-gray-800">{item.description}</td>
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
      </div>
</div>

            {/* ================= SUBMIT ================= */}
            <div className="lg:col-span-2 flex py-4 justify-end">
              <button
                type="submit"
                className="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded"
              >
                Save Products
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default ProductCategories;
