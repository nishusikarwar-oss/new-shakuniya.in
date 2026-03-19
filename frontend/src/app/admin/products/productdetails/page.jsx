//  "use client";

// import { useState, useEffect, Fragment } from "react";
// import dynamic from "next/dynamic";
// import {
//   Eye,
//   Pencil,
//   Trash2,
//   Search,
//   Plus,
//   X,
//   Save,
//   Image as ImageIcon,
//   Globe,
//   Twitter,
//   Code,
//   DollarSign,
//   Tag,
//   FileText,
//   Link,
//   EyeOff,
//   ChevronDown,
//   ChevronUp,
//   Star,
//   Clock,
//   Zap,
//   MessageSquare,
//   Infinity,
//   Award
// } from "lucide-react";

// const CustomEditor = dynamic(() => import("@/components/CustomEditor"), {
//   ssr: false,
//   loading: () => <div className="h-96 bg-slate-950 border border-white/10 rounded-lg flex items-center justify-center">Loading editor...</div>
// });

// export default function ProductListPage() {
//   const [search, setSearch] = useState("");
//   const [loading, setLoading] = useState(true);
//   const [products, setProducts] = useState([]);
//   const [selectedProduct, setSelectedProduct] = useState(null);
//   const [showAddForm, setShowAddForm] = useState(false);
//   const [showFeatures, setShowFeatures] = useState(false);
//   const [productFeatures, setProductFeatures] = useState([]);
//   const [expandedProduct, setExpandedProduct] = useState(null);
  
//   // Form state
//   const [formData, setFormData] = useState({
//     title: "",
//     slug: "",
//     short_description: "",
//     full_description: "",
//     tags: "",
//     price_usd: "",
//     price_inr: "",
//     image: null,
//     video_url: "",
//     logo_preview: null,
//     featured_preview: null,
//     meta_title: "",
//     meta_description: "",
//     meta_keywords: "",
//     canonical_url: "",
//     og_title: "",
//     og_description: "",
//     og_image: null,
//     og_preview: null,
//     twitter_title: "",
//     twitter_description: "",
//     twitter_image: null,
//     twitter_preview: null,
//     schema_markup: "",
//     is_active: true
//   });

//   const [status, setStatus] = useState("active");
//   const [ogImage, setOgImage] = useState(null);
//   const [twitterImage, setTwitterImage] = useState(null);
//   const [tab, setTab] = useState("openGraph");
//   const [logo, setLogo] = useState(null);
//   const [featured, setFeatured] = useState(null);

//   // Fetch products
//   useEffect(() => {
//     fetchProducts();
//   }, []);

//   const fetchProducts = async () => {
//     try {
//       setLoading(true);
//       const response = await fetch('http://127.0.0.1:8000/api/products');
//       const data = await response.json();
      
//       if (data.success) {
//         setProducts(data.data.data);
//       }
//     } catch (error) {
//       console.error('Error fetching products:', error);
//     } finally {
//       setLoading(false);
//     }
//   };

//   const fetchProductFeatures = async (productId) => {
//     try {
//       const response = await fetch(`http://127.0.0.1:8000/api/products/${productId}/features`);
//       const data = await response.json();
      
//       if (data.success) {
//         setProductFeatures(data.data);
//         setExpandedProduct(productId);
//       }
//     } catch (error) {
//       console.error('Error fetching features:', error);
//     }
//   };

//   const fetchProductDetails = async (id) => {
//     try {
//       const response = await fetch(`http://127.0.0.1:8000/api/products/${id}`);
//       const data = await response.json();
      
