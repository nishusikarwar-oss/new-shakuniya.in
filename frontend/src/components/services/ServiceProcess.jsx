"use client"
import { FileText, Users, Calendar, CreditCard } from "lucide-react";

const processSteps = [
  {
    icon: FileText,
    title: "Post Your Project Requirements",
    description: "Our experts will thoroughly review your project requirements given by you and ensure the success of the project."
  },
  {
    icon: Users,
    title: "Discuss Project Details With Our Analysts",
    description: "Our experts will contact you within no time to debate your project related queries and to supply the simplest solution."
  },
  {
    icon: Calendar,
    title: "Choose Engagement Terms & Timelines",
    description: "We provide unmatched engagement and maintenance services to enhance your web application performance."
  },
  {
    icon: CreditCard,
    title: "Securely Pay Online And Get Started!",
    description: "Ensure the website is secure before entering payment details and get started with your project."
  }
];

const ServiceProcess = () => {
  return (
    <section className="py-20 lg:py-28 relative bg-gradient-to-b from-transparent via-purple-950/10 to-transparent">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
            Our Development <span className="gradient-text">Process</span>
          </h2>
          <p className="text-muted-foreground text-lg max-w-3xl mx-auto">
            We deliver highest level of customer service by deploying innovative and collaborative project management systems to build the most professional, robust and highly scalable solutions.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          {processSteps.map((step, index) => {
            const IconComponent = step.icon;
            return (
              <div 
                key={index}
                className="glass-card p-6 text-center hover:bg-white/10 rounded-4xl hover:shadow-purple-500/20 transition-all duration-300 group hover:-translate-y-2 relative"
              >
                {/* Step number */}
                <div className="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-cyan-500 flex items-center justify-center text-sm font-bold text-white">
                  <span className="">{index + 1}</span>
                </div>
                
                <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500/20 to-cyan-500/20 flex items-center justify-center mb-5 mx-auto group-hover:from-purple-500/30 group-hover:to-cyan-500/30 transition-all">
                  <IconComponent size={32} className="text-purple-400" />
                </div>
                <h3 className="text-lg font-bold mb-3 text-foreground">{step.title}</h3>
                <p className="text-muted-foreground text-sm leading-relaxed">{step.description}</p>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default ServiceProcess;