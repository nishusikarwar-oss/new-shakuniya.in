"use client"
import { Button } from "@/components/ui/button";
import {
  Search,
  Share2,
  MessageCircle,
  MousePointerClick,
  Globe,
  ShoppingBag,
  Video,
} from "lucide-react";

const services = [
  {
    icon: Search,
    title: "Search Engine Optimisation",
    description:
      "Get to the top of search results by using our reliable SEO services and expertise.",
    cta: "To the Top of Search Results",
    gradient: "from-orange-500 to-red-500",
  },
  {
    icon: Share2,
    title: "Social Media Optimisation",
    description:
      "Get better traffic from social networks other than big search engines.",
    cta: "Boost up Social Traffic",
    gradient: "from-pink-500 to-purple-500",
  },
  {
    icon: MessageCircle,
    title: "Social Media Marketing",
    description:
      "Take your brand message across multiple social media networks for great results.",
    cta: "Effective Social Reach",
    gradient: "from-blue-500 to-cyan-500",
  },
  {
    icon: MousePointerClick,
    title: "Pay Per Click",
    description:
      "Use the PPC advertising model to generate leads and drive up sales online.",
    cta: "Increase Leads / Drive Sales",
    gradient: "from-green-500 to-teal-500",
  },
  {
    icon: Globe,
    title: "Website Development & Design",
    description:
      "Design your website to offer a great digital user experience for visitors and customers.",
    cta: "Upgrade your Website",
    gradient: "from-purple-500 to-indigo-500",
  },
  {
    icon: ShoppingBag,
    title: "E-Commerce Solutions",
    description:
      "Combining designing, hosting and digital marketing expertise for your online business.",
    cta: "Build your Business Online without Borders",
    gradient: "from-cyan-500 to-blue-500",
  },
  {
    icon: Video,
    title: "Video Marketing & YouTube SEO",
    description:
      "Content is getting more visual than ever. Use our video promotion service to increase viewership.",
    cta: "Multiply Online Viewership",
    gradient: "from-red-500 to-pink-500",
  },
];

const ServicesSection = () => {
  return (
    <section id="services" className="py-24 relative overflow-hidden bg-[#0a0a16]">
      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        {/* Section Header */}
        <div className="text-center mb-16">
          <h2 className="text-4xl  md:text-5xl lg:text-6xl font-bold mb-6">
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">
              Result-Driven
            </span>{" "}
            <span className="text-white">
              Digital Marketing Company
            </span>
          </h2>
          <p className=" text-lg md:text-xl text-gray-400 max-w-3xl mx-auto">
            We offer end to end digital solutions for Your Business to Grow Online
          </p>
        </div>

        {/* Services Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
          {services.slice(0, 6).map((service, index) => (
            <div
              key={index}
              className="bg-[#16162a]/60 hover:scale-105 hover:shadow-xl/30 shadow-[#00d9ff]/20 backdrop-blur-xl border border-white/5 p-10 rounded-[2.5rem] group hover:bg-[#1c1c36]/80 hover:border-[#00d9ff]/30 transition-all duration-300"
            >
              <div
                className={`w-14 h-14 mb-8 rounded-xl bg-gradient-to-br ${service.gradient} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300`}
              >
                <service.icon className="w-7 h-7 text-white" />
              </div>
              <h3 className="text-xl font-bold text-[#b366ff] mb-4">
                {service.title}
              </h3>
              <p className="text-gray-400 mb-8 leading-relaxed text-base">
                {service.description}
              </p>
              <button className="px-6 py-2.5 rounded-xl bg-[#6366f1] text-white font-semibold text-sm hover:bg-[#4f46e5] transition-all duration-300">
                {service.cta}
              </button>
            </div>
          ))}
        </div>

        {/* Featured Service (Single Card Layout) */}
        <div className="flex justify-center mb-16">
          <div className="bg-[#16162a]/60 backdrop-blur-xl border border-white/5 p-10 rounded-[2.5rem] group hover:bg-[#1c1c36]/80 hover:border-[#00d9ff]/30 hover:shadow-[0_0_30px_rgba(0,217,255,0.1)] transition-all duration-300 max-w-xl w-full text-center flex flex-col items-center">
            <div
              className={`w-14 h-14 mb-8 rounded-xl bg-gradient-to-br ${services[6].gradient} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300`}
            >
              <Video className="w-7 h-7 text-white" />
            </div>
            <h3 className="text-xl font-bold text-[#b366ff] mb-4">
              {services[6].title}
            </h3>
            <p className="text-gray-400 mb-8 leading-relaxed text-base">
              {services[6].description}
            </p>
            <button className="px-6 py-2.5 rounded-xl bg-[#6366f1] text-white font-semibold text-sm hover:bg-[#4f46e5] transition-all duration-300">
              {services[6].cta}
            </button>
          </div>
        </div>

        {/* Contact CTA */}
        <div className="flex justify-center mt-12">
          <button className="px-10 py-4 rounded-2xl bg-gradient-to-r from-[#9333ea] to-[#a855f7] text-white font-bold text-lg hover:shadow-[0_0_30px_rgba(147,51,234,0.4)] transition-all duration-300 uppercase tracking-wide">
            To Give Your Business Strong Online Presence - Contact Us
          </button>
        </div>
      </div>
    </section>
  );
};

export default ServicesSection;
