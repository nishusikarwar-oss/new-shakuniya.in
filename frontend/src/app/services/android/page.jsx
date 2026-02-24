"use client";
import ServiceHero from "@/components/services/ServiceHero";
import inspire from "@/images/inspire.png";
import ServiceFeatures from "@/components/services/ServiceFeatures";
import ServiceProcess from "@/components/services/ServiceProcess";
import ServiceStats from "@/components/services/ServiceStats";
import ServiceCommitment from "@/components/services/ServiceCommitment";
import ServiceCTA from "@/components/services/ServiceCTA";
import ServiceCharter from "@/components/services/ServiceCharter";
import WhyChooseUs from "@/components/services/WhyChooseUs";
import { Button } from "@/components/ui/button";
import { Smartphone, Layers, Zap, Shield, Settings, Palette, ArrowRight } from "lucide-react";
import Navbar from "@/components/Navbar";

const androidFeatures = [
  {
    icon: Smartphone,
    title: "HI-End Android App Developers",
    description: "Our expert Android developers create cutting-edge applications with the latest technologies and best practices for optimal performance."
  },
  {
    icon: Layers,
    title: "Create Innovative APIs for Mobile",
    description: "We develop robust and scalable APIs that power your mobile applications with seamless data synchronization and real-time updates."
  },
  {
    icon: Shield,
    title: "Mi-Smart Mobile Products (MVP)",
    description: "Build minimum viable products quickly to test your ideas in the market and gather valuable user feedback for iteration."
  },
  {
    icon: Settings,
    title: "Get Personalized Services with AI",
    description: "Integrate artificial intelligence and machine learning capabilities to deliver personalized user experiences in your Android apps."
  },
  {
    icon: Zap,
    title: "Quick Support & Maintenance",
    description: "We provide continuous support and maintenance services to ensure your Android application runs smoothly at all times."
  },
  {
    icon: Palette,
    title: "Advanced App Environment Experience",
    description: "Create immersive user experiences with advanced UI/UX design, animations, and intuitive navigation patterns."
  }
];

const AndroidDevelopment = () => {
  return (
    <div className="min-h-screen mt-20 bg-[#05070a] text-white">
      <Navbar/>
      
      <ServiceHero 
        highlightText="INSPIRE THE NEXT."
        title=""
        description="Enabling businesses to get competitive edge in the market by building scalable and extensible software and mobile applications."
        backgroundImage={inspire.src}
      />
      
      {/* About Section - Dark Background */}
      <section className="py-20 lg:py-28  relative bg-[#05070a]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-start">
            {/* Left Content */}
            <div>
              <span className="text-sm text-purple-500 uppercase tracking-wider mb-4 block font-medium">
                Top Notch Android App
              </span>
              <h2 className="text-3xl md:text-4xl font-bold mb-6 text-white">
                DEVELOPMENT COMPANY IN INDIA AND USA.
              </h2>
              <p className="text-gray-400 leading-relaxed mb-4">
                India has become one of the fastest growing mobile markets in the world, with over 95% annual growth rates. As India is a cost-effective location, people look for productive app solutions with 60% less costs.
              </p>
              <p className="text-gray-400 leading-relaxed mb-4">
                We are the top Android app developers with a full life cycle app development approach that provides solutions from concept and design to development, deployment, and maintenance.
              </p>
              <p className="text-gray-400 leading-relaxed mb-6">
                Our team of experienced developers specializes in creating high-performance, scalable Android applications that meet the unique needs of businesses across various industries. We leverage the latest technologies and frameworks to deliver solutions that drive growth and engagement.
              </p>
              <Button 
                className="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg px-6 py-3"
              >
                Request A Quote
                <ArrowRight className="ml-2" size={18} />
              </Button>
            </div>
            
            {/* Right Feature Grid */}
            <div className="grid sm:grid-cols-2 gap-4">
              {androidFeatures.map((feature, index) => {
                const IconComponent = feature.icon;
                return (
                  <div 
                    key={index} 
                    className="bg-[#11141d] border border-gray-800 rounded-xl p-5 hover:shadow-lg hover:border-purple-500/50 transition-all duration-300 group"
                  >
                    <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500/20 to-blue-500/20 flex items-center justify-center mb-4 group-hover:from-purple-500/30 group-hover:to-blue-500/30 transition-all">
                      <IconComponent size={24} className="text-purple-400" />
                    </div>
                    <h4 className="font-semibold text-sm mb-2 text-white">{feature.title}</h4>
                    <p className="text-gray-400 text-xs leading-relaxed">{feature.description.slice(0, 80)}...</p>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </section>
      
      <ServiceStats />
      
      <ServiceFeatures 
        title="Our Android App Development Services."
        subtitle="We deliver comprehensive Android development solutions tailored to your business needs."
        features={androidFeatures}
      />
      
      <ServiceProcess />
      
      <ServiceCharter 
        companyName="Shakuniya Solutions"
        tagline="Make It Think, Talk & Perform Like A Business."
        description="Being a formidable application development company, we have clearly cut our niche due to our ability to understand the client's needs before arriving at an optimum plan and develop a defined roadmap for implementation of matching app development standards. Hence we are the leading authority when it comes to anything and everything related to apps, design and development. Our expertise lies in building apps that are business ready, all client performance and technology standards."
      />
      
      <ServiceCommitment />
      
      <WhyChooseUs 
        title="Why Customers In Over 10+ Countries Choose Shakuniya Solutions?"
        points={[
          "Assured satisfaction throughout product lifecycle",
          "All stakeholders are on the priority and with 100 percent client satisfaction",
          "100+ combined years of experience with experts in the field of mobile development",
          "ISO certified and trusted by enterprises and startups for high performance",
          "Strictly about the product on client's servers for 100% data security",
          "100% Dedicated & Privacy secure: Guarantee"
        ]}
      />
      
      <ServiceCTA />
    </div>
  );
};

export default AndroidDevelopment;