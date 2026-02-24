"use client"

import { Button } from "@/components/ui/button";
import { ArrowRight } from "lucide-react";
import android from "../../images/android.png";

const ServiceCharter = ({
  companyName = "Shakuniya Solutions",
  tagline = "Make It Think, Talk & Perform Like A Business.",
  description = "Being a formidable application development company, we have clearly cut our niche due to our ability to understand the client's needs before arriving at an optimum plan and develop a defined roadmap for implementation of matching app development standards. Hence we are the leading authority when it comes to anything and everything related to apps."
}) => {
  return (
    <section className="py-16 lg:py-24 relative overflow-hidden">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="relative  rounded-3xl overflow-hidden">
          
          {/* Background decorations */}
          {/* <div className="absolute top-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl"  />
          <div className="absolute bottom-0 right-0 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl" /> */}
          <img src={android.src} className="absolute w-full h-full blur-xs object-cover" />

          <div className="relative z-10 grid lg:grid-cols-2 gap-8 items-center p-8 lg:p-12">
            
            {/* Left - Phone Mockup */}
            <div className="flex justify-center">
              <div className="relative">
                
                {/* Phone frame */}
                <div className="relative w-64 h-[500px] bg-gray-900 rounded-[3rem] p-3 shadow-2xl">
                  <div className="absolute top-6 left-1/2 -translate-x-1/2 w-20 h-6 bg-gray-800 rounded-full" />

                  <div className="w-full h-full bg-gradient-to-br from-cyan-400 via-purple-500 to-pink-500 rounded-[2.5rem] flex items-center justify-center overflow-hidden">
                    <div className="text-center p-4">
                      <div className="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl mx-auto mb-4 flex items-center justify-center">
                        <span className="text-2xl font-bold text-white">SS</span>
                      </div>
                      <p className="text-white/90 text-sm font-medium">
                        Mobile App
                      </p>
                      <p className="text-white/70 text-xs">
                        Development
                      </p>
                    </div>
                  </div>
                </div>

                {/* Floating elements */}
                <div className="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-2xl shadow-xl flex items-center justify-center animate-float">
                  <span className="text-white text-xs font-semibold text-center">
                    Quality<br />Assured
                  </span>
                </div>

                <div className="absolute -bottom-4 -left-4 w-20 h-20 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl shadow-xl flex items-center justify-center animate-float-delayed">
                  <span className="text-white text-xs font-semibold text-center">
                    24/7<br />Support
                  </span>
                </div>

              </div>
            </div>

            {/* Right - Content */}
            <div className="text-white">
              <h3 className="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 leading-tight">
                {companyName} Charter Is To{" "}
                <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">
                  {tagline.split(" ").slice(0, 3).join(" ")}
                </span>{" "}
                {tagline.split(" ").slice(3).join(" ")}
              </h3>

              <p className="text-white/80 leading-relaxed mb-6">
                {description}
              </p>

              <Button className="bg-white text-purple-900 hover:bg-gray-100 rounded-lg px-6 py-3 font-semibold">
                Know More
                <ArrowRight className="ml-2" size={18} />
              </Button>
            </div>

          </div>
        </div>
      </div>
    </section>
  );
};

export default ServiceCharter;