//       if (data.success) {
//         const product = data.data;
//         setSelectedProduct(product);
//         setFormData({
//           title: product.title || "",
//           slug: product.slug || "",
//           short_description: product.short_description || "",
//           full_description: product.full_description || "",
//           tags: "",
//           price_usd: product.price_usd || "",
//           price_inr: product.prices?.inr?.raw || "",
//           image: null,
//           video_url: product.video_url || "",
//           logo_preview: null,
//           featured_preview: product.primary_image_url || null,
//           meta_title: product.meta_title || "",
//           meta_description: product.meta_description || "",
//           meta_keywords: product.meta_keywords || "",
//           canonical_url: "",
//           og_title: "",
//           og_description: "",
//           og_image: null,
//           og_preview: null,
//           twitter_title: "",
//           twitter_description: "",
//           twitter_image: null,
//           twitter_preview: null,
//           schema_markup: "",
//           is_active: product.is_active
//         });
//         setStatus(product.is_active ? "active" : "inactive");
//         setShowAddForm(true);
//         fetchProductFeatures(id);
//       }
//     } catch (error) {
//       console.error('Error fetching product details:', error);
//     }
//   };

//   const handleView = (product) => {
//     fetchProductDetails(product.id);
//   };

//   const handleEdit = (product) => {
//     fetchProductDetails(product.id);
//   };

//   const handleDelete = async (id) => {
//     if (!confirm("Are you sure you want to delete this product?")) return;
    
//     try {
//       const response = await fetch(`http://127.0.0.1:8000/api/products/${id}`, {
//         method: 'DELETE',
//       });
      
//       if (response.ok) {
//         setProducts((prev) => prev.filter((p) => p.id !== id));
//       }
//     } catch (error) {
//       console.error('Error deleting product:', error);
//     }
//   };

//   const handleAddNew = () => {
//     setSelectedProduct(null);
//     setFormData({
//       title: "",
//       slug: "",
//       short_description: "",
//       full_description: "",
//       tags: "",
//       price_usd: "",
//       price_inr: "",
//       image: null,
//       video_url: "",
//       logo_preview: null,
//       featured_preview: null,
//       meta_title: "",
//       meta_description: "",
//       meta_keywords: "",
//       canonical_url: "",
//       og_title: "",
//       og_description: "",
//       og_image: null,
//       og_preview: null,
//       twitter_title: "",
//       twitter_description: "",
//       twitter_image: null,
//       twitter_preview: null,
//       schema_markup: "",
//       is_active: true
//     });
//     setLogo(null);
//     setFeatured(null);
//     setOgImage(null);
//     setTwitterImage(null);
//     setStatus("active");
//     setShowAddForm(true);
//   };

//   const handleSubmit = async (e) => {
//     e.preventDefault();
    
//     try {
//       const url = selectedProduct
//         ? `http://127.0.0.1:8000/api/products/${selectedProduct.id}`
//         : 'http://127.0.0.1:8000/api/products';
      
//       // Use POST for both but add _method=PUT for updates (Laravel requirement for FormData)
//       const method = 'POST';
      
//       const submitData = new FormData();
//       if (selectedProduct) {
//         submitData.append('_method', 'PUT');
//       }
      
//       submitData.append('title', formData.title);
//       submitData.append('slug', formData.slug || formData.title.toLowerCase().replace(/[^a-z0-9]+/g, '-'));
//       submitData.append('short_description', formData.short_description || "");
//       submitData.append('full_description', formData.full_description || "");
//       submitData.append('price_usd', formData.price_usd || '0');
//       submitData.append('price_inr', formData.price_inr || '0');
//       submitData.append('video_url', formData.video_url || "");
//       submitData.append('meta_title', formData.meta_title || "");
//       submitData.append('meta_description', formData.meta_description || "");
//       submitData.append('meta_keywords', formData.meta_keywords || "");
//       submitData.append('is_active', status === "active" ? '1' : '0');
      
//       // Add missing fields
//       submitData.append('tags', formData.tags || "");
//       submitData.append('canonical_url', formData.canonical_url || "");
//       submitData.append('og_title', formData.og_title || "");
//       submitData.append('og_description', formData.og_description || "");
//       submitData.append('twitter_title', formData.twitter_title || "");
//       submitData.append('twitter_description', formData.twitter_description || "");
//       submitData.append('schema_markup', formData.schema_markup || "");
      
