"use client";
import React from "react";
import { MapPin, Phone, Mail } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { useState } from "react";
import Footer from "@/components/Footer";
import Navbar from "@/components/Navbar";

const Contact = () => {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    message: "",
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log("Form submitted:", formData);
  };

  return (
    <div className="min-h-screen bg-[#05070a]">
      {/* Hero Section */}

<Navbar/>

      <section className="relative h-[500px] pt-32 pb-16 overflow-hidden">
        <video
          src="/videos/contact.mp4"
          autoPlay
          loop
          muted
          playsInline
          className="absolute inset-0 w-full h-full object-cover opacity-20"
        />

        <div className="absolute inset-0 bg-gradient-to-br from-[#05070a] via-transparent to-[#05070a]/80" />
        <div className="absolute top-20 right-10 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl animate-float" />
        <div className="absolute bottom-10 left-10 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl animate-float-delayed" />

        <div className="container mx-auto px-4 lg:px-8 relative z-10">
          <div className="text-center mb-12">
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-white">
              <span className="gradient-text">Contact</span> Us
            </h1>
            <p className="text-[#b5b0b0] text-lg max-w-2xl mx-auto">
              Get in touch with us for any inquiries or project discussions
            </p>
          </div>
        </div>
      </section>

      {/* Contact Section */}
      <section className="py-16 lg:py-24 relative">
        <div className="max-w-[1200px] mx-auto px-4 lg:px-8">
          <div className="glass-card bg-[#0f1217]/50 border border-white/10 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-xl hover:shadow-purple-500/10 transition-shadow duration-300 hover:scale-105">
            <div className="grid lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-white/10">
              {/* Left Side */}
              <div className="p-8 lg:p-12">
                <div className="space-y-10">
                  {/* Address */}
                  <div className="text-center sm:text-left">
                    <div className="w-14 h-14  mx-auto sm:mx-0 mb-4 rounded-full bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                      <MapPin className="w-7 h-7 text-purple-400" />
                    </div>
                    <h3 className="text-xl font-bold text-white mb-3">
                      Address
                    </h3>
                    <p className="text-[#b5b0b0] leading-relaxed">
                      E 308 Vijay Raja Ideal Homes,
                      <br />
                      Gudapakkam, Thiruvallur,
                      <br />
                      Chennai - 600124, TamilNadu,
                      <br />
                      India
                    </p>
                    <p className="text-[#b5b0b0] leading-relaxed mt-3">
                      Jagan Towers 864, 4, 2nd Floor,
                      <br />
                      P.H. Road, Kilpauk, Chennai-
                      <br />
                      600010, India
                    </p>
                  </div>

                  <div className="grid grid-cols-1 lg:grid-cols-2">
                    {/* Phone */}
                    <div className="text-center sm:text-left">
                      <div className="w-14 h-14  mx-auto sm:mx-0 mb-4 rounded-full bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                        <Phone className="w-7 h-7 text-purple-400 " />
                      </div>
                      <h3 className="text-xl font-bold text-white mb-3">
                        Phone
                      </h3>
                      <p className="text-[#b5b0b0]">+91 81908 38230</p>
                    </div>

                    {/* Email */}
                    <div className="text-center sm:text-left">
                      <div className="w-14 h-14  mx-auto sm:mx-0 mb-4 rounded-full bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                        <Mail className="w-7 h-7 text-purple-400" />
                      </div>
                      <h3 className="text-xl font-bold text-white mb-3">
                        Email
                      </h3>
                      <a
                        href="mailto:hr@shakuniya.in"
                        className="text-purple-400 hover:text-purple-300 transition-colors"
                      >
                        hr@shakuniya.in
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              {/* Right Side - Form */}
              <div className="p-8 lg:p-12">
                <h2 className="text-2xl font-bold text-white mb-2">
                  Send us a message
                </h2>
                <p className="text-[#b5b0b0] mb-8">
                  If you have any work from me or any types of queries related
                  to my tutorial, you can send me message from here.
                </p>

                <form onSubmit={handleSubmit} className="space-y-5">
                  <Input
                    type="text"
                    placeholder="Enter your name"
                    value={formData.name}
                    onChange={(e) =>
                      setFormData({ ...formData, name: e.target.value })
                    }
                    className="bg-white/5 border-white/10 h-12 text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                  />

                  <Input
                    type="email"
                    placeholder="Enter your email"
                    value={formData.email}
                    onChange={(e) =>
                      setFormData({ ...formData, email: e.target.value })
                    }
                    className="bg-white/5 border-white/10 h-12 text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                  />

                  <Textarea
                    placeholder="Enter your message"
                    value={formData.message}
                    onChange={(e) =>
                      setFormData({ ...formData, message: e.target.value })
                    }
                    className="bg-white/5 border-white/10 min-h-[120px] resize-none text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                  />

                  <Button
                    type="submit"
                    className="w-full h-12 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg shadow-purple-500/20 transition-all hover:scale-[1.01]"
                  >
                    Send Now
                  </Button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
      <Footer />
    </div>
  );
};

export default Contact;
