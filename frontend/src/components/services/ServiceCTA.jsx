"use client";
import { Button } from "@/components/ui/button";
import { ArrowRight } from "lucide-react";

const ServiceCTA = () => {
  return (
    <section className="py-16 lg:py-20 relative overflow-hidden">
      <div 
        className="absolute inset-0"
        style={{
          background: 'linear-gradient(135deg, hsl(263 70% 20% / 0.6) 0%, hsl(222 47% 10%) 50%, hsl(200 100% 20% / 0.4) 100%)'
        }}
      />
      
      {/* Decorative elements */}
      <div className="absolute top-1/2 left-10 w-32 h-32 bg-purple-500/30 rounded-full blur-3xl" />
      <div className="absolute top-1/2 right-10 w-32 h-32 bg-cyan-500/30 rounded-full blur-3xl" />

      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        <div className="text-center max-w-3xl mx-auto">
          <h2 className="text-2xl md:text-3xl lg:text-4xl font-bold mb-4">
            When <span className="gradient-text">Success</span> Matters
          </h2>
          <p className="text-muted-foreground text-lg mb-8">
            We are committed to building robust and scalable applications that creates efficient business processes and adds value to our customers' businesses.
          </p>
          <Button variant="hero" size="lg" className="group">
            Get Quote Now
            <ArrowRight className="ml-2 group-hover:translate-x-1 transition-transform" size={18} />
          </Button>
        </div>
      </div>
    </section>
  );
};

export default ServiceCTA;