'use client'
import { Users, Briefcase, TrendingUp } from "lucide-react";

const CareerHero = () => {
  return (
    <section className="relative min-h-[60vh] flex items-center justify-center overflow-hidden bg-[#05070a]">
      
      {/* Background pattern */}
      <div className="absolute inset-0 z-0">
        <div className="absolute inset-0 bg-gradient-to-br from-[#05070a] via-transparent to-[#05070a]/80" />
        <div className="absolute top-20 right-10 w-72 h-72 bg-purple-500/20 rounded-full blur-xl animate-float" />
        <div className="absolute bottom-20 left-10 w-96 h-96 bg-cyan-500/15 rounded-full blur-xl animate-float-delayed" />
      </div>

      <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-20" />

      {/* Glow blobs */}
      <div className="absolute top-20 left-10 w-24 h-24 bg-yellow-400/20 rounded-full blur-2xl" />
      <div className="absolute bottom-20 right-10 w-32 h-32 bg-cyan-400/20 rounded-full blur-2xl" />

      {/* Floating icons */}
      <FloatingIcon position="top-1/4 left-[15%]" delay="0s">
        <Users />
      </FloatingIcon>
      <FloatingIcon position="top-1/3 right-[15%]" delay="0.6s">
        <Briefcase />
      </FloatingIcon>
      <FloatingIcon position="bottom-1/4 left-[20%]" delay="1.2s">
        <TrendingUp />
      </FloatingIcon>

      {/* Content */}
      <div className="container mx-auto h-[500px] px-4 lg:px-8 relative z-10 text-center pt-44 animate-fade-in-up">
        <h1 className="text-5xl md:text-7xl lg:text-8xl font-extrabold mb-6 tracking-tight drop-shadow-lg">
          <span className="gradient-text">CAREER</span>
        </h1>

        <h2 className="text-xl md:text-2xl font-semibold text-[#edebeb] mb-4">
          THINK CAREER PROGRESSION
        </h2>

        <p className="text-[#b5b0b0] text-lg max-w-2xl mx-auto">
          It's time to take your career graph to the next level!
        </p>
      </div>
    </section>
  );
};

const FloatingIcon = ({ children, position, delay }) => (
  <div className={`absolute ${position} hidden md:block`}>
    <div
      className="bg-white/10 backdrop-blur-md p-3 rounded-xl "
      style={{ animationDelay: delay }}
    >
      <div className="w-8 h-8 text-white/80">
        {children}
      </div>
    </div>
  </div>
);

export default CareerHero;
