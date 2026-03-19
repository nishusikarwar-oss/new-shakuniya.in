"use client";
import {
  CheckCircle,
  Lightbulb,
  Users,
  DollarSign,
  Brain,
  Rocket,
  MessageSquare,
} from "lucide-react";
import aboutus from "@/images/aboutus.png";
import Footer from "@/components/Footer";
import Navbar from "@/components/Navbar";

/* ================= DATA ================= */

const values = [
  {
    title: "WE BELIEVE IN BY THE PEOPLE & FOR THE PEOPLE STRATEGY!",
    description:
      "We challenge the standards of configuration, push the limits of advancement, and we've not looked back since we consistently discovering, developing & learning from that point forward.",
  },
  {
    title: "WE VALUE OUR PEOPLE",
    description:
      "It is not what makes Shakuniya Solutions successful, it is who made it! Our experienced, knowledgeable, skilled & dedicated team is what we are proud of. We value our people more than anything else & take gratitude when we achieve tasks together.",
  },
  {
    title: "OUR CLIENTS ARE OUR REFLECTION!",
    description:
      'We deeply understand your intent & work accordingly. Because we measure success on basis of our clients feedback. We work with a motto of "Client satisfaction is our prime concern".',
  },
  {
    title: "WE DO NOT COMPROMISE IN QUALITY!",
    description:
      "Having an impact is a commendable objective, yet helping individuals to accomplish the things along with the quality they need to accomplish is truly what we are associated with. #noqualitycompromise",
  },
  {
    title: "24*7 UNCONDITIONAL SERVICES!",
    description:
      "Our esteemed services ensure the customers to have results past the desires & expectations.",
  },
];

const features = [
  {
    icon: Brain,
    title: "We brainstorm a lot!",
    description:
      "Idea are brainstorming tactics to evolve the right & clean, slice between data coding & looking for innovative & user friendly web applications.",
  },
  {
    icon: Lightbulb,
    title: "Believe in Innovation!",
    description:
      "Innovation is like Jugaad. It only comes when you give it a try. We believe that extraordinary things come with innovation which help you stand & lead in the crowd.",
  },
  {
    icon: Users,
    title: "Creative Developers!",
    description:
      'Our developers are curious about to develop new creative ideas. We say "TECHSPERT" they have an eye on the market facts, that benefits in the best-as-a-service environment.',
  },
  {
    icon: DollarSign,
    title: "Value for Money!",
    description:
      "We understand the value of money & that with a creative approach we serve you the best service for your satisfaction & with minimum cost. The flexibility we give aims to achieve a total ROI based results.",
  },
];

/* ================= COMPONENT ================= */

