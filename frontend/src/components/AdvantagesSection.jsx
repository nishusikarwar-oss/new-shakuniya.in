"use client"
import { CheckCircle2 } from "lucide-react";
import teamIllustration from "@/images/team-illustration.png";

const advantages = [
  "Reduce costs or expenses by using our package.",
  "Focus on your Core Area of business while we manage your online presence.",
  "Benefit from Time-to-Market advantages that we can offer as specialists.",
  "Critical or particular skills available on demand under one roof.",
];

const AdvantagesSection = () => {
  return (
    <section className="py-24 relative overflow-hidden bg-[#0a0a16]">
      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        <div className="text-center mb-16">
          <h2 className="text-4xl  md:text-5xl lg:text-6xl font-bold  text-white uppercase tracking-tight">
            Advantages
          </h2>
        </div>

        <div className="grid lg:grid-cols-2 gap-16 items-center">
          {/* Left - Advantages List */}
          <div className="space-y-6">
            {advantages.map((advantage, index) => (
              <div
                key={index}
                className="flex items-center gap-6 p-6 bg-[#16162a]/60 backdrop-blur-xl border border-white/5 rounded-2xl hover:scale-105 hover:shadow-xl/30 shadow-purple-400/50 group hover:bg-[#1c1c36]/80 transition-all duration-300"
              >
                <div className="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-r from-[#3b82f6] to-[#06b6d4] flex items-center justify-center">
                  <CheckCircle2 className="w-5 h-5 text-white" />
                </div>
                <p className="text-gray-300 text-lg font-medium leading-relaxed">
                  {advantage}
                </p>
              </div>
            ))}
          </div>

          {/* Right - Illustration */}
          <div className="flex justify-center lg:justify-end">
            <div className="relative">
              <div className="absolute inset-0 bg-gradient-to-r from-[#9333ea]/20 to-[#06b6d4]/20 blur-[100px] rounded-full" />
              <img
                src={teamIllustration.src}
                alt="Team working on digital marketing and analytics"
                className="relative z-10 w-full max-w-xl rounded-4xl   hover:shadow-2xl/30 shadow-purple-600/50 animate-float"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default AdvantagesSection;