//       if (featured) submitData.append('featured_image', featured);
//       if (ogImage) submitData.append('og_image', ogImage);
//       if (twitterImage) submitData.append('twitter_image', twitterImage);
      
//       const response = await fetch(url, {
//         method: method,
//         body: submitData
//       });
      
//       if (response.ok) {
//         fetchProducts();
//         setShowAddForm(false);
//         setSelectedProduct(null);
//       }
//     } catch (error) {
//       console.error('Error saving product:', error);
//     }
//   };

//   const previewImage = (file) => {
//     if (!file) return null;
//     return URL.createObjectURL(file);
//   };

//   // Icon mapping for features
//   const getIcon = (iconName) => {
//     switch(iconName) {
//       case 'MessageSquare': return <MessageSquare size={16} className="text-blue-400" />;
//       case 'Zap': return <Zap size={16} className="text-yellow-400" />;
//       case 'Clock': return <Clock size={16} className="text-green-400" />;
//       case 'Infinity': return <Infinity size={16} className="text-purple-400" />;
//       case 'Award': return <Award size={16} className="text-orange-400" />;
//       case 'Star': return <Star size={16} className="text-yellow-400" />;
//       default: return <Tag size={16} className="text-gray-400" />;
//     }
//   };

//   const filteredProducts = products.filter(product => 
//     product.title?.toLowerCase().includes(search.toLowerCase())
//   );

//   if (loading && !showAddForm) {
//     return (
//       <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6 flex items-center justify-center">
//         <div className="text-white">Loading...</div>
//       </div>
//     );
//   }

//   return (
//     <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6 space-y-6">
//       {/* Header */}
//       <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
//         <h1 className="text-2xl font-bold text-white">Products Details</h1>
//         <button
//           onClick={handleAddNew}
//           className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2"
//         >
//           <Plus size={16} />
//           Add New Product
//         </button>
//       </div>

//       {!showAddForm ? (
//         <>
//           {/* Filter Section */}
//           <div className="bg-[#111827] rounded-lg p-4 space-y-4 border border-gray-800">
//             <div className="grid grid-cols-1 md:grid-cols-4 gap-3">
//               <select className="bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm text-gray-300">
//                 <option>-- Select Products --</option>
//                 {products.map(p => (
//                   <option key={p.id} value={p.id}>{p.title}</option>
//                 ))}
//               </select>

//               <select className="bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-sm text-gray-300">
//                 <option>-- Select Product Type --</option>
//                 <option>Normal</option>
//                 <option>Featured</option>
//               </select>

//               <button className="bg-indigo-600 hover:bg-indigo-700 text-white rounded px-4 py-2 text-sm">
//                 Filter
//               </button>

//               <div className="relative">
//                 <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
//                 <input
//                   type="text"
//                   placeholder="Search..."
//                   value={search}
//                   onChange={(e) => setSearch(e.target.value)}
//                   className="bg-[#0b1220] border border-gray-700 rounded pl-9 pr-3 py-2 w-full text-sm text-gray-300"
//                 />
//               </div>
//             </div>
//           </div>

//           {/* Products Table */}
//           <div className="bg-[#111827] rounded-lg border border-gray-800 overflow-x-auto">
//             <table className="w-full text-sm">
//               <thead className="bg-[#0b1220] text-gray-300">
//                 <tr>
//                   <th className="p-3 border border-gray-800 text-left">Id</th>
//                   <th className="p-3 border border-gray-800 text-left">Offer Type</th>
//                   <th className="p-3 border border-gray-800 text-left">Date</th>
//                   <th className="p-3 border border-gray-800 text-left">Name</th>
//                   <th className="p-3 border border-gray-800 text-left">Category</th>
//                   <th className="p-3 border border-gray-800 text-left">Lead Rate</th>
//                   <th className="p-3 border border-gray-800 text-left">Vendor Name</th>
//                   <th className="p-3 border border-gray-800 text-left">Thumbnail</th>
//                   <th className="p-3 border border-gray-800 text-left">Action</th>
//                 </tr>
//               </thead>