const About = () => {
  return (

    <div className="min-h-screen bg-[#05070a] text-white">

<Navbar/>

      {/* ================= HERO ================= */}
      <section className="relative min-h-[80vh] flex items-center justify-center pt-20 overflow-hidden">
        <div
          className="absolute inset-0 bg-cover bg-center opacity-60 scale-110"
          style={{ backgroundImage: `url(${aboutus.src})` }}
        >
          <div className="absolute inset-0 bg-gradient-to-b from-[#05070a]/80 via-[#05070a]/60 to-[#05070a]" />
        </div>

        <div className="relative z-10 text-center px-4 max-w-5xl">
          <h1 className="text-5xl  md:text-7xl font-bold mb-8 tracking-tighter">
            <span className="bg-gradient-to-r from-white via-gray-400 to-white bg-clip-text text-transparent">
              About Us
            </span>
          </h1>
          <div className="w-20 h-1 bg-gradient-to-r from-purple-500 to-blue-500 mx-auto mb-8" />
          <p className="text-lg md:text-2xl text-gray-400 mb-4 font-medium">
            Performance does Matter. Reinventing your limits with technology!
          </p>

          <p className="text-xl md:text-3xl font-bold bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
            Global Leader for all IT Solutions
          </p>
        </div>
      </section>

      {/* ================= VALUES + FORM ================= */}
      <section className="py-16 lg:py-24">
        <div className="container mx-auto px-9 grid lg:grid-cols-2 gap-20 items-start">
          {/* VALUES */}
          <div className="space-y-12">
            {values.map((item, i) => (
              <div key={i} className="flex gap-6 mb-5 group">
                <div className="flex-shrink-0 mt-1">
                  <div className="w-7 h-7 rounded-full border border-purple-500 flex items-center justify-center group-hover:bg-purple-500 transition-all duration-300">
                    <CheckCircle className="w-4 h-4 text-purple-500 group-hover:text-white" />
                  </div>
                </div>
                <div>
                  <h3 className="text-xl md:text-2xl font-semibold text-white mb-2 leading-tight uppercase tracking-wider">
                    {item.title}
                  </h3>
                  <p className="text-gray-400  text-lg">{item.description}</p>
                </div>
              </div>
            ))}
          </div>

          {/* FORM + ACTIONS */}
          <div className="space-y-10">
            {/* FORM CARD */}
            <div className="bg-[#11141d] p-6 md:p-9 transition-transform duration-300 ease-in-out hover:scale-105 hover:shadow-purple-500/40 rounded-[3rem] border border-gray-800 shadow-2xl relative overflow-hidden">
              <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 via-blue-500 to-purple-500" />
              <p className="text-center text-sm font-bold text-purple-400 mb-8 tracking-[0.3em] uppercase">
                100% Secure and Confidential
              </p>

              <form className="space-y-8">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                  <input
                    placeholder="Your Name*"
                    className="bg-[#1c212c] border border-gray-700/50 text-white px-8 py-4 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/50 w-full placeholder:text-gray-500 transition-all text-lg"
                  />
                  <input
                    placeholder="Your Email*"
                    className="bg-[#1c212c] border border-gray-700/50 text-white px-8 py-5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/50 w-full placeholder:text-gray-500 transition-all text-lg"
                  />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                  <input
                    placeholder="Your Phone*"
                    className="bg-[#1c212c] border border-gray-700/50 text-white px-8 py-5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/50 w-full placeholder:text-gray-500 transition-all text-lg"
                  />
                  <div className="relative">
                    <select className="bg-[#1c212c] border border-gray-700/50 text-white px-8 py-5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/50 w-full appearance-none cursor-pointer text-lg">
                      <option className="bg-[#11141d]">Your Budget</option>
                      <option className="bg-[#11141d]">$5k – $10k</option>
                      <option className="bg-[#11141d]">$10k – $25k</option>
                    </select>
                    <div className="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                      <svg
                        className="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          strokeWidth="2"
                          d="M19 9l-7 7-7-7"
                        />
                      </svg>
                    </div>
                  </div>
                </div>

                <textarea
                  rows="3"
                  placeholder="Project Description*"
                  className="bg-[#1c212c] border border-gray-700/50 text-white px-8 py-5 mb-4 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500/50 w-full placeholder:text-gray-500 transition-all resize-none text-lg"
                />

                <button className="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-2xl text-xl font-bold transition-all shadow-xl shadow-purple-500/30 active:scale-[0.98]">
                  Submit
                </button>
              </form>
            </div>

            {/* ADDITIONAL ACTIONS */}
            <div
              className="bg-[#11141d] p-6 md:p-9 rounded-[3rem] border border-gray-800 shadow-2xl relative overflow-hidden 
transition-transform duration-300 ease-in-out hover:scale-105 hover:shadow-purple-500/40"
            >
              <button className="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-2xl text-xl font-bold uppercase tracking-[0.25em] transition-all shadow-xl shadow-purple-500/30 active:scale-[0.98]">
                KNOW MORE ABOUT US
              </button>

              <div className="grid grid-cols-3 gap-6 pt-6 border-t border-gray-800/50">
                <div className="flex flex-col items-center gap-2 group cursor-pointer">
                  <div className="w-14 h-14 flex items-center justify-center text-purple-400 group-hover:scale-110 group-hover:text-purple-300 transition-all duration-300">
                    <Rocket className="w-10 h-10 " />
                  </div>
                  <span className="text-xs md:text-base text-gray-400 group-hover:text-white transition text-center font-bold tracking-tight">
                    Our Products
                  </span>
                </div>

                <div className="flex flex-col items-center gap-2 group cursor-pointer">
                  <div className="w-14 h-14 flex items-center justify-center text-purple-400 group-hover:scale-110 group-hover:text-purple-300 transition-all duration-300">
                    <Lightbulb className="w-10 h-10 " />
                  </div>
                  <span className="text-xs md:text-base text-gray-400 group-hover:text-white transition text-center font-bold tracking-tight">
                    Our Services
                  </span>
                </div>

                <div className="flex flex-col items-center gap-2 group cursor-pointer">
                  <div className="w-14 h-14 flex items-center justify-center text-purple-400 group-hover:scale-110 group-hover:text-purple-300 transition-all duration-300">
                    <MessageSquare className="w-10 h-10 " />
                  </div>
                  <span className="text-xs md:text-base text-gray-400 group-hover:text-white transition text-center font-bold tracking-tight">
                    Contact Us
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ================= MISSION ================= */}
      <section className="py-4 text-center px-4">
        <div className="flex flex-col items-center mb-8">
          <div className="w-16 h-1 bg-gradient-to-r from-purple-500 to-blue-500 mb-6" />
          <h2 className="text-5xl md:text-6xl font-bold tracking-tight">
            <div className="text-white">OUR</div>
            <div className="bg-gradient-to-r from-purple-400 via-blue-400 to-purple-400 bg-clip-text text-transparent">
              MISSION
            </div>
          </h2>
        </div>

        <div className="max-w-5xl mx-auto space-y-6">
          <p className="text-gray-400 text-lg md:text-2xl leading-relaxed">
            At Shakuniya Solutions, our mission is to empower businesses with
            innovative, reliable, and cutting-edge technology solutions.
          </p>
          <p className="text-gray-400 text-lg md:text-2xl leading-relaxed">
            We are dedicated to delivering exceptional services that drive
            digital transformation, enhance operational efficiency, and create
            lasting value.
          </p>
          <p className="text-gray-400 text-lg md:text-2xl leading-relaxed">
            Through our expertise in software development, cloud solutions, and
            seamless integration, we aim to help our clients stay ahead in an
            ever-evolving technological landscape, ensuring sustainable growth
            and success.
          </p>
        </div>
      </section>

      {/* ================= FEATURES ================= */}
      <section className="py-16 lg:py-24">
        <div className="container mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          {features.map((f, i) => {
            const Icon = f.icon;
            return (
              <div
                key={i}
                className="bg-[#11141d] p-8 rounded-[2.5rem] border border-gray-800 flex flex-col items-center text-center group hover:border-purple-500/50 transition-all duration-500 hover:shadow-purple-500/40 hover:-translate-y-2"
              >
                <div className="w-20 h-20 bg-[#1c212c] rounded-3xl flex items-center justify-center mb-8 border border-gray-700 relative overflow-hidden group-hover:border-purple-500/50 transition-colors">
                  <div className="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-blue-500/10" />
                  <Icon className="w-10 h-10  text-purple-400 relative z-10 group-hover:scale-110 transition-transform duration-500" />
                </div>
                <div className="w-12 h-1 bg-gradient-to-r from-purple-500 to-blue-500 mb-8 rounded-full" />
                <h3 className="text-2xl font-bold text-white mb-4 tracking-tight">
                  {f.title}
                </h3>
                <p className="text-gray-400 leading-relaxed text-base md:text-lg">
                  {f.description}
                </p>
              </div>
            );
          })}
        </div>
      </section>
      <Footer/>
    </div>
  );
};

export default About;
