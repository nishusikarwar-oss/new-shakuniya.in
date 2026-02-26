"use client"
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Send, Mail, Phone, Search } from "lucide-react";
import worldMapBg from "@/images/world-map-bg.png";

const ContactSection = () => {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    mobile: "",
    company: "",
    service: "",
    message: "",
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log("Form submitted:", formData);
  };

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  return (
    <section id="contact" className="py-24 relative overflow-hidden bg-[#0a0a16]">
      {/* Background World Map */}
      <div
        className="absolute inset-0 z-0 opacity-20"
        style={{
          backgroundImage: `url(${worldMapBg.src})`,
          backgroundSize: "cover",
          backgroundPosition: "center",
        }}
      />

      <div className="container mx-auto px-4 lg:px-8 relative z-10 ">
        <div className="grid lg:grid-cols-2 gap-16 items-center">
          {/* Form Section */}
          <div className="bg-[#16162a]/60 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-10 md:p-14 hover:shadow-xl/30 shadow-purple-400/50">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-4">
              Let's talk.
            </h2>
            <p className="text-gray-400 mb-10 text-lg">
              We're here to help Enquiry Form
            </p>

            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid md:grid-cols-2 gap-6">
                <input
                  name="name"
                  placeholder="Name *"
                  value={formData.name}
                  onChange={handleChange}
                  className="w-full bg-[#1c1c36]/50 border border-white/5 focus:border-[#b366ff] h-14 rounded-2xl px-6 text-white outline-none transition-all"
                  required
                />
                <input
                  name="email"
                  type="email"
                  placeholder="Email *"
                  value={formData.email}
                  onChange={handleChange}
                  className="w-full bg-[#1c1c36]/50 border border-white/5 focus:border-[#b366ff] h-14 rounded-2xl px-6 text-white outline-none transition-all"
                  required
                />
              </div>

              <div className="grid md:grid-cols-2 gap-6">
                <input
                  name="mobile"
                  placeholder="Mobile *"
                  value={formData.mobile}
                  onChange={handleChange}
                  className="w-full bg-[#1c1c36]/50 border border-white/5 focus:border-[#b366ff] h-14 rounded-2xl px-6 text-white outline-none transition-all"
                  required
                />
                <input
                  name="company"
                  placeholder="Company *"
                  value={formData.company}
                  onChange={handleChange}
                  className="w-full bg-[#1c1c36]/50 border border-white/5 focus:border-[#b366ff] h-14 rounded-2xl px-6 text-white outline-none transition-all"
                  required
                />
              </div>

              <div className="relative">
                <select
                  name="service"
                  value={formData.service}
                  onChange={handleChange}
                  className="w-full bg-[#1c1c36]/50 border border-white/5 focus:border-[#b366ff] h-14 rounded-2xl px-6 text-white outline-none transition-all appearance-none cursor-pointer"
                  required
                >
                  <option value="" className="bg-[#1c1c36]">Service Requirement *</option>
                  <option value="seo" className="bg-[#1c1c36]">Search Engine Optimization</option>
                  <option value="smm" className="bg-[#1c1c36]">Social Media Marketing</option>
                  <option value="ppc" className="bg-[#1c1c36]">Pay Per Click</option>
                  <option value="web" className="bg-[#1c1c36]">Website Development</option>
                  <option value="ecom" className="bg-[#1c1c36]">E-Commerce Solutions</option>
                  <option value="video" className="bg-[#1c1c36]">Video Marketing</option>
                </select>
                <div className="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                  <svg className="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                </div>
              </div>

              <textarea
                name="message"
                placeholder="Message"
                value={formData.message}
                onChange={handleChange}
                className="w-full bg-[#1c1c36]/50 border border-white/5 focus:border-[#b366ff] min-h-[140px] rounded-2xl px-6 py-4 text-white outline-none transition-all resize-none"
              />

              <div className="flex flex-col sm:flex-row gap-8 items-center pt-4">
                <div className="flex items-center gap-4 text-gray-400 text-base">
                  <span>Verify the numbers * 8 + 1 = </span>
                  <input
                    type="text"
                    className="w-16 bg-[#1c1c36]/50 border border-white/5 h-12 rounded-xl text-center text-white outline-none focus:border-[#b366ff]"
                  />
                </div>

                <button
                  type="submit"
                  className="flex-1 sm:flex-none px-10 py-4 rounded-2xl bg-gradient-to-r from-[#9333ea] to-[#a855f7] text-white font-bold text-lg hover:shadow-[0_0_30px_rgba(147,51,234,0.4)] transition-all duration-300 flex items-center justify-center gap-3 uppercase tracking-wide"
                >
                  Request for a Quote
                </button>
              </div>
            </form>

            <p className="text-center text-gray-500 mt-10 font-medium">
              Why wait? Get your free quote today!
            </p>
          </div>

          {/* Right Side Icons */}
          <div className="hidden lg:flex flex-col items-center justify-center relative h-full">
            <div className="relative w-full aspect-square max-w-[500px]">
              <div className="absolute hover:animate-bounce  top-[15%] right-[10%] w-20 h-20 bg-[#16162a]/60 backdrop-blur-md border border-white/10 rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(34,211,238,0.2)] animate-float">
                <Phone className="w-9 h-9 text-[#22d3ee]" />
              </div>
              <div className="absolute hover:animate-bounce  top-[45%] right-[30%] w-24 h-24 bg-[#16162a]/60 backdrop-blur-md border border-white/10 rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(168,85,247,0.2)] animate-float-delayed">
                <Mail className="w-10 h-10 text-[#a855f7]" />
              </div>
              <div className="absolute hover:animate-bounce  bottom-[15%] right-[15%] w-16 h-16 bg-[#16162a]/60 backdrop-blur-md border border-white/10 rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(236,72,153,0.2)] animate-float-slow">
                <Search className="w-7 h-7 text-[#ec4899]" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ContactSection;