//               <tbody>
//                 {filteredProducts.map((item) => (
//                   <Fragment key={item.id}>
//                     <tr className="hover:bg-[#0b1220] transition">
//                       <td className="p-3 border border-gray-800 font-mono text-xs">
//                         {item.id.toString().substring(0, 8)}...
//                       </td>
//                       <td className="p-3 border border-gray-800">Normal</td>
//                       <td className="p-3 border border-gray-800">
//                         {new Date(item.created_at).toLocaleDateString()}
//                       </td>
//                       <td className="p-3 border border-gray-800 font-medium">{item.title}</td>
//                       <td className="p-3 border border-gray-800">Default</td>
//                       <td className="p-3 border border-gray-800 text-green-400">
//                         ${parseFloat(item.price_usd || 0).toLocaleString()}
//                       </td>
//                       <td className="p-3 border border-gray-800">Admin</td>
//                       <td className="p-3 border border-gray-800">
//                         <div className="w-10 h-10 bg-gray-800 rounded overflow-hidden">
//                           {item.primary_image_url ? (
//                             <img 
//                               src={item.primary_image_url} 
//                               alt={item.title}
//                               className="w-full h-full object-cover"
//                             />
//                           ) : (
//                             <div className="w-full h-full flex items-center justify-center">
//                               <ImageIcon size={16} className="text-gray-600" />
//                             </div>
//                           )}
//                         </div>
//                       </td>
//                       <td className="p-3 border border-gray-800">
//                         <div className="flex gap-1">
//                           <button
//                             onClick={() => handleView(item)}
//                             className="bg-green-600 hover:bg-green-700 text-white p-1.5 rounded"
//                             title="View"
//                           >
//                             <Eye size={14} />
//                           </button>
//                           <button
//                             onClick={() => handleEdit(item)}
//                             className="bg-blue-600 hover:bg-blue-700 text-white p-1.5 rounded"
//                             title="Edit"
//                           >
//                             <Pencil size={14} />
//                           </button>
//                           <button
//                             onClick={() => handleDelete(item.id)}
//                             className="bg-red-600 hover:bg-red-700 text-white p-1.5 rounded"
//                             title="Delete"
//                           >
//                             <Trash2 size={14} />
//                           </button>
//                         </div>
//                       </td>
//                     </tr>
                    
//                     {/* Features Row (shown when product is selected) */}
//                     {expandedProduct === item.id && productFeatures.length > 0 && (
//                       <tr className="bg-[#0b1220]">
//                         <td colSpan="9" className="p-3 border border-gray-800">
//                           <div className="space-y-2">
//                             <h3 className="text-sm font-semibold text-indigo-400 mb-2">Product Features</h3>
//                             <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
//                               {productFeatures.map((feature) => (
//                                 <div key={feature.id} className="bg-[#111827] p-3 rounded border border-gray-700">
//                                   <div className="flex items-start gap-2">
//                                     <div className="mt-1">
//                                       {getIcon(feature.icon_name)}
//                                     </div>
//                                     <div>
//                                       <h4 className="text-sm font-medium text-white">{feature.title}</h4>
//                                       <p className="text-xs text-gray-400 mt-1">{feature.description}</p>
//                                     </div>
//                                   </div>
//                                 </div>
//                               ))}
//                             </div>
//                           </div>
//                         </td>
//                       </tr>
//                     )}
//                   </Fragment>
//                 ))}
//               </tbody>
//             </table>
//           </div>
//         </>
//       ) : (
//         /* Add/Edit Product Form */
//         <div className="bg-slate-950 text-gray-200 p-6">
//           <h1 className="text-xl font-semibold mb-6 text-white">
//             {selectedProduct ? 'Edit Product' : 'Add Product'}
//           </h1>

//           <form onSubmit={handleSubmit}>
//             <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
//               {/* LEFT: Product Details */}
//               <div className="lg:col-span-2 bg-slate-900 rounded-lg border border-white/10 p-5">
//                 <h2 className="font-medium mb-4 text-white">Products Details</h2>

