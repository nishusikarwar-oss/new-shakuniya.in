"use client"

import React from "react";
import ServiceHero from "@/components/services/ServiceHero";
import ServiceProcess from "@/components/services/ServiceProcess";
import ServiceStats from "@/components/services/ServiceStats";
import ServiceCommitment from "@/components/services/ServiceCommitment";
import ServiceCTA from "@/components/services/ServiceCTA";
import { Lightbulb, Cloud, Users, Shield, BarChart3, Cpu } from "lucide-react";
import Navbar from "@/components/Navbar";

const consultingServices = [
  {
    icon: Lightbulb,
    title: "Product Engineering Solution",
    description:
      "Our product engineering teams are truly aligned with a vision of delivering future ready software products that are fully compatible with industry standards.",
  },
  {
    icon: Cloud,
    title: "Cloud Solution",
    description:
      "Cloud Services reach from planning to implementation and we deliver cloud solutions that help drive business transformation and improve productivity.",
  },
  {
    icon: Users,
    title: "Managed IT Solution",
    description:
      "Our IT strategy consulting will assist you choose the proper technologies for your business and industry, map the implementation strategy, also assist you implement them.",
  },
  {
    icon: Shield,
    title: "Engagement Models",
    description:
      "We manage entire solutions from our offices, integrate onsite with your teams, or allow you to leverage our highly skilled individual consultant resources.",
  },
  {
    icon: BarChart3,
    title: "Top Rated Team",
    description:
      "Our 30+ highly trained and experienced IT professionals are the highest drawer and renowned for his or her expertise in delivering exceptional solutions.",
  },
  {
    icon: Cpu,
    title: "Adaptability & Scalability",
    description:
      "Vision to our success is our understanding of the fact that every business is unique and has its own appetite for growth and transformation.",
  },
];

const additionalServices = [
  {
    title: "Key to Expertise",
    description:
      "Over the last 15 years, Shakuniya has acquired cutting edge expertise in all the major web and software development technologies.",
  },
  {
    title: "Cost Benefits",
    description:
      "The geographic location of our organization allows us to supply highly cost-effective IT outsourcing services to offshore clients.",
  },
  {
    title: "Enhanced Communication",
    description:
      "The combination of networks, devices, content and technology with hard work leads in enhancing the performances of the company.",
  },
  {
    title: "Intellectual Property Protection",
    description:
      "At Shakuniya, nothing is left to chance when it comes to protecting the clients' intellectual property rights with the best standards.",
  },
];

const ITConsultant = () => {
  return (
    <div className="min-h-screen mt-20 bg-[#05070a] text-white">
      <Navbar/>
      <ServiceHero
        highlightText="IT CONSULTING"
        title="Services For Developing Innovative Software & Mobile Applications."
        description="Shakuniya Solutions is form an experienced IT strategy for a digital and technological transformation of your organization that goes in line with your business goals. Our strategic IT consulting company will assist you automate and digitalize operations."
      />

      <section className="py-20 lg:py-28 relative bg-[#05070a]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-start mb-16">
            <div>
              <span className="text-sm text-purple-500 uppercase tracking-wider mb-4 block font-medium">
                IT Strategy & Consulting
              </span>
              <h2 className="text-3xl md:text-4xl font-bold mb-6 text-white">
                Shakuniya Solutions Offers Bouquet of{" "}
                <span className="text-purple-500">IT CONSULTING</span> Services
                For Developing Innovative Softwares & Mobile Applications.
              </h2>
              <p className="text-gray-400 leading-relaxed mb-6">
                The advantages that accrue from IT sources are well understood
                and appreciated by businesses and organizations across the
                globe. IT Consulting services have emerged as one of the most
                effective and convenient method of reducing overall operational
                cost.
              </p>
              <p className="text-gray-400 leading-relaxed">
                Over the last 5+ years, Shakuniya Solutions has earned its spurs
                deploying the whole gamut of IT outsourcing services both to
                offshore and onshore businesses.
              </p>
            </div>

            <div className="grid sm:grid-cols-2 gap-4">
              {consultingServices.slice(0, 4).map((service, index) => {
                const IconComponent = service.icon;
                return (
                  <div
                    key={index}
                    className="bg-[#11141d] border border-gray-800 rounded-xl p-5 hover:shadow-lg hover:border-purple-500/50 transition-all duration-300 group"
                  >
                    <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500/20 to-blue-500/20 flex items-center justify-center mb-4 group-hover:from-purple-500/30 group-hover:to-blue-500/30 transition-all">
                      <IconComponent size={24} className="text-purple-400" />
                    </div>
                    <h4 className="font-semibold text-sm mb-2 text-white">
                      {service.title}
                    </h4>
                    <p className="text-gray-400 text-xs leading-relaxed">
                      {service.description.slice(0, 80)}...
                    </p>
                  </div>
                );
              })}
            </div>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {additionalServices.map((service, index) => (
              <div
                key={index}
                className="bg-[#11141d] border border-gray-800 rounded-xl p-6 hover:shadow-lg hover:border-purple-500/50 transition-all duration-300"
              >
                <h4 className="font-bold text-lg mb-3 text-white">
                  {service.title}
                </h4>
                <p className="text-gray-400 text-sm leading-relaxed">
                  {service.description}
                </p>
              </div>
            ))}
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

export default ITConsultant;
