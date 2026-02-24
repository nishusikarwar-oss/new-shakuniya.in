"use client"

import { Eye, Truck, Headphones, Users, Clock } from "lucide-react";

const commitments = [
  {
    icon: Eye,
    title: "100% Transparency",
    description: "We believe complete transparency with our clients, so we keep them updated associated with project progress during the whole development cycle."
  },
  {
    icon: Truck,
    title: "95% On-time Delivery",
    description: "Our pursuit of agile development methodology has resulted in an enviable 95% on-time delivery diary for the past 15+ years of our operations."
  },
  {
    icon: Headphones,
    title: "Free 30 Days Support",
    description: "We take complete responsibility for our work and provide 30 days free support after delivery of the project to ensure smooth operation."
  },
  {
    icon: Users,
    title: "Flexible Engagements",
    description: "While developing software, the 'hard' side of skills, the essential 'soft' side lies in communications. Our developers are effective communicators."
  },
  {
    icon: Clock,
    title: "24x7 Support",
    description: "Our communication channels are open 24/7 that connects you with the acceptable team of experts to make sure smooth execution of your project."
  }
];

const ServiceCommitment = () => {
  return (
    <section className="py-20 lg:py-28 relative bg-background">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-foreground ">
            Our Commitment & <span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-cyan-500">Guaranty</span>
          </h2>
          <p className="text-muted-foreground text-lg max-w-3xl mx-auto">
            Shakuniya Solutions delivers robust, scalable and high performance software, web and mobile app development services to help you harness the power of technology, consulting and maximize your online business investment.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-5 gap-6">
          {commitments.map((item, index) => {
            const IconComponent = item.icon;
            return (
              <div 
                key={index}
                className="glass-card p-6 rounded-tl-4xl rounded-br-4xl hover:bg-gradient-to-br from-purple-500/20  to-cyan-500/20  text-center hover:bg-white/10 transition-all duration-300 group hover:-translate-y-2"
              >
                <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500/20  to-cyan-500/20 flex items-center justify-center mb-4 mx-auto group-hover:from-purple-500/30 group-hover:to-cyan-500/30 transition-all">
                  <IconComponent size={28} className="text-purple-400" />
                </div>
                <h3 className="text-base font-bold mb-3 text-foreground">{item.title}</h3>
                <p className="text-muted-foreground text-xs leading-relaxed">{item.description}</p>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default ServiceCommitment; 