//                 {/* Product Title */}
//                 <div className="mb-4">
//                   <label className="text-sm font-medium">
//                     Product Title<span className="text-red-400">*</span>
//                   </label>
//                   <input
//                     type="text"
//                     placeholder="Enter Product Title"
//                     value={formData.title}
//                     onChange={(e) => setFormData({...formData, title: e.target.value})}
//                     className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
//                     required
//                   />
//                 </div>

//                 {/* URL Slug */}
//                 <div className="mb-4">
//                   <label className="text-sm font-medium">URL Slug</label>
//                   <input
//                     type="text"
//                     placeholder="Enter URL Slug (e.g., my-product-post)"
//                     value={formData.slug}
//                     onChange={(e) => setFormData({...formData, slug: e.target.value})}
//                     className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
//                   />
//                   <p className="text-xs text-gray-400 mt-1">
//                     SEO-friendly URL. If left empty, will be generated from title.
//                   </p>
//                 </div>

//                 {/* Short Description */}
//                 <div className="mb-4">
//                   <label className="text-sm font-medium">Short Description</label>
//                   <textarea
//                     rows={2}
//                     placeholder="Enter short description"
//                     value={formData.short_description}
//                     onChange={(e) => setFormData({...formData, short_description: e.target.value})}
//                     className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
//                   />
//                 </div>

//                 {/* Product Description */}
//                 <div className="mb-4">
//                   <h1 className="text-xl font-bold mb-2 mt-6 bg-linear-to-r from-purple-400 via-cyan-400 to-purple-400 bg-clip-text text-transparent">
//                     Product Description
//                   </h1>
//                   <div className="w-full max-w-5xl bg-[#111118] border border-white/10 rounded-2xl shadow-2xl">
//                     <CustomEditor 
//                       value={formData.full_description}
//                       onChange={(content) => setFormData({...formData, full_description: content})}
//                     />
//                   </div>
//                 </div>

//                 {/* Product Video URL */}
//                 <div className="mb-4">
//                   <label className="text-sm font-medium">Product Video URL</label>
//                   <input
//                     type="text"
//                     placeholder="https://youtube.com/watch?v=xxxxx"
//                     value={formData.video_url}
//                     onChange={(e) => setFormData({...formData, video_url: e.target.value})}
//                     className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
//                   />
//                   <p className="text-xs text-gray-400 mt-1">
//                     Accepts YouTube, Vimeo, or direct MP4 links.
//                   </p>
//                 </div>

//                 {/* Pricing */}
//                 <div className="grid grid-cols-2 gap-4 mb-4">
//                   <div>
//                     <label className="text-sm font-medium">Price (USD)</label>
//                     <input
//                       type="number"
//                       step="0.01"
//                       placeholder="0.00"
//                       value={formData.price_usd}
//                       onChange={(e) => setFormData({...formData, price_usd: e.target.value})}
//                       className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
//                     />
//                   </div>
//                   <div>
//                     <label className="text-sm font-medium">Price (INR)</label>
//                     <input
//                       type="number"
//                       step="0.01"
//                       placeholder="0.00"
//                       value={formData.price_inr}
//                       onChange={(e) => setFormData({...formData, price_inr: e.target.value})}
//                       className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
//                     />
//                   </div>
//                 </div>

//                 {/* Tags */}
//                 <div>
//                   <label className="text-sm font-medium">
//                     Tags<span className="text-red-400">*</span>
//                   </label>
//                   <input
//                     type="text"
//                     placeholder="Enter comma separated tags"
//                     value={formData.tags}
//                     onChange={(e) => setFormData({...formData, tags: e.target.value})}
//                     className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
//                   />
//                   <p className="text-xs text-gray-400 mt-1">
//                     Maximum of 15 keywords. Use lowercase.
//                   </p>
//                 </div>
//               </div>

