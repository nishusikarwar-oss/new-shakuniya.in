"use client"
import { useEffect, useState, useRef } from "react";

const stats = [
  { value: 5, suffix: "+", label: "Years", sublabel: "Industry Experience" },
  { value: 15, suffix: "+", label: "Finished Projects", sublabel: "" },
  { value: 10, suffix: "+", label: "Satisfied Clients", sublabel: "" },
  { value: 50, suffix: "+", label: "Members Team", sublabel: "" },
];

const useCountUp = (end, duration = 2000, startCounting = false) => {
  const [count, setCount] = useState(0);

  useEffect(() => {
    if (!startCounting) return;

    let startTime = null;

    const animate = (timestamp) => {
      if (!startTime) startTime = timestamp;
      const progress = Math.min((timestamp - startTime) / duration, 1);
      setCount(Math.floor(progress * end));

      if (progress < 1) {
        requestAnimationFrame(animate);
      }
    };

    requestAnimationFrame(animate);
  }, [end, duration, startCounting]);

  return count;
};

const StatsSection = () => {
  const [isVisible, setIsVisible] = useState(false);
  const sectionRef = useRef(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true);
          observer.disconnect();
        }
      },
      { threshold: 0.3 }
    );

    if (sectionRef.current) {
      observer.observe(sectionRef.current);
    }

    return () => observer.disconnect();
  }, []);

  return (
    <section ref={sectionRef} className="py-24 relative overflow-hidden bg-[#0a0a16]">
      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-12">
          {stats.map((stat, index) => {
            const count = useCountUp(stat.value, 2000, isVisible);

            return (
              <div key={index} className="text-center">
                <div className="mb-4">
                  <span className="text-6xl md:text-7xl lg:text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-b from-[#b366ff] to-[#6366f1]">
                    {count}
                    {stat.suffix}
                  </span>
                </div>

                <div className="space-y-1">
                  <p className="text-xl md:text-2xl font-bold text-white">
                    {stat.label}
                  </p>

                  {stat.sublabel && (
                    <p className="text-base text-gray-400 font-medium">
                      {stat.sublabel}
                    </p>
                  )}
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default StatsSection;
