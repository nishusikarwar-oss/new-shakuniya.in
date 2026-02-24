"use client";
import React from "react";
import { Button } from "@/components/ui/button";
import crystal from "@/images/crystal.png";
import {
  ArrowRight,
  Headphones,
  Music,
  Mic,
  Radio,
  Users,
  Shield,
  Zap,
  Globe,
  Server,
  Lock,
  Settings,
  Podcast,
} from "lucide-react";
import Navbar from "@/components/Navbar";

const features = [
  {
    icon: Headphones,
    title: "Crystal-clear audio",
    description:
      "Real-time audio streaming engine supports 48kHz audio sampling and delivers high-quality real-time audio with outstanding acoustic echo cancellation, automatic noise suppressor, and automatic gain control.",
  },
  {
    icon: Users,
    title: "Outstanding co-host capabilities",
    description:
      "Support multiple co-hosts with seamless audio mixing and turn-taking features.",
  },
  {
    icon: Radio,
    title: "Unlimited audience size",
    description:
      "Scale to millions of concurrent listeners without quality degradation.",
  },
  {
    icon: Mic,
    title: "Flexible real-time messaging",
    description:
      "Integrated messaging for audience engagement during audio streams.",
  },
  {
    icon: Music,
    title: "Advanced engagement features",
    description:
      "Virtual gifts, reactions, and interactive elements for audience participation.",
  },
];

const solutions = [
  {
    icon: Globe,
    title: "A one-stop solution",
    description:
      "With RTC and IM capabilities included, you can easily build the features required for most live audio use cases.",
  },
  {
    icon: Zap,
    title: "Global reach with ultra-low latency",
    description:
      "Real-time communications network covers 200+ countries and regions. With our seamless global network scheduling strategy, it delivers real-time audio and video streaming to users with ultra-low latency.",
  },
  {
    icon: Server,
    title: "Optimized performance under weak networks",
    description:
      "With our proprietary QUIC technologies, audio and video streams are delivered reliably even under weak network conditions, so you can promise your users still an excellent communication experience at all times.",
  },
  {
    icon: Users,
    title: "Support for massive concurrency",
    description:
      "High concurrency system architecture enables your apps to handle hundreds of millions of concurrent messages easily, meeting the needs of large-scale live streaming events.",
  },
  {
    icon: Lock,
    title: "Robust data security",
    description:
      "Implement strict privilege control and security of multiple dimensions, including data encryption, storage destruction, and other data operations.",
  },
  {
    icon: Settings,
    title: "Flexible and feature-rich APIs",
    description:
      "Plenty of APIs for you to implement complex business features, with customizable UI and branding.",
  },
];