//               {/* RIGHT: Product Image */}
//               <div className="bg-slate-900 rounded-lg border border-white/10 p-5">
//                 <h2 className="font-medium mb-4 text-white">Product Image</h2>

//                 {/* Product Image */}
//                 <div className="mb-6">
//                   <label className="text-sm font-medium">
//                     Product Image{" "}
//                     <span className="text-red-400">(925 × 661px)*</span>
//                   </label>

//                   <input
//                     type="file"
//                     className="block mt-2 text-sm text-gray-400"
//                     onChange={(e) => setFeatured(e.target.files[0])}
//                   />

//                   <p className="text-xs text-gray-400 mt-1">
//                     Appears in product listings and details
//                   </p>

//                   <div className="mt-3 w-full h-48 border border-white/10 flex items-center justify-center text-xs text-gray-500 bg-slate-950 rounded">
//                     {featured ? (
//                       <img
//                         src={previewImage(featured)}
//                         className="object-cover w-full h-full rounded"
//                         alt="Preview"
//                       />
//                     ) : formData.featured_preview ? (
//                       <img
//                         src={formData.featured_preview}
//                         className="object-cover w-full h-full rounded"
//                         alt="Product"
//                       />
//                     ) : (
//                       "IMAGE PREVIEW"
//                     )}
//                   </div>
//                 </div>

//                 {/* Alt Text */}
//                 <div className="mb-6">
//                   <label className="text-sm font-medium">Image Alt Text</label>
//                   <input
//                     type="text"
//                     placeholder="Describe the image for accessibility"
//                     className="w-full mt-1 bg-slate-950 border border-white/10 rounded px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
//                   />
//                 </div>

//                 {/* Open Graph Image */}
//                 <div>
//                   <label className="text-sm font-medium">
//                     Open Graph Image{" "}
//                     <span className="text-red-400">(1200 × 630px)</span>
//                   </label>
//                   <input 
//                     type="file" 
//                     className="block mt-2 text-sm text-gray-400"
//                     onChange={(e) => setOgImage(e.target.files[0])}
//                   />
//                 </div>
//               </div>
//             </div>

//             {/* SEO Metadata Section */}
//             <div className="mt-8 bg-slate-900 rounded-xl shadow-xl p-6 border border-slate-800">
//               <h1 className="text-2xl font-semibold mb-6 text-white">
//                 SEO Metadata
//               </h1>

//               <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
//                 {/* LEFT SIDE */}
//                 <div className="lg:col-span-2 space-y-6">
//                   <div>
//                     <label className="block text-sm font-medium mb-1 text-slate-300">
//                       Meta Title
//                     </label>
//                     <input
//                       type="text"
//                       placeholder="Meta Title (50-60 characters)"
//                       value={formData.meta_title}
//                       onChange={(e) => setFormData({...formData, meta_title: e.target.value})}
//                       className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                     />
//                     <p className="text-xs text-slate-500 mt-1">
//                       Recommended: 50–60 characters
//                     </p>
//                   </div>

//                   <div>
//                     <label className="block text-sm font-medium mb-1 text-slate-300">
//                       Meta Description
//                     </label>
//                     <textarea
//                       rows={4}
//                       placeholder="Meta Description (150-160 characters)"
//                       value={formData.meta_description}
//                       onChange={(e) => setFormData({...formData, meta_description: e.target.value})}
//                       className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                     />
//                     <p className="text-xs text-slate-500 mt-1">
//                       Recommended: 150–160 characters
//                     </p>
//                   </div>

//                   <div>
//                     <label className="block text-sm font-medium mb-1 text-slate-300">
//                       Meta Keywords
//                     </label>
//                     <textarea
//                       rows={2}
//                       placeholder="Comma separated keywords"
//                       value={formData.meta_keywords}
//                       onChange={(e) => setFormData({...formData, meta_keywords: e.target.value})}
//                       className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                     />
//                   </div>

