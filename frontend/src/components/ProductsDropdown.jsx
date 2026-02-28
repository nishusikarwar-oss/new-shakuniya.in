// components/ProductsDropdown.jsx
"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { ChevronDown, ArrowRight, Zap, MessageSquare, Users, ShieldCheck, HelpCircle } from "lucide-react";

// Icon mapping for dynamic icons
const IconMap = {
  Zap: Zap,
  MessageSquare: MessageSquare,
  Users: Users,
  ShieldCheck: ShieldCheck,
  Globe: MessageSquare, // Fallback icons
  Mail: MessageSquare,
  Phone: MessageSquare,
  Bot: Zap,
  Video: Users,
  Lock: ShieldCheck,
};

const ProductsDropdown = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [dropdownProducts, setDropdownProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchDropdownProducts();
  }, []);

  const fetchDropdownProducts = async () => {
    try {
      setLoading(true);
      // Fetch products from API - limit 4 for dropdown
      const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/products?limit=4`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const responseData = await response.json();
      
      // Handle different response formats
      let productsArray = [];
      
      if (responseData.success === true && responseData.data) {
        if (responseData.data.data && Array.isArray(responseData.data.data)) {
          productsArray = responseData.data.data;
        } else if (Array.isArray(responseData.data)) {
          productsArray = responseData.data;
        }
      } else if (Array.isArray(responseData)) {
        productsArray = responseData;
      }
      
      // Map products to dropdown format (only first 4)
      const mappedProducts = productsArray.slice(0, 4).map((product, index) => ({
        id: product.id || index,
        slug: product.slug || '',
        title: product.title || product.name || 'Product',
        shortDescription: product.short_description || product.description?.substring(0, 50) + '...' || 'View product details',
        icon: IconMap[product.icon_name] || HelpCircle,
      }));
      
      setDropdownProducts(mappedProducts);
      
    } catch (err) {
      console.error('Error fetching dropdown products:', err);
      setError(err.message);
      
      // Fallback to empty array if error
      setDropdownProducts([]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div
      className="relative"
      onMouseEnter={() => setIsOpen(true)}
      onMouseLeave={() => setIsOpen(false)}
    >
      {/* Trigger */}
      <button
        type="button"
        className="flex items-center gap-1 text-muted-foreground hover:text-foreground transition-colors duration-300 text-sm font-medium"
      >
        Products
        <ChevronDown
          size={14}
          className={`transition-transform duration-300 ${
            isOpen ? "rotate-180" : ""
          }`}
        />
      </button>

      {/* Dropdown Menu */}
      <div
        className={`absolute top-full left-1/2 -translate-x-1/2 pt-4 transition-all duration-300 ease-out ${
          isOpen
            ? "opacity-100 translate-y-0 pointer-events-auto"
            : "opacity-0 -translate-y-2 pointer-events-none"
        }`}
      >
        <div className="dark-glass p-2 min-w-[280px] shadow-2xl shadow-black/50 relative rounded-2xl">
          {/* Arrow indicator */}
          <div className="absolute -top-[6px] left-1/2 -translate-x-1/2 w-3 h-3 rotate-45 bg-[#0a0a0f] border-l border-t border-white/10" />

          <div className="space-y-1">
            {/* Loading State */}
            {loading && (
              <div className="px-4 py-8 text-center">
                <div className="w-8 h-8 border-2 border-purple-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                <p className="text-xs text-muted-foreground">Loading products...</p>
              </div>
            )}

            {/* Error State */}
            {error && !loading && (
              <div className="px-4 py-6 text-center">
                <p className="text-xs text-red-400 mb-2">Failed to load products</p>
                <button 
                  onClick={fetchDropdownProducts}
                  className="text-xs text-purple-400 hover:text-purple-300 underline"
                >
                  Try again
                </button>
              </div>
            )}

            {/* Products List */}
            {!loading && !error && dropdownProducts.length === 0 && (
              <div className="px-4 py-6 text-center">
                <p className="text-xs text-muted-foreground">No products available</p>
              </div>
            )}

            {!loading && !error && dropdownProducts.map((product) => {
              const IconComponent = product.icon;

              return (
                <Link
                  key={product.id}
                  href={`/products/${product.slug}`}
                  className="flex items-center gap-3 px-4 py-3 rounded-xl border border-transparent hover:bg-white/5 hover:border-[#00d9ff]/20 transition-all duration-200 group"
                >
                  <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500/20 to-cyan-500/20 flex items-center justify-center group-hover:from-purple-500/30 group-hover:to-cyan-500/30 transition-all duration-200">
                    {IconComponent && (
                      <IconComponent
                        size={20}
                        className="text-purple-400 group-hover:text-purple-300 transition-colors"
                      />
                    )}
                  </div>

                  <div>
                    <span className="block text-sm font-medium text-foreground group-hover:text-white transition-colors">
                      {product.title}
                    </span>
                    <span className="block text-xs text-muted-foreground">
                      {product.shortDescription}
                    </span>
                  </div>
                </Link>
              );
            })}

            {/* View More products - Always visible */}
            <Link
              href="/products"
              className="flex items-center justify-between px-4 py-3 mt-2 rounded-xl bg-gradient-to-r from-purple-500/10 to-cyan-500/10 hover:from-purple-500/20 hover:to-cyan-500/20 transition-all duration-200 group border-t border-white/5"
            >
              <span className="text-sm font-medium text-primary group-hover:text-white transition-colors">
                View More Products
              </span>
              <ArrowRight
                size={16}
                className="text-primary group-hover:text-white transition-all duration-300 group-hover:translate-x-1"
              />
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductsDropdown;