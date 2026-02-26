
"use client";

import { useState, useEffect } from "react";
import BlogList from "@/components/blog/BlogList";
import Link from "next/link";
import { ArrowLeft } from "lucide-react";
import { Button } from "@/components/ui/button";
import apiClient from "@/lib/api"; // ✅ अपना apiClient import करें

const Blogs = () => {
  const [allBlogs, setAllBlogs] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchBlogs = async () => {
      try {
        // ✅ API से सभी blogs लाएँ
        const data = await apiClient.getBlogs();
        setAllBlogs(data);
      } catch (error) {
        console.error("Error fetching blogs:", error);
        setAllBlogs([]);
      } finally {
        setLoading(false);
      }
    };

    fetchBlogs();
  }, []);

  return (
    <div className="min-h-screen bg-background">

      {/* Hero Section */}
      <section className="pt-32 pb-16 bg-gradient-to-br from-purple-900/20 via-background to-background">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center space-y-4">

            <span className="text-primary font-medium text-sm uppercase tracking-wider">
              Our Blog
            </span>

            <h1 className="text-4xl md:text-5xl font-bold text-foreground">
              Insights & Articles
            </h1>

            <p className="text-muted-foreground max-w-2xl mx-auto">
              Explore our collection of articles covering the latest trends in technology,
              development best practices, and industry insights from the Shakuniya Solution team.
            </p>

            {/* Go Back to Home Blogs */}
            <div className="pt-6">
              <Link href="/#blogs" className="inline-block">
                <Button className="flex items-center gap-2">
                  <ArrowLeft size={16} />
                  Go to Home Blogs
                </Button>
              </Link>
            </div>

          </div>
        </div>
      </section>

      {/* Blog Listing */}
      <section className="py-16">
        <div className="container mx-auto px-4 lg:px-8">
          {loading ? (
            // Loading skeleton - grid में 8 cards
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {[...Array(8)].map((_, i) => (
                <div key={i} className="animate-pulse">
                  <div className="h-48 bg-gray-200 rounded-lg mb-4"></div>
                  <div className="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                  <div className="h-4 bg-gray-200 rounded w-1/2"></div>
                </div>
              ))}
            </div>
          ) : (
            <BlogList blogs={allBlogs} />
          )}
        </div>
      </section>

    </div>
  );
};

export default Blogs;