const AudioStreaming = () => {
  return (
    <div className="min-h-screen">
      <Navbar/>
      {/* Hero Section */}
      <section className="relative min-h-[70vh] mt-20 flex items-center overflow-hidden bg-[#05070a]">
        <div className="absolute inset-0 z-0">
          <div className="absolute inset-0 bg-gradient-to-br from-purple-900/40 via-transparent to-cyan-900/30">
            <div className="animated-grid absolute inset-0" />
          </div>
          <div className="absolute top-20 right-10 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl animate-float" />
          <div className="absolute bottom-20 left-10 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl animate-float-delayed" />
        </div>

        <div className="container mx-auto px-4 lg:px-8 relative z-10 pt-32 pb-16">
          {/* Background Video */}
          {/* <video
    autoPlay
    loop
    muted
    playsInline
    className="absolute inset-0 w-full h-full object-cover scale-110 blur-xs"
  >
    <source src={video3} type="video/mp4" />
  </video> */}
          {/* <img src={video3} alt="" /> */}
          {/* Dark Overlay */}
          <div className="absolute inset-0 "></div>

          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                <span className="gradient-text">Live Audio Streaming</span>
              </h1>

              <p className="text-lg md:text-xl text-muted-foreground mb-8 max-w-2xl leading-relaxed text-[#d7cfcf]">
                Experience crystal-clear audio and discover new content with our
                live audio streaming app. Enjoy a vast library of music,
                podcasts, and audiobooks, create custom playlists, and chat with
                other users while listening. Secure payment gateway and reliable
                server-side architecture ensure seamless access to your favorite
                audio content anytime, anywhere.
              </p>

              <div className="glass-card inline-block p-4 rounded-xl mb-6">
                <p className="text-sm text-muted-foreground mb-1 text-[#edebeb]">
                  PRICING DETAILS
                </p>
                <p className="text-2xl font-bold text-foreground text-[#f2f1f1]">
                  $7000.00 / ₹5,25,000 INR
                </p>
              </div>

              <div className="flex flex-wrap gap-4">
                <Button variant="hero" size="lg" className="group">
                  Buy Now
                  <ArrowRight
                    className="ml-2 group-hover:translate-x-1 transition-transform"
                    size={18}
                  />
                </Button>
              </div>
            </div>

            <div className="relative hidden lg:block">
              <div className="w-full h-full bg-gradient-to-br from-purple-500/20 to-cyan-500/20 rounded-2xl flex items-center justify-center border border-white/10 glass-card">
                <img
                  src="/videos/video3.gif"
                  className="w-full h-full object-cover rounded-2xl"
                />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features for creating incredible live social audio experiences */}
      <section className="py-20 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-4">
              Features for creating incredible live
            </h2>
            <h3 className="text-3xl md:text-4xl font-bold gradient-text">
              social audio experiences
            </h3>
          </div>

          <div className="grid lg:grid-cols-2 gap-12 items-start">
            <div className="space-y-6">
              {features.map((feature, index) => {
                const IconComponent = feature.icon;
                return (
                  <div
                    key={index}
                    className="flex items-start gap-4 p-4 glass-card border border-white/10 rounded-xl hover:bg-white/5 transition-all"
                  >
                    <div className="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center flex-shrink-0 border border-purple-500/20">
                      <IconComponent size={20} className="text-purple-400" />
                    </div>
                    <div>
                      <h4 className="font-bold text-white mb-1">
                        {feature.title}
                      </h4>
                      <p className="text-[#b5b0b0] text-sm">
                        {feature.description}
                      </p>
                    </div>
                  </div>
                );
              })}
            </div>

            <div className="relative">
              <div className="w-full h-full mt-8 bg-gradient-to-br from-purple-500/10 to-cyan-500/10 rounded-2xl flex items-center justify-center border border-white/10 glass-card">
                <div className="text-center">
                  <img
                    src={crystal.src}
                    className="w-full h-full object-cover border-2 rounded-2xl hover:scale-105 transition-all duration-300 shadow-xs shadow-purple-300 "
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* High-quality services section */}
      <section className="py-20 lg:py-28 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-white">
              Deliver high-quality services with
            </h2>
            <h3 className="text-3xl md:text-4xl lg:text-5xl font-bold">
              <span className="gradient-text">state-of-the-art solutions</span>
            </h3>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {solutions.map((solution, index) => {
              const IconComponent = solution.icon;
              return (
                <div
                  key={index}
                  className="glass-card p-6 hover:bg-white/10 transition-all duration-300 group hover:-translate-y-2 border border-white/10"
                >
                  <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500/20 to-cyan-500/20 flex items-center justify-center mb-5 group-hover:from-purple-500/30 group-hover:to-cyan-500/30 transition-all">
                    <IconComponent size={28} className="text-purple-400" />
                  </div>
                  <h3 className="text-xl font-bold mb-3 text-white">
                    {solution.title}
                  </h3>
                  <p className="text-[#b5b0b0] text-sm leading-relaxed">
                    {solution.description}
                  </p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 lg:py-28 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="glass-card p-8 md:p-12 text-center max-w-4xl mx-auto border border-white/10">
            <h2 className="text-3xl md:text-4xl font-bold mb-4 text-white">
              Ready to Build Your{" "}
              <span className="gradient-text">Audio Streaming App?</span>
            </h2>
            <p className="text-[#b5b0b0] text-lg mb-8">
              Create immersive audio experiences for your users
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Button variant="hero" size="lg" className="group">
                Get Started Now
                <ArrowRight
                  className="ml-2 group-hover:translate-x-1 transition-transform"
                  size={18}
                />
              </Button>
              <Button
                variant="heroOutline"
                size="lg"
                className="text-white border-white/20 hover:bg-white/10"
              >
                Contact Sales
              </Button>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default AudioStreaming;
