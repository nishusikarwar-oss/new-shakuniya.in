
'use client'
import { Users, Briefcase, TrendingUp } from "lucide-react";
import { useState, useEffect } from "react";

const CareerHero = () => {
  const [stats, setStats] = useState({
    totalJobs: 0,
    totalDepartments: 0,
    totalLocations: 0
  });

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      // Direct fetch calls - no API client
      const jobsRes = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/jobs`);
      const deptsRes = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/departments`);
      const locsRes = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/locations`);
      
      const jobsData = await jobsRes.json();
      const deptsData = await deptsRes.json();
      const locsData = await locsRes.json();

      setStats({
        totalJobs: jobsData.data?.length || jobsData.length || 0,
        totalDepartments: deptsData.data?.length || deptsData.length || 0,
        totalLocations: locsData.data?.length || locsData.length || 0
      });
    } catch (error) {
      console.error('Error fetching stats:', error);
    }
  };

  return (
    <section className="relative min-h-[60vh] flex items-center justify-center overflow-hidden bg-[#0a0a0f]">
      
      {/* Background pattern */}
      <div className="absolute inset-0 z-0">
        <div className="absolute inset-0 bg-gradient-to-br from-[#0a0a0f] via-transparent to-[#0a0a0f]/80" />
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
        <h1 className="text-5xl md:text-7xl lg:text-8xl font-extrabold mb-6 tracking-tight drop-shadow-lg text-white">
          <span className="text-transparent animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">CAREER</span>
        </h1>

        <h2 className="text-xl md:text-2xl font-semibold text-gray-200 mb-4 uppercase tracking-widest">
          THINK CAREER PROGRESSION
        </h2>

        <p className="text-gray-400 text-lg max-w-2xl mx-auto mb-6">
          It's time to take your career graph to the next level!
        </p>

        {/* Dynamic Stats */}
        <div className="flex justify-center gap-8 mt-8">
          <div className="text-center">
            <div className="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-purple-600">{stats.totalJobs}+</div>
            <div className="text-sm text-gray-500 font-medium uppercase tracking-wider">Open Positions</div>
          </div>
          <div className="text-center">
            <div className="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-cyan-600">{stats.totalDepartments}+</div>
            <div className="text-sm text-gray-500 font-medium uppercase tracking-wider">Departments</div>
          </div>
          <div className="text-center">
            <div className="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-pink-600">{stats.totalLocations}+</div>
            <div className="text-sm text-gray-500 font-medium uppercase tracking-wider">Locations</div>
          </div>
        </div>
      </div>
    </section>
  );
};

const FloatingIcon = ({ children, position, delay }) => (
  <div className={`absolute ${position} hidden md:block`}>
    <div
      className="bg-white/10 backdrop-blur-md p-3 rounded-xl animate-float"
      style={{ animationDelay: delay }}
    >
      <div className="w-8 h-8 text-white/80">
        {children}
      </div>
    </div>
  </div>
);

export default CareerHero;