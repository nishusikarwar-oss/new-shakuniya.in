"use client";
import React from "react";
import ServiceHero from "@/components/services/ServiceHero";
import ServiceFeatures from "@/components/services/ServiceFeatures";
import ServiceProcess from "@/components/services/ServiceProcess";
import ServiceStats from "@/components/services/ServiceStats";
import ServiceCommitment from "@/components/services/ServiceCommitment";
import ServiceCTA from "@/components/services/ServiceCTA";
import {
  Apple,
  Layers,
  Gamepad2,
  TestTube,
  Building,
  Headphones,
} from "lucide-react";
import post from "@/images/post.png";
import details from "@/images/details.png";
import terms from "@/images/terms.png";
import online from "@/images/online.png";
import Navbar from "@/components/Navbar";

const iosFeatures = [
  {
    icon: Apple,
    title: "iOS App Design",
    description:
      "Want us to style the UI/UX of your IOS app? Hire our IOS app designers now and obtain your app designed from us. We assure you to style any sort of iOS app that's interactive and easy-to-access for your app users.",
  },
  {
    icon: Layers,
    title: "iOS App Development",
    description:
      "We provide customized app development for iOS to satisfy our client’s specifications and business requirements. By understanding the interface functionalities we customize your app with top features.",
  },
  {
    icon: TestTube,
    title: "iOS App Testing",
    description:
      "A team of quality testers and analysts test the app against various quality parameters and using various methods. This is when we deliver the first demo of the app to the clients.",
  },
  {
    icon: Headphones,
    title: "IT Consulting Service",
    description:
      "As an IOS application development company, we offer the proper consulting approach through the planning , development, and QA, distribution, and maintenance phases.",
  },
  {
    icon: Building,
    title: "Enterprise iOS Apps",
    description:
      "The focus of Enterprise app development should be on the factors most important to the client; factors such as usability, visual design features, programming, security, and quality assurance.",
  },
  {
    icon: Gamepad2,
    title: "iOS Game Development",
    description:
      "Shakuniya Solutions is one of the top Ios mobile app development companies in India that has a team of professional game developers for high performance Ios gaming services.",
  },
];

const developmentFeatures = [
  {
    image: post.src,
    title: "Post Your Project Requirements",
    description:
      "Our analysts will thoroughly review your project requirements, Project requirements are conditions or tasks that must be completed to ensure the success of the project.",
  },
  {
    image: details.src,
    title: "Discuss Project Details With Our Analysts",
    description:
      "Our experts will contact you within no time to discuss your project related queries and to offer the best solution for your project development",
  },
  {
    image: terms.src,
    title: "Choose Engagement Terms & Timelines",
    description:
      "Progress and the deadlines observance are not neglected for a long-standing relationship and long-term projects",
  },
  {
    image: online.src,
    title: "Securely Pay Online And Get Started!",
    description:
      "Make payment through our 100% secure online system and our team shall immediately start on your project within 24 hours.",
  },
];

const IosDevelopment = () => {
  return (
    <div className="min-h-screen mt-20 bg-[#05070a] text-white">
      <Navbar/>
      <ServiceHero
        highlightText="iOS APP"
        title="DEVELOPMENT"
        description="Shakuniya Solutions is a top iOS app development company in India that provides offshore iOS application development services for growing businesses with the wherewithal to obtain maximum productivity."
      />

      <section className="py-20 lg:py-28 relative bg-[#05070a]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-start">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-6 text-white">
                <span className="gradient-text">
                  SHAKUNIYA SOLUTIONS IS A TOP IOS APP{" "}
                </span>{" "}
                DEVELOPMENT COMPANY WITH OVER{" "}
                <span className="gradient-text">20,000</span> MAN YEARS OF
                EXPERIENCE.
              </h2>
              <p className="text-gray-400 leading-relaxed mb-6">
                Shakuniya Solutions is a leading Ios app development company and
                known all over the world of its superior quality
                services.Shakuniya Solutions offers a full range of highly
                innovative and out of the box Ios app development supported Ios
                platform that help business to reinforce their marketing
                strategy and in acquiring a sizable amount of consumers within
                the market.As an established Ios mobile app development company,
                we help the customers increase visibility, improve agility, and
                add innovation to their offerings for the target market through
                the mobile application.
              </p>
              <p className="text-gray-400 leading-relaxed">
                Our offshore app development team excels in the development of
                interactive, feature-packed, and high-functioning applications.
                We manage a multi-thread work environment with a team of
                proficient designers, developers, engineers, business analysts &
                architects. They specialize in custom Ios app development for
                creating cross-platform as well as native apps for SMEs and
                enormous organizations. We provide an foothold over your
                competitors by creating applications to leverage future-proof
                technology and app trends.
              </p>
            </div>

            <div className="grid sm:grid-cols-2 gap-4">
              {iosFeatures.map((feature, index) => {
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
                      {feature.description}
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
        title="Our Development Services."
        subtitle="
We deliver highest level of customer service by deploying innovative and collaborative project management systems to build the most professional, robust and highly scalable web & IOS solutions with highest quality standards."
        features={developmentFeatures}
      />

      <ServiceProcess />

      <ServiceCommitment />

      <ServiceCTA />
    </div>
  );
};

export default IosDevelopment;
