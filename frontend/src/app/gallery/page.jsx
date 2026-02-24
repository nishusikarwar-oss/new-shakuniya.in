"use client";

import { useEffect, useState } from "react";
import { Play, Eye } from "lucide-react";
import Footer from "@/components/Footer";
import event from "@/images/event.png"
import Navbar from "@/components/Navbar";

const Gallery = () => {
  const [galleryItems, setGalleryItems] = useState([]);
  const [categories, setCategories] = useState(["All"]);
  const [categoryMap, setCategoryMap] = useState({});
  const [activeCategory, setActiveCategory] = useState("All");
  const [hoveredItem, setHoveredItem] = useState(null);
  const [loading, setLoading] = useState(true);
  const [imagesLoaded, setImagesLoaded] = useState({});

  useEffect(() => {
    const loadGalleryData = async () => {
      try {
        setLoading(true);
        
        const [galleryRes, categoriesRes] = await Promise.all([
          fetch("http://127.0.0.1:8000/api/gallery-images"),
          fetch("http://127.0.0.1:8000/api/categories")
        ]);

        const galleryJson = await galleryRes.json();
        const categoriesJson = await categoriesRes.json();

        // ✅ Process Categories (Pagination structure)
        let catMap = {};
        let catList = ["All"];
        
        if (categoriesJson.success && categoriesJson.data?.data) {
          const cats = categoriesJson.data.data; // Access nested data array
          
          cats.forEach(cat => {
            if (cat.id && cat.category_name) {
              catMap[cat.id] = cat.category_name;
              if (cat.category_name !== "All") {
                catList.push(cat.category_name);
              }
            }
          });
        }
        
        console.log("Category Map:", catMap);
        setCategoryMap(catMap);
        setCategories(catList);

        // ✅ Process Gallery Images (Direct array)
        let items = [];
        if (galleryJson.success && galleryJson.data) {
          items = galleryJson.data; // Direct array
        }

        // ✅ Format items
        const formatted = items.map((item) => {
          const categoryName = catMap[item.category_id] || 'Uncategorized';
          
          // Ensure image URL is correct
          let imageUrl = item.image_url;
          if (imageUrl && !imageUrl.startsWith('http')) {
            imageUrl = `http://127.0.0.1:8000${imageUrl.startsWith('/') ? '' : '/'}${imageUrl}`;
          }
          
          return {
            ...item,
            id: item.id,
            image: imageUrl,
            category: categoryName,
            // Ensure these fields exist
            displayName: item.image_name || 'Untitled',
            displayDate: item.created_at_formatted || item.created_at || '',
          };
        });

        setGalleryItems(formatted);

      } catch (error) {
        console.error("Error:", error);
      } finally {
        setLoading(false);
      }
    };

    loadGalleryData();
  }, []);

  // Handle image load success
  const handleImageLoad = (id) => {
    setImagesLoaded(prev => ({ ...prev, [id]: true }));
  };

  // Handle image load error
  const handleImageError = (id) => {
    console.log(`Image failed to load: ${id}`);
    setImagesLoaded(prev => ({ ...prev, [id]: false }));
  };

  // Filter items
  const filteredItems = activeCategory === "All"
    ? galleryItems
    : galleryItems.filter((item) => item.category === activeCategory);

  return (
    <div className="min-h-screen bg-[#0a0a0f]">
<Navbar/>    

      {/* Hero Section */}
      <section className="pt-32 pb-16 relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-b from-purple-900/10 via-transparent to-cyan-900/5" />
        <div className="container mx-auto px-4 relative z-10 text-center">
          <span className="text-purple-400 uppercase tracking-widest text-sm font-semibold">
            OUR PORTFOLIO
          </span>
          <h1 className="text-5xl font-bold mt-4 mb-6">
            <span className="text-white">Our </span>
            <span className="bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400 bg-clip-text text-transparent">
              Gallery
            </span>
          </h1>
          <p className="text-gray-400 max-w-2xl mx-auto">
            Explore our collection of innovative digital solutions and stunning interfaces.
          </p>
        </div>
      </section>

      {/* Filters */}
      <section className="pb-12">
        <div className="flex justify-center gap-3 flex-wrap">
          {categories.map((cat, index) => (
            <button
              key={index}
              onClick={() => setActiveCategory(cat)}
              className={`px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 ${
                activeCategory === cat
                  ? "bg-gradient-to-r from-purple-500 to-cyan-500 text-white shadow-lg shadow-purple-500/25"
                  : "bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white border border-white/10"
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
      </section>

      {/* Gallery Grid */}
      <section className="pb-24">
        <div className="container mx-auto px-4">
          {loading ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {[1, 2, 3, 4, 5, 6, 7, 8].map((i) => (
                <div 
                  key={i} 
                  className="h-60 rounded-2xl bg-white/5 animate-pulse border border-white/5"
                />
              ))}
            </div>
          ) : filteredItems.length === 0 ? (
            <div className="text-center py-20">
              <p className="text-gray-400">
                No items found in <span className="text-purple-400">{activeCategory}</span> category
              </p>
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {filteredItems.map((item) => (
                <div
                  key={item.id}
                  className="relative group rounded-2xl overflow-hidden bg-white/5 border border-white/5 aspect-square"
                  onMouseEnter={() => setHoveredItem(item.id)}
                  onMouseLeave={() => setHoveredItem(null)}
                >
                  {/* Image with loading state */}
                  <div className="relative w-full h-full">
                    {!imagesLoaded[item.id] && (
                      <div className="absolute inset-0 flex items-center justify-center bg-white/5">
                        <div className="w-8 h-8 border-2 border-purple-500/30 border-t-purple-500 rounded-full animate-spin"></div>
                      </div>
                    )}
                    <img
                      src={event.src}
                      alt={item.displayName}
                      className={`w-full h-full object-cover transition-all duration-700 ${
                        hoveredItem === item.id ? "scale-110 blur-sm" : "scale-100"
                      } ${imagesLoaded[item.id] ? 'opacity-100' : 'opacity-0'}`}
                      onLoad={() => handleImageLoad(item.id)}
                      onError={(e) => {
                        handleImageError(item.id);
                        e.target.src = "https://via.placeholder.com/400x400?text=Image+Not+Found";
                      }}
                    />
                  </div>

                  {/* Overlay - Fixed hover effect */}
                  <div className={`absolute inset-0 bg-gradient-to-t from-black/95 via-black/70 to-transparent flex flex-col items-center justify-center transition-all duration-500 ${
                    hoveredItem === item.id ? "opacity-100" : "opacity-0"
                  }`}>
                    {/* Icon */}
                    <div className="w-14 h-14 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center mb-4 border border-white/20 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                      <Eye className="text-white w-6 h-6" />
                    </div>

                    {/* Image Name */}
                    <h3 className="text-white font-semibold text-center px-4 text-lg mb-2 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-100">
                      {item.displayName}
                    </h3>

                    {/* Category Badge */}
                    <span className="text-xs text-purple-400 px-3 py-1 bg-purple-500/20 rounded-full mb-2 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-200">
                      {item.category}
                    </span>

                    {/* Created Date */}
                    {item.created_at_formatted && (
                      <span className="text-xs text-gray-300 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-300">
                        {item.created_at_formatted}
                      </span>
                    )}
                    
                    {/* Raw Created At (if needed) */}
                    {item.created_at && !item.created_at_formatted && (
                      <span className="text-xs text-gray-400 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-300">
                        {new Date(item.created_at).toLocaleDateString()}
                      </span>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </section>

      <Footer />
    </div>
  );
};

export default Gallery;