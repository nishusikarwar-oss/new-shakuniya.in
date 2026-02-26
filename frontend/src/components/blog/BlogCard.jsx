"use client";

import Link from "next/link";
import { Calendar, ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import event2 from "@/images/event2.png"

const BlogCard = ({ blog }) => {
  const formattedDate = new Date(blog.date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  }) 
  console.log("BLOG CARD 👉", blog);

  ;

  return (
    <Card className="group overflow-hidden bg-[#0A0C10] border-[#1F2937] hover:border-[#7C3AED]/50 transition-all duration-300 hover:shadow-2xl hover:shadow-[#7C3AED]/10 rounded-2xl">
      
      {/* Image */}
      <Link href={`/blogs/${blog.slug}`} className="block">
        <div className="relative overflow-hidden aspect-[16/10]">
          <img
            src={event2.src}
            alt={blog.title}
            className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
          />

          {/* Category */}
          <div className="absolute top-4 left-4">
            <span className="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-[#7C3AED] text-white rounded-full">
              {blog.category}
            </span>
          </div>
        </div>
      </Link>

      <CardContent className="p-6 space-y-4">

        {/* Date */}
        <div className="flex items-center gap-2 text-[13px] text-gray-400">
          <Calendar size={14} className="text-gray-400" />
          <span>{formattedDate}</span>
        </div>

        {/* Title */}
        <Link href={`/blogs/${blog.slug}`}>
          <h3 className="text-xl font-bold text-white line-clamp-2 group-hover:text-[#7C3AED] transition-colors duration-300 leading-tight">
            {blog.title}
          </h3>
        </Link>

        {/* Excerpt */}
        <p className="text-[14px] text-gray-400 line-clamp-2 leading-relaxed">
          {blog.excerpt}
        </p>

        {/* CTA */}
        <div className="pt-2">
          <Link 
            href={`/blogs/${blog.slug}`} 
            className="inline-flex items-center gap-2 text-[#7C3AED] font-semibold text-sm hover:gap-3 transition-all duration-300"
          >
            Get Started
            <ArrowRight size={18} />
          </Link>
        </div>

      </CardContent>
    </Card>
  );
};

export default BlogCard;
