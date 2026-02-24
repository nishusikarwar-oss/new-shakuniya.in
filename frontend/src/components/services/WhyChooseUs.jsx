"use client";
import { Check } from "lucide-react";
import customers from "../../images/customers.png";

const defaultPoints = [
  "Assured satisfaction throughout product lifecycle",
  "All stakeholders are on the priority and with 100 percent client satisfaction",
  "100+ combined years of experience with experts in the field of mobile development",
  "ISO certified and trusted by enterprises and startups for high performance",
  "Strictly about the product on client's servers for 100% data security",
  "100% Dedicated & Privacy secure: Guarantee",
];

const WhyChooseUs = ({
  title = "Why Customers In Over 10+ Countries Choose Shakuniya Solutions?",
  subtitle = "Shakuniya Solutions",
  points = defaultPoints,
  backgroundImage,
}) => {
  return (
    <section className="py-16 lg:py-24 relative overflow-hidden">
      
      {/* Background */}
      <div className="absolute inset-0 z-0">
        {backgroundImage ? (
          <div
            className="absolute inset-0 bg-cover bg-center"
            style={{ backgroundImage: `url(${backgroundImage})` }}
          >
            <div className="absolute inset-0 bg-gradient-to-r from-background/95 via-background/80 to-transparent" />
          </div>
        ) : (
          <div className="absolute inset-0 bg-gradient-to-r from-background via-background to-purple-950/30">
            <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjAzKSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-50" />
          </div>
        )}
      </div>

      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          
          {/* Left visual */}
          <div className="relative hidden lg:block">
            <div className="aspect-[4/3] rounded-2xl overflow-hidden bg-gradient-to-br from-purple-900/50 to-cyan-900/50 backdrop-blur-sm border border-white/10">
              <div className="absolute inset-0 flex items-center justify-center">
                <img src={customers.src} className="" />
                {/* <div className="text-center">
                  <div className="w-24 h-24 bg-gradient-to-br from-purple-500 to-cyan-500 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                    <span className="text-3xl font-bold text-white">SS</span>
                  </div>
                  <p className="text-white/70 text-sm">Global Presence</p>
                </div> */}
              </div>
            </div>
          </div>

          {/* Right content */}
          <div>
            <h2 className="text-3xl md:text-4xl font-bold mb-8 text-foreground leading-tight">
              {title.split("Shakuniya Solutions")[0]}
              <span className="gradient-text">Shakuniya Solutions</span>?
            </h2>

            <div className="space-y-4">
              {points?.map((point, index) => (
                <div key={index} className="flex items-start gap-3">
                  <div className="w-6 h-6 rounded-full bg-gradient-to-r from-purple-500 to-cyan-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <Check size={14} className="text-white" />
                  </div>
                  <p className="text-muted-foreground leading-relaxed">
                    {point}
                  </p>
                </div>
              ))}
            </div>
          </div>

        </div>
      </div>
    </section>
  );
};

export default WhyChooseUs;
