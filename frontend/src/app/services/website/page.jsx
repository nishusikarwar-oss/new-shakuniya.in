"use client";
import ServiceHero from "@/components/services/ServiceHero";
import ServiceFeatures from "@/components/services/ServiceFeatures";
import ServiceProcess from "@/components/services/ServiceProcess";
import ServiceStats from "@/components/services/ServiceStats";
import ServiceCommitment from "@/components/services/ServiceCommitment";
import ServiceCTA from "@/components/services/ServiceCTA";
import {
  Globe,
  Layout,
  Code,
  ShoppingCart,
  Server,
  TestTube,
} from "lucide-react";
import Navbar from "@/components/Navbar";

const websiteFeatures = [
  {
    icon: Layout,
    title: "Web Design & Development",
    description:
      "Create stunning, responsive websites with modern design principles and cutting-edge development technologies.",
  },
  {
    icon: ShoppingCart,
    title: "E-commerce Solutions",
    description:
      "Build powerful e-commerce platforms with secure payment gateways, inventory management, and seamless user experience.",
  },
  {
    icon: Code,
    title: "PHP Development",
    description:
      "Expert PHP development services for building dynamic, scalable web applications with robust backend systems.",
  },
  {
    icon: Server,
    title: "ASP.NET Development",
    description:
      "Enterprise-grade ASP.NET development for building secure, high-performance web applications.",
  },
  {
    icon: Globe,
    title: "Open Source Solutions",
    description:
      "Leverage open source technologies like WordPress, Drupal, and Joomla for cost-effective web solutions.",
  },
  {
    icon: TestTube,
    title: "Website Testing",
    description:
      "Comprehensive testing services including functional testing, performance testing, and security audits.",
  },
];

const WebsiteDevelopment = () => {
  return (
    <div className="min-h-screen mt-20 bg-[#05070a] text-white">
      <Navbar/>
      <ServiceHero
        highlightText="WEBSITE"
        title="DEVELOPMENT"
        description="Shakuniya Solutions offers robust and scalable web application development services across various platforms and industry verticals. We provide comprehensive end-to-end website development services for mission-critical web applications commanding superior performance."
      />

      <section className="py-20 lg:py-28 relative bg-[#05070a]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-start">
            <div>
              <span className="text-sm text-purple-500 uppercase tracking-wider mb-4 block font-medium">
                Web Development Services
              </span>
              <h2 className="text-3xl md:text-4xl font-bold mb-6 text-white">
                TOP SOFTWARE DEVELOPMENT COMPANY WITH OVER 20 YEARS OF
                EXPERIENCE.
              </h2>
              <p className="text-gray-400 leading-relaxed mb-6">
                Shakuniya Solutions is a leading web development company in
                India and across the world with the expertise to deliver quality
                solutions via Shakuniya Solutions' offshore that helped
                digitally transform many businesses.
              </p>
              <p className="text-gray-400 leading-relaxed">
                We specialize in creating responsive, user-friendly websites
                that drive engagement and conversions for businesses of all
                sizes.
              </p>
            </div>

            <div className="grid sm:grid-cols-2 gap-4">
              {websiteFeatures.slice(0, 4).map((feature, index) => {
                const IconComponent = feature.icon;
                return (
                  <div
                    key={index}
                    className="bg-[#11141d] border border-gray-800 rounded-xl p-5 hover:shadow-lg hover:border-purple-500/50 transition-all duration-300 group"
                  >
                    <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500/20 to-blue-500/20 flex items-center justify-center mb-4 group-hover:from-purple-500/30 group-hover:to-blue-500/30 transition-all">
                      <IconComponent size={24} className="text-purple-400" />
                    </div>
                    <h4 className="font-semibold text-sm mb-2 text-white">
                      {feature.title}
                    </h4>
                    <p className="text-gray-400 text-xs leading-relaxed">
                      {feature.description.slice(0, 80)}...
                    </p>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </section>

      <ServiceStats />

      <ServiceFeatures
        title="Our Website Development Services."
        subtitle="We deliver comprehensive web development solutions tailored to your business needs."
        features={websiteFeatures}
      />

      <ServiceProcess />

      <ServiceCommitment />

      <ServiceCTA />
    </div>
  );
};

export default WebsiteDevelopment;
