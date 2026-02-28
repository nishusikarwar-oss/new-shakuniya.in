
"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { ArrowRight, ArrowLeft } from "lucide-react";
import { Button } from "@/components/ui/button";
import * as Icons from "lucide-react";

// Icon mapping for dynamic icons
const IconMap = {
  Zap: Icons.Zap,
  MessageSquare: Icons.MessageSquare,
  Users: Icons.Users,
  ShieldCheck: Icons.ShieldCheck,
  Globe: Icons.Globe,
  Mail: Icons.Mail,
  Phone: Icons.Phone,
  Bot: Icons.Bot,
  Video: Icons.Video,
  Lock: Icons.Lock,
};

export default function Products() {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      setLoading(true);
      // Fetch products from API
      const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/products`);
      
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
      
      // Fetch features and images for each product
      const productsWithDetails = await Promise.all(
        productsArray.map(async (product) => {
          try {
            // Fetch features for this product
            const featuresResponse = await fetch(
              `${process.env.NEXT_PUBLIC_API_URL}/product-features?product_id=${product.id}`
            );
            
            let featuresData = [];
            if (featuresResponse.ok) {
              const featuresJson = await featuresResponse.json();
              
              if (featuresJson.success === true && featuresJson.data) {
                if (Array.isArray(featuresJson.data)) {
                  featuresData = featuresJson.data;
                } else if (featuresJson.data.data && Array.isArray(featuresJson.data.data)) {
                  featuresData = featuresJson.data.data;
                }
              } else if (Array.isArray(featuresJson)) {
                featuresData = featuresJson;
              }
            }
            
            // Take first 3 features
            const features = featuresData.slice(0, 3);
            
            // Fetch product images
            const imagesResponse = await fetch(
              `${process.env.NEXT_PUBLIC_API_URL}/product-images?product_id=${product.id}`
            );
            
            let imagesData = [];
            if (imagesResponse.ok) {
              const imagesJson = await imagesResponse.json();
              
              if (imagesJson.success === true && imagesJson.data) {
                if (Array.isArray(imagesJson.data)) {
                  imagesData = imagesJson.data;
                } else if (imagesJson.data.data && Array.isArray(imagesJson.data.data)) {
                  imagesData = imagesJson.data.data;
                }
              } else if (Array.isArray(imagesJson)) {
                imagesData = imagesJson;
              }
            }
            
            const image = imagesData.length > 0 
              ? (imagesData[0].image_url || imagesData[0].url) 
              : (product.image_url || null);
            
            return {
              id: product.id,
              slug: product.slug || '',
              name: product.name || product.title || 'Untitled Product',
              title: product.title || product.name || 'Untitled Product',
              description: product.description || '',
              short_description: product.short_description || 
                (product.description ? product.description.substring(0, 100) + '...' : 'No description available'),
              features: features,
              image: image,
              icon: IconMap[product.icon_name] || Icons.HelpCircle
            };
          } catch (err) {
            console.error(`Error fetching details for product ${product?.id}:`, err);
            return {
              id: product?.id || Math.random(),
              slug: product?.slug || '',
              name: product?.name || product?.title || 'Untitled Product',
              title: product?.title || product?.name || 'Untitled Product',
              description: product?.description || '',
              short_description: product?.short_description || 
                (product?.description ? product.description.substring(0, 100) + '...' : 'No description available'),
              features: [],
              image: null,
              icon: Icons.HelpCircle
            };
          }
        })
      );
      
      // Filter out any null products
      const validProducts = productsWithDetails.filter(p => p !== null);
      setProducts(validProducts);
      
    } catch (err) {
      setError(err.message);
      console.error('Error fetching products:', err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-background flex items-center justify-center">
        <div className="text-center">
          <div className="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-muted-foreground">Loading products...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-background flex items-center justify-center">
        <div className="text-center">
          <p className="text-red-500 mb-4">Error: {error}</p>
          <Button onClick={fetchProducts} className="mx-auto">
            Try Again
          </Button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#0a0a0f]">
      {/* Hero Section */}
      <section className="relative pt-32 pb-20 overflow-hidden">
        <div className="container mx-auto px-4 relative z-10 text-center">
          <span className="text-purple-400 uppercase tracking-[0.3em] text-sm font-bold mb-4 block animate-fade-in">
            OUR INNOVATION
          </span>
          <h1 className="text-5xl md:text-7xl font-bold mb-6 text-white tracking-tight uppercase">
            Our <span className="text-transparent animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">Products</span>
          </h1>
          <p className="text-gray-300 text-lg md:text-xl max-w-3xl mx-auto font-medium">
            Discover our range of cutting-edge digital products designed to
            transform your business. From AI-powered automation to
            comprehensive management solutions, we build for the future.
          </p>

          <div className="mt-8 flex justify-center">
            {/* Back to Home Button */}
            <Link href="/" className="group">
              <Button className="flex items-center gap-2 bg-gradient-to-r from-purple-500 to-cyan-500 text-white rounded-lg hover:shadow-lg transition-all duration-300">
                <ArrowLeft
                  size={16}
                  className="transition-transform duration-300 group-hover:-translate-x-1"
                />
                Back to Home
              </Button>
            </Link>
          </div>
        </div>
      </section>

      {/* Products Grid */}
      <section className="py-16 px-12">
        <div className="container mx-auto px-4 lg:px-8">
          {products.length === 0 ? (
            <div className="text-center py-16">
              <p className="text-muted-foreground">No products found.</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {products.map((product) => {
                const IconComponent = product.icon;

                return (
                  <Link
                    key={product.id}
                    href={`/products/${product.slug}`}
                    className="group block h-full"
                  >
                    <div className="glass-card p-8 rounded-[2rem] hover:bg-[#1a1a2e]/80 transition-all duration-500 hover:border-[#00d9ff]/50 hover:shadow-[0_0_40px_rgba(0,217,255,0.15)] hover:-translate-y-2 h-full flex flex-col">
                      {/* Icon */}
                      <div className="w-16 h-16 bg-gradient-to-br from-purple-500/20 to-cyan-500/20 border border-white/10 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-gradient-to-br group-hover:from-purple-500 group-hover:to-cyan-500 transition-all duration-500 shadow-lg">
                        <IconComponent
                          size={28}
                          className="text-primary group-hover:text-white transition-colors"
                        />
                      </div>

                      {/* Content */}
                      <h3 className="text-2xl font-bold text-white mb-4 group-hover:text-[#00d9ff] transition-colors duration-300">
                        {product.name}
                      </h3>
                      <p className="text-gray-400 text-base mb-6 line-clamp-3 leading-relaxed group-hover:text-gray-300 transition-colors">
                        {product.short_description}
                      </p>

                      {/* Features Preview */}
                      {product.features && Array.isArray(product.features) && product.features.length > 0 && (
                        <ul className="space-y-3 mb-8 flex-grow">
                          {product.features.map((feature, index) => (
                            <li
                              key={index}
                              className="flex items-center text-gray-500 text-sm group-hover:text-gray-400 transition-colors"
                            >
                              <span className="w-1.5 h-1.5 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full mr-3 shadow-[0_0_5px_rgba(147,51,234,0.5)]" />
                              {feature.name || feature.title || feature.feature_description || 'Feature'}
                            </li>
                          ))}
                        </ul>
                      )}

                      {/* CTA */}
                      <div className="inline-flex items-center text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 group-hover:from-white group-hover:to-white transition-all duration-300 mt-auto pt-4 border-t border-white/5 uppercase tracking-widest">
                        View Product
                        <ArrowRight
                          size={14}
                          className="h-4 w-4 ml-2 group-hover:translate-x-2 transition-transform"
                        />
                      </div>
                    </div>
                  </Link>
                );
              })}
            </div>
          )}

          {/* Bottom CTA */}
          <div className="mt-16 text-center ">
            <div className="glass-card inline-block p-10 max-w-2xl rounded-[2rem] border border-white/10">
              <h2 className="text-3xl font-bold text-white mb-6">
                Need a Custom <span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">Solution?</span>
              </h2>
              <p className="text-gray-300 mb-8 text-lg leading-relaxed">
                We can build tailor-made products specifically for your unique
                business challenges. Let&apos;s discuss how we can help you
                innovate.
              </p>

              <Link href="/contact">
                <Button className="flex items-center gap-2 mx-auto px-8 py-6 text-lg bg-gradient-to-r from-purple-500 to-cyan-500 hover:shadow-[0_0_20px_rgba(147,51,234,0.3)] transition-all duration-300 rounded-xl">
                  Contact Us
                  <ArrowRight size={20} />
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}