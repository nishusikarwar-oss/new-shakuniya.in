"use client"

import { Button } from "@/components/ui/button";
import { ArrowRight } from "lucide-react";

const ServiceHero = ({
  title,
  highlightText,
  description,
  backgroundImage,
  backgroundVideo,
  videoBlur = "sm",
}) => {
  const blurClasses = {
    none: "",
    sm: "backdrop-blur-sm",
    md: "backdrop-blur-md",
    lg: "backdrop-blur-lg",
    xl: "backdrop-blur-xl",
  };

  return (
    <section className="relative min-h-[70vh] flex items-center overflow-hidden">
      
      {/* Background */}
      <div className="absolute inset-0 z-0">
        {backgroundVideo ? (
          <div className="absolute inset-0">
            <video
              autoPlay
              loop
              muted
              playsInline
              className="absolute inset-0 w-full h-full object-cover"
            >
              <source src={backgroundVideo} type="video/mp4" />
            </video>
            <div className={`absolute inset-0 bg-background/70 ${blurClasses[videoBlur] || blurClasses.sm}`} />
          </div>
        ) : backgroundImage ? (
          <div
            className="absolute inset-0 bg-cover bg-center"
            style={{ backgroundImage: `url(${backgroundImage})` }}
          >
            <div className="absolute inset-0 bg-gradient-to-r from-background via-background/95 to-background/70" />
          </div>
        ) : (
          <div className="absolute inset-0 bg-gradient-to-br from-background via-background to-purple-950/20">
            <div className="animated-grid absolute inset-0" />
          </div>
        )}

        {/* Decorative elements */}
        <div className="absolute top-20 right-10 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl animate-float" />
        <div className="absolute bottom-20 left-10 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl animate-float-delayed" />
      </div>

      {/* Content */}
      <div className="container mx-auto px-4 lg:px-8 relative z-10 pt-32 pb-16">
        <div className="max-w-3xl">
          <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
            <span className="gradient-text">{highlightText}</span>
            {title && (
              <>
                <br />
                <span className="text-foreground">{title}</span>
              </>
            )}
          </h1>

          <p className="text-lg md:text-xl text-muted-foreground mb-8 max-w-2xl leading-relaxed">
            {description}
          </p>

          <div className="flex flex-wrap gap-4">
            <Button variant="hero" size="lg" className="group">
              Get Started
              <ArrowRight
                className="ml-2 group-hover:translate-x-1 transition-transform"
                size={18}
              />
            </Button>

            <Button variant="heroOutline" size="lg">
              Learn More
            </Button>
          </div>
        </div>
      </div>

    </section>
  );
};

export default ServiceHero;
