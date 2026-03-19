// components/blog/BlogSection.jsx
"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import BlogCard from "./BlogCard";
import apiClient from "@/lib/api"; // ✅ अपने apiClient को import करें

const BlogSection = () => {
  const [blogs, setBlogs] = useState({ data: [] });
  const [loading, setLoading] = useState(true);
console.log(blogs)
  useEffect(() => {
    const fetchBlogs = async () => {
      try {
        // ✅ API से 4 latest blogs लाएँ
        const data = await apiClient.getBlogs({ per_page: 4 });
        setBlogs(data);
      } catch (error) {
        console.error("Error fetching blogs:", error);
        setBlogs({ data: [] });
      } finally {
        setLoading(false);
      }
    };

    fetchBlogs();
  }, []);

  return (
    <section id="blogs" className="py-20 bg-background">
      <div className="container mx-auto px-4 lg:px-8">

        {/* Section Header */}
        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
          <div className="space-y-3">
            <span className="text-primary font-medium text-sm uppercase tracking-wider">
              Our Blog
            </span>

            <h2 className="text-3xl md:text-4xl font-bold text-foreground">
              Latest Insights & News
            </h2>

            <p className="text-muted-foreground max-w-xl">
              Stay updated with the latest trends in technology, development insights,
              and industry news from our expert team.
            </p>
          </div>

          {/* View All Button */}
          <Link href="/blogs" className="inline-block w-fit group">
            <Button className="flex items-center gap-2">
              View All Blogs
              <ArrowRight
                size={16}
                className="transition-transform duration-300 group-hover:translate-x-1"
              />
            </Button>
          </Link>
        </div>

        {/* Blog Cards Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {loading ? (
            // Loading skeleton - 4 cards
            [...Array(4)].map((_, i) => (
              <div key={i} className="animate-pulse">
                <div className="h-48 bg-gray-200 rounded-lg mb-4"></div>
                <div className="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div className="h-4 bg-gray-200 rounded w-1/2"></div>
              </div>
            ))
          ) : blogs.data.length > 0 ? (
            blogs.data.map((blog) => (
              <BlogCard key={blog.id} blog={blog} />
            ))
          ) : (
            <p className="col-span-4 text-center text-muted-foreground">
              No blogs found
            </p>
          )}
        </div>

      </div>
    </section>
  );
};

export default BlogSection;