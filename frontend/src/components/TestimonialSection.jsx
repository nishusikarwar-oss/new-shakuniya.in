"use client";

import { useState } from "react";
import Image from "next/image";
import { ChevronLeft, ChevronRight, Quote, Star } from "lucide-react";

const testimonials = [
  {
    id: 1,
    name: "Rajesh Kumar",
    role: "CEO, TechStart India",
    image:
      "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face",
    content:
      "Shakuniya Solutions transformed our digital presence completely. Their team delivered a stunning website that increased our conversions by 150%. Highly recommended!",
    rating: 5,
  },
  {
    id: 2,
    name: "Priya Sharma",
    role: "Founder, EcoGreen Solutions",
    image:
      "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&h=150&fit=crop&crop=face",
    content:
      "The mobile app they developed for us exceeded all expectations. Professional, responsive, and truly innovative. Our customers love the seamless experience.",
    rating: 5,
  },
  {
    id: 3,
    name: "Amit Patel",
    role: "Director, Global Exports Ltd",
    image:
      "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face",
    content:
      "Their SEO and digital marketing strategies helped us reach international markets. We saw a 200% increase in organic traffic within just 6 months.",
    rating: 5,
  },
  {
    id: 4,
    name: "Sneha Reddy",
    role: "Marketing Head, Retail Plus",
    image:
      "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face",
    content:
      "Working with Shakuniya was a game-changer for our e-commerce business. Their expertise in both design and development is unmatched in the industry.",
    rating: 5,
  },
  {
    id: 5,
    name: "Vikram Singh",
    role: "CTO, FinanceHub",
    image:
      "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face",
    content:
      "The custom software solution they built streamlined our operations and saved us countless hours. Their technical expertise and professionalism are top-notch.",
    rating: 5,
  },
];

export default function TestimonialSection() {
  const [currentIndex, setCurrentIndex] = useState(0);

  const nextTestimonial = () => {
    setCurrentIndex((prev) => (prev + 1) % testimonials.length);
  };

  const prevTestimonial = () => {
    setCurrentIndex(
      (prev) => (prev - 1 + testimonials.length) % testimonials.length
    );
  };

  const visibleTestimonials = () => {
    return Array.from({ length: 3 }, (_, i) => {
      const index = (currentIndex + i) % testimonials.length;
      return testimonials[index];
    });
  };

  return (
    <section className="py-24 bg-[#020617] text-white relative overflow-hidden">
      {/* Background Glow */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-gradient-to-b from-purple-500/5 to-transparent pointer-events-none" />

      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        {/* Header */}
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-6xl font-bold mb-6 flex flex-wrap justify-center gap-x-4">
            What Our
            <span className="bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text text-transparent">
              Clients
            </span>
            Say
          </h2>
          <p className="text-gray-400 text-lg max-w-2xl mx-auto leading-relaxed">
            Don't just take our word for it. Here's what our valued clients have
            to say about working with us.
          </p>
        </div>

        {/* Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
          {visibleTestimonials().map((testimonial) => (
            <div
              key={testimonial.id}
              className="bg-[#111827]/50 border border-gray-800/50 p-8 rounded-[2rem] relative group transition-all duration-500 hover:border-purple-500/30"
            >
              <Quote className="absolute top-8 right-8 w-10 h-10 text-purple-500/20 group-hover:text-purple-500/40 transition-colors" />

              {/* Stars */}
              <div className="flex gap-1 mb-6">
                {[...Array(5)].map((_, i) => (
                  <Star
                    key={i}
                    className="w-5 h-5 fill-yellow-400 text-yellow-400"
                  />
                ))}
              </div>

              <p className="text-gray-300 text-lg mb-8 leading-relaxed italic">
                "{testimonial.content}"
              </p>

              <div className="flex items-center gap-4 mt-auto">
                <div className="relative w-14 h-14">
                  <Image
                    src={testimonial.image}
                    alt={testimonial.name}
                    fill
                    className="rounded-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500"
                  />
                </div>
                <div>
                  <h4 className="font-bold text-white text-lg">
                    {testimonial.name}
                  </h4>
                  <p className="text-sm text-gray-500 font-medium">
                    {testimonial.role}
                  </p>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Controls */}
        <div className="flex items-center justify-center gap-8">
          <button
            onClick={prevTestimonial}
            className="w-12 h-12 rounded-full border border-gray-800 flex items-center justify-center hover:bg-gray-800 transition-colors group"
          >
            <ChevronLeft className="w-6 h-6 text-gray-400 group-hover:text-white" />
          </button>

          <div className="flex items-center gap-3">
            {testimonials.map((_, index) => (
              <button
                key={index}
                onClick={() => setCurrentIndex(index)}
                className={`transition-all duration-300 rounded-full ${
                  index === currentIndex
                    ? "bg-purple-500 w-8 h-2.5"
                    : "bg-gray-700 w-2.5 h-2.5 hover:bg-gray-600"
                }`}
              />
            ))}
          </div>

          <button
            onClick={nextTestimonial}
            className="w-12 h-12 rounded-full border border-gray-800 flex items-center justify-center hover:bg-gray-800 transition-colors group"
          >
            <ChevronRight className="w-6 h-6 text-gray-400 group-hover:text-white" />
          </button>
        </div>
      </div>
    </section>
  );
}
