// app/blogs/[slug]/page.jsx
"use client";

import { useState, useEffect } from "react";
import { useParams } from "next/navigation";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import BlogCard from "@/components/blog/BlogCard";
import Link from "next/link";
import { Calendar, User, ArrowLeft, Tag } from "lucide-react";
import apiClient from "@/lib/api"; // ✅ अपना apiClient import करें
import event2 from "@/images/event2.png";

export default function BlogDetails() {
  const { slug } = useParams();
  const [blog, setBlog] = useState(null);
  const [relatedBlogs, setRelatedBlogs] = useState([]);
  const [loading, setLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);

  useEffect(() => {
    const fetchBlogData = async () => {
      try {
        setLoading(true);
        
        // ✅ API से single blog fetch करें
        const response = await apiClient.getBlogBySlug(slug);
        console.log("Blog API Response:", response); // Debug log
        
        // ✅ Check if response has data property (Laravel API structure)
        const blogData = response.data || response;
        
        if (!blogData || Object.keys(blogData).length === 0) {
          setNotFound(true);
          return;
        }

        setBlog(blogData);

        // ✅ Related blogs fetch करें
        try {
          const allBlogsResponse = await apiClient.getBlogs({
            limit: 10
          });
          
          const allBlogs = allBlogsResponse.data || allBlogsResponse;
          
          // Current blog को हटाकर 3 related blogs लें
          if (Array.isArray(allBlogs)) {
            const filtered = allBlogs
              .filter(b => b.slug !== slug)
              .slice(0, 3);
            
            setRelatedBlogs(filtered);
          }
        } catch (relatedError) {
          console.error("Error fetching related blogs:", relatedError);
          // Don't set notFound for related blogs error
        }
        
      } catch (error) {
        console.error("Error fetching blog:", error);
        setNotFound(true);
      } finally {
        setLoading(false);
      }
    };

    if (slug) {
      fetchBlogData();
    }
  }, [slug]);

  if (loading) {
    return (
      <div className="min-h-screen bg-background">
        <Navbar />
        <div className="pt-32 container mx-auto px-4 lg:px-8">
          <div className="max-w-4xl mx-auto animate-pulse">
            <div className="h-8 bg-gray-200 rounded w-3/4 mb-4"></div>
            <div className="h-4 bg-gray-200 rounded w-1/2 mb-8"></div>
            <div className="h-96 bg-gray-200 rounded-2xl mb-8"></div>
            <div className="space-y-4">
              <div className="h-4 bg-gray-200 rounded w-full"></div>
              <div className="h-4 bg-gray-200 rounded w-full"></div>
              <div className="h-4 bg-gray-200 rounded w-3/4"></div>
            </div>
          </div>
        </div>
        <Footer />
      </div>
    );
  }

  if (notFound || !blog) {
    return (
      <div className="min-h-screen bg-background">
        <Navbar />
        <div className="pt-32 container mx-auto px-4 lg:px-8 text-center">
          <h1 className="text-4xl font-bold mb-4">Blog Not Found</h1>
          <p className="text-gray-600 mb-8">
            The blog post you're looking for doesn't exist or has been removed.
          </p>
          <Link href="/blogs">
            <Button className="bg-primary text-white px-6 py-3 rounded-lg">
              Back to Blogs
            </Button>
          </Link>
        </div>
        <Footer />
      </div>
    );
  }

  // ✅ Format date properly from API response
  const formatDate = (dateString) => {
    if (!dateString) return "Date not available";
    
    try {
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return "Invalid Date";
      
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    } catch (e) {
      return "Invalid Date";
    }
  };

  // ✅ Get the correct date from API (created_at or created_at_formatted)
  const displayDate = blog.created_at_formatted || formatDate(blog.created_at);
  
  // ✅ Get the correct image URL
  const imageUrl = blog.featured_image_url || blog.featured_image || '/placeholder.jpg';

  return (
    <div className="min-h-screen bg-background">
      <Navbar />

      {/* Hero Section */}
      <section className="pt-32 pb-8">
        <div className="container mx-auto px-4 lg:px-8">

          {/* Back Button */}
          <Link href="/blogs" className="inline-block mb-6">
            <Button variant="ghost" className="flex items-center gap-2 text-muted-foreground hover:text-foreground">
              <ArrowLeft size={16} />
              Back to Blogs
            </Button>
          </Link>

          <div className="max-w-4xl mx-auto space-y-6">
            {/* Category - if available */}
            {blog.category && (
              <div className="flex items-center gap-2">
                <Tag size={16} className="text-primary" />
                <span className="text-primary font-medium text-sm">
                  {blog.category}
                </span>
              </div>
            )}

            {/* Title */}
            <h1 className="text-3xl md:text-4xl lg:text-5xl font-bold">
              {blog.title}
            </h1>

            {/* Meta */}
            <div className="flex flex-wrap gap-6 text-muted-foreground">
              {blog.author && (
                <span className="flex items-center gap-2">
                  <User size={16} /> {blog.author}
                </span>
              )}
              <span className="flex items-center gap-2">
                <Calendar size={16} /> {displayDate}
              </span>
              {blog.read_time && (
                <span className="flex items-center gap-2 text-sm">
                  📖 {blog.read_time}
                </span>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Featured Image */}
      {imageUrl && (
        <section className="pb-12">
          <div className="container mx-auto px-4 lg:px-8">
            <div className="max-w-4xl mx-auto aspect-video overflow-hidden rounded-2xl">
              <img
                src={ `
http://127.0.0.1:8000/storage/`+imageUrl || event2.src}
                alt={blog.title}
                className="w-full h-full object-cover"
                onError={(e) => {
                  e.currentTarget.src = event2.src; // Fallback image
                }}
              />
            
            </div>
          </div>
        </section>
      )}

      {/* Blog Content */}
      <section className="pb-20">
        <div className="container mx-auto px-4 lg:px-8">
          <article className="max-w-4xl mx-auto">
            {/* Excerpt - show if available */}
            {blog.excerpt && (
              <div className="text-xl text-gray-600 italic border-l-4 border-primary pl-6 mb-8">
                {blog.excerpt}
              </div>
            )}
            
            {/* Main Content */}
            <div 
              className="prose prose-lg max-w-none"
              dangerouslySetInnerHTML={{ __html: blog.content || '' }}
            />
          </article>
        </div>
      </section>

      {/* Related Blogs */}
      {relatedBlogs.length > 0 && (
        <section className="py-16 bg-muted/30">
          <div className="container mx-auto px-4 lg:px-8">
            <h2 className="text-3xl font-bold text-center mb-8">
              Related Articles
            </h2>

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
              {relatedBlogs.map((item) => (
                <BlogCard key={item.id} blog={item} />
              ))}
            </div>
          </div>
        </section>
      )}

      <Footer />
    </div>
  );
}