//                   <div>
//                     <label className="block text-sm font-medium mb-1 text-slate-300">
//                       Canonical URL
//                     </label>
//                     <input
//                       type="url"
//                       placeholder="https://example.com/blog-post"
//                       value={formData.canonical_url}
//                       onChange={(e) => setFormData({...formData, canonical_url: e.target.value})}
//                       className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                     />
//                     <p className="text-xs text-slate-500 mt-1">
//                       Use if this content appears on multiple URLs
//                     </p>
//                   </div>
//                 </div>

//                 {/* RIGHT SIDE */}
//                 <div className="space-y-6">
//                   <div>
//                     <label className="text-sm font-medium text-slate-300">
//                       Open Graph Image
//                       <span className="text-red-400"> (1200 × 630px)</span>
//                     </label>

//                     <input
//                       type="file"
//                       accept="image/*"
//                       onChange={(e) => setOgImage(e.target.files[0])}
//                       className="mt-2 text-sm text-slate-400"
//                     />

//                     <div className="mt-3 border border-slate-700 rounded-lg h-40 flex items-center justify-center bg-slate-800">
//                       {ogImage ? (
//                         <img
//                           src={previewImage(ogImage)}
//                           alt="OG Preview"
//                           className="h-full object-contain"
//                         />
//                       ) : (
//                         <span className="text-slate-500">IMAGE PREVIEW</span>
//                       )}
//                     </div>
//                   </div>

//                   <div>
//                     <label className="text-sm font-medium text-slate-300">
//                       Twitter Card Image
//                       <span className="text-red-400"> (1200 × 675px)</span>
//                     </label>

//                     <input
//                       type="file"
//                       accept="image/*"
//                       onChange={(e) => setTwitterImage(e.target.files[0])}
//                       className="mt-2 text-sm text-slate-400"
//                     />

//                     <div className="mt-3 border border-slate-700 rounded-lg h-40 flex items-center justify-center bg-slate-800">
//                       {twitterImage ? (
//                         <img
//                           src={previewImage(twitterImage)}
//                           alt="Twitter Preview"
//                           className="h-full object-contain"
//                         />
//                       ) : (
//                         <span className="text-slate-500">IMAGE PREVIEW</span>
//                       )}
//                     </div>
//                   </div>

//                   <div>
//                     <label className="block text-sm font-medium mb-2 text-slate-300">
//                       Status
//                     </label>
//                     <div className="flex gap-6 text-slate-300">
//                       <label className="flex items-center gap-2">
//                         <input
//                           type="radio"
//                           checked={status === "active"}
//                           onChange={() => setStatus("active")}
//                           className="accent-indigo-500"
//                         />
//                         Active
//                       </label>

//                       <label className="flex items-center gap-2">
//                         <input
//                           type="radio"
//                           checked={status === "inactive"}
//                           onChange={() => setStatus("inactive")}
//                           className="accent-indigo-500"
//                         />
//                         Inactive
//                       </label>
//                     </div>
//                   </div>
//                 </div>
//               </div>

//               {/* SOCIAL MEDIA & SCHEMA */}
//               <div className="mt-12">
//                 <h2 className="text-lg font-semibold text-white mb-4">
//                   Social Media & Schema
//                 </h2>

//                 {/* Tabs */}
//                 <div className="flex flex-wrap gap-2 border-b border-slate-700 mb-6">
//                   {[
//                     { key: "openGraph", label: "Open Graph" },
//                     { key: "twitter", label: "Twitter" },
//                     { key: "schema", label: "Schema Markup" },
//                   ].map((item) => (
//                     <button
//                       key={item.key}
//                       type="button"
//                       onClick={() => setTab(item.key)}
//                       className={`px-4 py-2 rounded-t-lg text-sm font-medium transition
//                         ${
//                           tab === item.key
//                             ? "bg-slate-800 text-white border border-b-0 border-slate-700"
//                             : "text-slate-400 hover:text-white"
//                         }`}
//                     >
//                       {item.label}
//                     </button>
//                   ))}
//                 </div>

