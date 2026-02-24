"use client"
import { useEffect, useState, useRef } from "react";

const stats = [
  { value: 5, suffix: "+", label: "Years of Business" },
  { value: 50, suffix: "+", label: "IT Professionals" },
  { value: 45, suffix: "K", prefix: "ISO ", label: "NASSCOM & STPI Accreditation" },
  { value: 10, suffix: "+", label: "Clients Worldwide" },
  { value: 15, suffix: "+", label: "Projects Executed" },
];

const ServiceStats = () => {
  const [isVisible, setIsVisible] = useState(false);
  const sectionRef = useRef(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true);
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
    <section
      ref={sectionRef}
      className="py-16 lg:py-20 relative overflow-hidden"
      style={{
        background:
          "linear-gradient(135deg, hsl(263 70% 15% / 0.8) 0%, hsl(222 47% 8%) 50%, hsl(200 100% 15% / 0.5) 100%)",
      }}
    >
      {/* Background blur elements */}
      <div className="absolute top-0 left-1/4 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl" />
      <div className="absolute bottom-0 right-1/4 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl" />

      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        <div className="text-center mb-12">
          <h2 className="text-2xl md:text-3xl lg:text-4xl font-bold">
            We Transform Every Industry And{" "}
            <span className="gradient-text">Business</span> Processes.
          </h2>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
          {stats.map((stat, index) => (
            <div key={index} className="text-center">
              <div className="text-3xl md:text-4xl lg:text-5xl font-bold gradient-text mb-2">
                {isVisible && (
                  <>
                    {stat.prefix}
                    <CountUp target={stat.value} />
                    {stat.suffix}
                  </>
                )}
              </div>
              <p className="text-muted-foreground text-sm">
                {stat.label}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

const CountUp = ({ target }) => {
  const [count, setCount] = useState(0);

  useEffect(() => {
    const duration = 2000;
    const steps = 60;
    const increment = target / steps;
    let current = 0;

    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        setCount(target);
        clearInterval(timer);
      } else {
        setCount(Math.floor(current));
      }
    }, duration / steps);

    return () => clearInterval(timer);
  }, [target]);

  return <span>{count}</span>;
};

export default ServiceStats;
