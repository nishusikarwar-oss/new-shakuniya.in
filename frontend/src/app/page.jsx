"use client";
// import { Button } from "@/components/ui/button";
import { Monitor, Smartphone, Code, Server, ShoppingCart } from "lucide-react";
// import Image from "next/image";

import { motion } from "framer-motion";
import heroBg from "../images/hero-bg.png";
// import ServicesSection from "@/components/ServicesSection";
import AdvantagesSection from "@/components/AdvantagesSection";
import StatsSection from "@/components/StatsSection";
import ContactSection from "@/components/ContactSection";
import BlogSection from "@/components/blog/BlogSection";
import TestimonialSection from "@/components/TestimonialSection";
import Footer from "@/components/Footer";
import Faq from "@/components/faqsection/Faq"
import Navbar from "@/components/Navbar";

const services = [
  {
    icon: Monitor,
    title: "WEB UI/UX Design",
    gradient: "from-purple-500 to-purple-600",
  },
  {
    icon: Smartphone,
    title: "MOBILE UI/UX Design",
    gradient: "from-blue-500 to-purple-500",
  },
  {
    icon: Code,
    title: "PSD to HTML5",
    gradient: "from-cyan-500 to-blue-500",
  },
  {
    icon: Server,
    title: "CMS Development",
    gradient: "from-purple-500 to-pink-500",
  },
  {
    icon: ShoppingCart,
    title: "eCommerce",
    gradient: "from-pink-500 to-purple-500",
  },
];

const HeroSection = () => {
  return (
    <>
    {/* <Navbar/> */}
    <Navbar/>

    <section
      id="home"
      className="relative min-h-screen flex items-center justify-center overflow-hidden pt-32 pb-12 md:pt-24"
    >
      {/* Background Image */}
      <div
        className="absolute inset-0 z-0"
        style={{
          backgroundImage: `url(${heroBg.src})`,
          backgroundSize: "cover",
          backgroundPosition: "center",
        }}
      >
        <div className="absolute inset-0 bg-[#0a0a16]/80 backdrop-blur-[2px]" />
      </div>

      {/* Content */}
      <div className="container mx-auto px-4 z-10 relative">
        <div className="max-w-5xl mx-auto text-center mb-16">
          <h1 className="text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-[1.2] md:leading-[1.1] tracking-tight uppercase">
            <span className="text-white">RESPONSIVE </span>
            <span className="text-transparent  animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">
              WEB DESIGNING
            </span>
            <br className="hidden sm:block" />
            <span className="text-white"> & DEVELOPMENT </span>
            <span className="text-transparent  animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">
              SOLUTIONS
            </span>
          </h1>

          <p className="text-base md:text-2xl text-gray-300 mb-10 font-medium px-4">
            We craft your business website as a powerful marketing tool
          </p>

          {/* <div className="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center items-center px-4">
            <motion.button
              initial={{ x: -200, opacity: 0 }} // lg slide right
              animate={{ x: 0, opacity: 1 }}
              transition={{
                duration: 2,
                delay: 0.5,
                type: "spring",
                bounce: 0.6,
              }}
              className="px-8 py-4  rounded-xl bg-gradient-to-r from-[#9333ea] to-[#a855f7] text-white font-semibold text-lg hover:shadow-[0_0_20px_rgba(147,51,234,0.5)] transition-all duration-300 w-full sm:w-auto"
            >
              Get Free Consultation
            </motion.button>
            <motion.button
              initial={{ x: 100, opacity: 0 }} // lg slide left
              animate={{ x: 0, opacity: 1 }}
              transition={{
                duration: 2,
                delay: 0.5,
                type: "spring",
                bounce: 0.6,
              }}
              className="px-8 py-4 rounded-xl border border-gray-600 bg-white/5 backdrop-blur-md text-white font-semibold text-lg hover:bg-white/10 transition-all duration-300 w-full sm:w-auto"
            >
              View Services
            </motion.button>
          </div> */}
        </div>

        {/* Service Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6 max-w-7xl mx-auto px-4">
          {services.map((service, index) => (
            <div
              key={index}
              className="bg-[#1a1a2e]/60 backdrop-blur-xl border border-white/10 p-6 sm:p-8 rounded-2xl sm:rounded-[2rem] text-center hover:bg-[#1a1a2e]/80 transition-all duration-300 hover:scale-105 hover:border-[#00d9ff]/50 hover:shadow-[0_0_30px_rgba(0,217,255,0.2)] flex flex-col items-center group cursor-pointer"
            >
              <div
                className={`w-16 h-14 mb-6 rounded-2xl bg-gradient-to-br ${service.gradient} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300`}
              >
                <service.icon className="w-8 h-8 text-white" />
              </div>
              <h3 className="text-sm md:text-base font-semibold text-white uppercase tracking-wide leading-tight group-hover:text-[#00d9ff] transition-colors duration-300">
                {service.title.split(" ").map((word, i) => (
                  <span key={i} className="block sm:inline pr-1">
                    {word}
                  </span>
                ))}
              </h3>
            </div>
          ))}
        </div>
      </div>
    </section>

      {/* <ServicesSection/> */}
      <AdvantagesSection/>
        <StatsSection/>
        <TestimonialSection/>
        <BlogSection/>
        <Faq/>
        <ContactSection/>
        <Footer/>
</>

  );
};

export default HeroSection;