//                 {/* Content Box */}
//                 <div className="bg-slate-800 border border-slate-700 rounded-lg p-6 space-y-6">
//                   {tab === "openGraph" && (
//                     <>
//                       <div>
//                         <label className="block text-sm font-medium text-slate-300 mb-1">
//                           OG Title
//                         </label>
//                         <input
//                           type="text"
//                           placeholder="Open Graph Title"
//                           value={formData.og_title}
//                           onChange={(e) => setFormData({...formData, og_title: e.target.value})}
//                           className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                         />
//                       </div>

//                       <div>
//                         <label className="block text-sm font-medium text-slate-300 mb-1">
//                           OG Description
//                         </label>
//                         <textarea
//                           rows={4}
//                           placeholder="Open Graph Description"
//                           value={formData.og_description}
//                           onChange={(e) => setFormData({...formData, og_description: e.target.value})}
//                           className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                         />
//                       </div>
//                     </>
//                   )}

//                   {tab === "twitter" && (
//                     <>
//                       <div>
//                         <label className="block text-sm font-medium text-slate-300 mb-1">
//                           Twitter Title
//                         </label>
//                         <input
//                           type="text"
//                           placeholder="Twitter Card Title"
//                           value={formData.twitter_title}
//                           onChange={(e) => setFormData({...formData, twitter_title: e.target.value})}
//                           className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                         />
//                       </div>

//                       <div>
//                         <label className="block text-sm font-medium text-slate-300 mb-1">
//                           Twitter Description
//                         </label>
//                         <textarea
//                           rows={4}
//                           placeholder="Twitter Card Description"
//                           value={formData.twitter_description}
//                           onChange={(e) => setFormData({...formData, twitter_description: e.target.value})}
//                           className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                         />
//                       </div>
//                     </>
//                   )}

//                   {tab === "schema" && (
//                     <div>
//                       <label className="block text-sm font-medium text-slate-300 mb-1">
//                         Schema Markup (JSON-LD)
//                       </label>
//                       <textarea
//                         rows={6}
//                         placeholder='{
//   "@context": "https://schema.org",
//   "@type": "Product"
// }'
//                         value={formData.schema_markup}
//                         onChange={(e) => setFormData({...formData, schema_markup: e.target.value})}
//                         className="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 font-mono text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
//                       />
//                     </div>
//                   )}
//                 </div>
//               </div>

//               {/* Action Buttons */}
//               <div className="mt-8 flex justify-end gap-4">
//                 <button
//                   type="button"
//                   onClick={() => {
//                     setShowAddForm(false);
//                     setSelectedProduct(null);
//                   }}
//                   className="bg-gray-600 hover:bg-gray-700 transition text-white px-8 py-2 rounded-lg"
//                 >
//                   Cancel
//                 </button>
//                 <button
//                   type="submit"
//                   className="bg-indigo-600 hover:bg-indigo-700 transition text-white px-8 py-2 rounded-lg"
//                 >
//                   Save Product
//                 </button>
//               </div>
//             </div>
//           </form>

//           {/* Product Features Section (shown when editing) */}
//           {selectedProduct && productFeatures.length > 0 && (
//             <div className="mt-8 bg-slate-900 rounded-xl shadow-xl p-6 border border-slate-800">
//               <h2 className="text-lg font-semibold text-white mb-4">Product Features</h2>
//               <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
//                 {productFeatures.map((feature) => (
//                   <div key={feature.id} className="bg-slate-800 p-4 rounded-lg border border-slate-700">
//                     <div className="flex items-start gap-3">
//                       <div className="p-2 bg-slate-700 rounded-lg">
//                         {getIcon(feature.icon_name)}
//                       </div>
//                       <div>
//                         <h3 className="text-sm font-medium text-white">{feature.title}</h3>
//                         <p className="text-xs text-gray-400 mt-1">{feature.description}</p>
//                       </div>
//                     </div>
//                   </div>
//                 ))}
//               </div>
//             </div>
//           )}
//         </div>
//       )}
//     </div>
//   );
// }