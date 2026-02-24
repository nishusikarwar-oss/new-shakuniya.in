"use client"

import { Button } from "@/components/ui/button";
import { ArrowRight } from "lucide-react";

const ServiceAboutSection = ({ subtitle, title, paragraphs, features }) => {
  return (
    <section className="py-20 lg:py-28 relative bg-white">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="grid lg:grid-cols-2 gap-12 items-start">
          
          {/* Left Content */}
          <div>
            <span className="text-sm text-purple-600 uppercase tracking-wider mb-4 block font-medium">
              {subtitle}
            </span>

            <h2 className="text-3xl md:text-4xl font-bold mb-6 text-gray-900">
              {title}
            </h2>

            {paragraphs.map((paragraph, index) => (
              <p key={index} className="text-gray-600 leading-relaxed mb-4">
                {paragraph}
              </p>
            ))}

            <Button 
              className="mt-6 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg px-6 py-3"
            >
              Request A Quote
              <ArrowRight className="ml-2" size={18} />
            </Button>
          </div>
          
          {/* Right Feature Grid */}
          <div className="grid sm:grid-cols-2 gap-4">
            {features.slice(0, 6).map((feature, index) => {
              const IconComponent = feature.icon;
              return (
                <div 
                  key={index} 
                  className="bg-gray-50 border border-gray-100 rounded-xl p-5 hover:shadow-lg hover:border-purple-200 transition-all duration-300 group"
                >
                  <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-100 to-cyan-100 flex items-center justify-center mb-4 group-hover:from-purple-200 group-hover:to-cyan-200 transition-all">
                    <IconComponent size={24} className="text-purple-600" />
                  </div>

                  <h4 className="font-semibold text-sm mb-2 text-gray-900">
                    {feature.title}
                  </h4>

                  <p className="text-gray-500 text-xs leading-relaxed">
                    {feature.description.slice(0, 100)}...
                  </p>
                </div>
              );
            })}
          </div>

        </div>
      </div>
    </section>
  );
};

export default ServiceAboutSection;
