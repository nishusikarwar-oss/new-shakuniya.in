"use client";

import ServiceHero from "@/components/services/ServiceHero";
import ServiceFeatures from "@/components/services/ServiceFeatures";
import ServiceProcess from "@/components/services/ServiceProcess";
import ServiceStats from "@/components/services/ServiceStats";
import ServiceCommitment from "@/components/services/ServiceCommitment";
import ServiceCTA from "@/components/services/ServiceCTA";
import { Code, Blocks, Brain, Palette, Users, Server } from "lucide-react";
import Navbar from "@/components/Navbar";

const softwareFeatures = [
  {
    icon: Blocks,
    title: "Blockchain Development",
    description:
      "Build secure and transparent blockchain solutions for your business with smart contracts and decentralized applications.",
  },
  {
    icon: Server,
    title: "Web3 Solutions",
    description:
      "Transform your business with Web3 technologies including DeFi, NFTs, and decentralized platforms.",
  },
  {
    icon: Code,
    title: "Development of Personalized Software",
    description:
      "Custom software development tailored to your specific business requirements and workflows.",
  },
  {
    icon: Users,
    title: "Computer Consulting",
    description:
      "Expert consulting services to help you make informed technology decisions and optimize your software infrastructure.",
  },
  {
    icon: Brain,
    title: "Machine Intelligence",
    description:
      "Integrate AI and machine learning capabilities into your software for intelligent automation and insights.",
  },
  {
    icon: Palette,
    title: "Design Services",
    description:
      "UI/UX design services to create intuitive and visually appealing software interfaces that users love.",
  },
];

const SoftwareDevelopment = () => {
  return (
    <div className="min-h-screen mt-20 bg-[#05070a] text-white">
      <Navbar/>
      <ServiceHero
        highlightText="SOFTWARE"
        title="DEVELOPMENT COMPANY"
        description="As a software development company with 10 years of experience, we can create dependable, scalable, and secure software solutions for any operating system, browser, or device. We combine deep industry expertise with the most recent IT advancements to deliver custom solutions."
      />

      <section className="py-20 lg:py-28 relative bg-[#05070a]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <span className="text-sm text-purple-500 uppercase tracking-wider mb-4 block font-medium">
              Software Solutions
            </span>
            <h2 className="text-3xl md:text-4xl font-bold mb-6 text-white">
              Our Services For Developing{" "}
              <span className="text-purple-500">Software Applications</span>
            </h2>
            <p className="text-gray-400 max-w-3xl mx-auto">
              Our expertise in the design and development of web and mobile
              solutions has been established by more than 75 software products
              we have created in our studio for startups, midsize, and large
              enterprises.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {softwareFeatures.map((feature, index) => {
              const IconComponent = feature.icon;
              return (
                <div
                  key={index}
                  className="bg-[#11141d] border border-gray-800 rounded-xl p-6 hover:shadow-lg hover:border-purple-500/50 transition-all duration-300 group hover:-translate-y-2"
                >
                  <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500/20 to-blue-500/20 flex items-center justify-center mb-4 group-hover:from-purple-500/30 group-hover:to-blue-500/30 transition-all">
                    <IconComponent size={28} className="text-purple-400" />
                  </div>
                  <h4 className="font-bold text-lg mb-3 text-white">
                    {feature.title}
                  </h4>
                  <p className="text-gray-400 text-sm leading-relaxed">
                    {feature.description}
                  </p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      <ServiceStats />

      <ServiceProcess />

      <ServiceCommitment />

      <ServiceCTA />
    </div>
  );
};

export default SoftwareDevelopment;
