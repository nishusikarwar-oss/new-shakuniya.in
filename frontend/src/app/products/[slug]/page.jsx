import { notFound } from "next/navigation";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import {
  Zap,
  MessageSquare,
  Users,
  ShieldCheck,
  ArrowRight,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import Image from "next/image";

import biolinker from "@/images/biolinker2.png";
import ews from "@/images/ews.png";
import niyameet from "@/images/niyameet.png";
import vyaparbot from "@/images/vyaparbot.png";

const productsData = {
  "bio-linker": {
    title: "Bio Linker",
    image: biolinker.src,
    video: "/videos/biolinkervideo.mp4",
    price: "$4999.00 / ₹3,75,000 INR",
    description:
      "In a world of fragmented social media, Biolinkar acts as your digital headquarters. Build a centralized landing page that hosts your entire digital world.",
    features: [
      { icon: "MessageSquare", title: "Professional Bio-Link Architecture", description: "Build a centralized landing page that hosts your entire digital world." },
      { icon: "Zap", title: "Precision URL Management", description: "Turn long links into clean branded URLs." },
      { icon: "Users", title: "Smart Dynamic QR Integration", description: "Bridge offline and online marketing." },
    ],
  },

  ews: {
    title: "EWS",
    image: ews.src,
    video: "/videos/ewsvideo.mp4",
    price: "$4999.00 / ₹3,75,000 INR",
    description: "Unified messaging platform for Email, WhatsApp and SMS. Deliver critical OTPs, payment confirmations, and order milestones with sub-second latency.",
    features: [
      { icon: "MessageSquare", title: "Advanced Automation", description: "Deliver OTPs and notifications instantly." },
      { icon: "Zap", title: "Marketing Campaigns", description: "Execute omnichannel campaigns." },
      { icon: "Users", title: "Enterprise Integration", description: "Seamlessly connect with CRM and ERP." },
    ],
  },

  "niya-meet": {
    title: "Niya Meet",
    image: niyameet.src,
    video: "/videos/niyameetvideo.mp4",
    price: "$4999.00 / ₹3,75,000 INR",
    description: "Secure, unlimited, encrypted business video meetings. Crystal clear video & audio for professional collaboration without limits.",
    features: [
      { icon: "MessageSquare", title: "Unlimited Duration", description: "No 40-minute limits." },
      { icon: "Zap", title: "Ultra-HD Quality", description: "Crystal clear video & audio." },
      { icon: "ShieldCheck", title: "Encrypted Privacy", description: "End-to-end encryption." },
    ],
  },

  vyaparbot: {
    title: "Vyaparbot",
    image: vyaparbot.src,
    video: "/videos/vyaparbotvideo.mp4",
    price: "$4999.00 / ₹3,75,000 INR",
    description: "Vyaparbot is an intelligent AI-powered business automation bot that streamlines your operations. Handle customer queries, automate repetitive tasks, and provide 24/7 support with our cutting-edge chatbot solution designed for modern businesses.",
    features: [
      { 
        icon: "MessageSquare", 
        title: "Advanced Automation & Real-Time Notifications", 
        description: "Deliver critical OTPs, payment confirmations, and order milestones with sub-second latency across all primary channels." 
      },
      { 
        icon: "Zap", 
        title: "Strategic Marketing & Omnichannel Campaigns", 
        description: "Execute high-volume marketing campaigns via Email, WhatsApp, and SMS simultaneously to maximize brand visibility and impact." 
      },
      { 
        icon: "Users", 
        title: "Enterprise-Grade Integration & Scalability", 
        description: "Seamlessly bridge EWS with your existing tech stack (CRM, ERP, or e-commerce) via our high-performance, developer-friendly APIs." 
      },
    ],
  },
};

const IconMap = {
  Zap,
  MessageSquare,
  Users,
  ShieldCheck,
};

export function generateStaticParams() {
  return Object.keys(productsData).map((slug) => ({
    slug,
  }));
}

export default async function ProductPage({ params }) {
  const { slug } = await params;

  let product = null;

  try {
    
    const response = await fetch(`http://localhost:8000/api/products/${slug}`, {
      next: { revalidate: 60 },
    });
    
    if (response.ok) {
      product = await response.json();
    }
  } catch (error) {
    console.error("Failed to fetch product from API:", error);
  }


  if (!product) {
    product = productsData[slug];
  }

  if (!product) return notFound();

  return (
    <>
      <Navbar />

      <div className="min-h-screen bg-gradient-to-br from-[#0a0517] via-[#020617] to-[#0f172a] text-white selection:bg-purple-500/30">
      
        <section className="relative pt-24 pb-32">
          <div className="container mx-auto px-6 relative z-10">
            <div className="grid lg:grid-cols-2 gap-16 items-center">
              <div>
                <h1 className="text-7xl md:text-8xl font-black mb-6 tracking-tight leading-tight">
                  <span className="bg-gradient-to-r from-purple-500 via-pink-500 to-purple-400 bg-clip-text text-transparent">
                    {product.title}
                  </span>
                </h1>
                <p className="text-base md:text-lg text-gray-300 mb-10 leading-relaxed max-w-lg font-light">
                  {product.description}
                </p>

                <div className="mb-10">
                  <div className="bg-gradient-to-br from-purple-950/30 to-blue-950/20 backdrop-blur-md border border-purple-500/30 p-6 rounded-2xl inline-block min-w-[300px] shadow-2xl shadow-purple-900/20">
                    <p className="text-[11px] font-bold uppercase tracking-widest text-purple-300/70 mb-3">Starting From</p>
                    <p className="text-4xl md:text-5xl font-bold text-white tracking-tight bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent">
                      {product.price}
                    </p>
                  </div>
                </div>

                {/* <div>
                  <Button className="h-14 px-10 bg-[#9333ea] hover:bg-[#a855f7] text-white rounded-xl text-lg font-medium transition-all duration-300 shadow-xl shadow-purple-900/20 group">
                    Get Started
                    <ArrowRight className="ml-2 group-hover:translate-x-1 transition-transform" size={20} />
                  </Button>
                </div> */}
              </div>

              <div className="relative h-full flex items-center justify-center lg:justify-end">
                <div className="relative w-full h-96 bg-gradient-to-br from-purple-950/40 to-blue-950/40 border border-purple-500/20 rounded-3xl shadow-2xl overflow-hidden flex items-center justify-center group">
                  <div className="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(168,85,247,0.15),transparent)] transition-opacity duration-500 group-hover:opacity-150" />
                  <img
                    src={product.image}
                    alt={product.title}
                    className="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                  />
                </div>
              </div>
            </div>
          </div>
        </section>

    
        <section className="py-32 relative border-t border-purple-500/10 bg-gradient-to-b from-[#020617] to-[#0a0517]">
          <div className="container mx-auto px-6">
            <div className="text-center mb-16">
              <h2 className="text-5xl md:text-6xl font-bold mb-4">
                <span className="text-white">Core Features & </span>
                <span className="bg-gradient-to-r from-purple-500 to-pink-500 bg-clip-text text-transparent">Benefits</span>
              </h2>
            </div>
            
            <div className="grid lg:grid-cols-2 gap-16 items-center">
            
              <div className="relative group">
                <div className="absolute -inset-1.5 bg-gradient-to-r from-purple-600/30 to-blue-600/30 rounded-3xl blur-xl opacity-50 group-hover:opacity-70 transition duration-1000" />
                <div className="relative rounded-3xl overflow-hidden border border-purple-500/30 shadow-2xl shadow-purple-900/30 bg-black aspect-video">
                  <video
                    src={product.video}
                    autoPlay
                    loop
                    muted
                    playsInline
                    controls
                    className="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#0a0517]/40 via-transparent to-transparent" />
                </div>
              </div>

         
              <div className="space-y-5">
                {product.features.map((feature, index) => {
                  const Icon = typeof feature.icon === 'string' ? (IconMap[feature.icon] || MessageSquare) : feature.icon;
                  return (
                    <div
                      key={index}
                      className={`group relative p-6 md:p-8 rounded-2xl transition-all duration-500 cursor-default backdrop-blur-sm ${
                        index === 2 
                          ? "bg-gradient-to-br from-purple-950/60 to-blue-950/40 border border-purple-500/50 shadow-2xl shadow-purple-900/40" 
                          : "bg-purple-950/20 border border-purple-500/20 hover:bg-purple-950/40 hover:border-purple-500/40"
                      }`}
                    >
                      <div className="flex gap-5 items-start">
                        <div className={`p-3 rounded-xl shrink-0 ${
                          index === 2 ? "bg-gradient-to-br from-purple-600/40 to-pink-600/40 text-purple-300 shadow-lg shadow-purple-600/30" : "bg-purple-950/50 text-purple-300 group-hover:text-purple-200 transition-colors"
                        }`}>
                          <Icon size={32} strokeWidth={1.5} />
                        </div>
                        <div className="flex-1">
                          <h3 className={`text-lg md:text-xl font-bold mb-2 ${
                            index === 2 ? "text-white" : "text-gray-100 group-hover:text-white transition-colors"
                          }`}>
                            {feature.title}
                          </h3>
                          <p className={`leading-relaxed text-sm md:text-base ${
                            index === 2 ? "text-gray-300" : "text-gray-400 group-hover:text-gray-300 transition-colors"
                          }`}>
                            {feature.description}
                          </p>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>
        </section>
      </div>

      <Footer />
    </>
  );
}


