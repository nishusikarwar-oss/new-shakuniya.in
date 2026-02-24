"use client";

import Link from "next/link";
import { ArrowRight, ArrowLeft } from "lucide-react";
import { Button } from "@/components/ui/button";
import { getAllServices } from "@/data/services";

export default function Services() {
  const allServices = getAllServices();

  return (
    <div className="min-h-screen bg-background">
      {/* Hero Section */}
      <section className="pt-32 pb-16 bg-gradient-to-br from-purple-900/20 via-background to-background">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
            <div className="text-center md:text-left space-y-4 flex-1">
              <span className="text-primary font-medium text-sm uppercase tracking-wider">
                What We Offer
              </span>
              <h1 className="text-4xl md:text-5xl font-bold text-foreground">
                Our Services
              </h1>
              <p className="text-muted-foreground max-w-2xl">
                Explore our comprehensive range of technology services designed
                to help your business thrive in the digital age. From mobile
                development to IT consulting, we&apos;ve got you covered.
              </p>
            </div>

            {/* Back to Home Button */}
            <Link href="/" className="self-center md:self-start mt-4 md:mt-8">
              <Button className="flex items-center gap-2">
                <ArrowLeft
                  size={16}
                  className="transition-transform duration-300 group-hover:-translate-x-1"
                />
                Back to Home
              </Button>
            </Link>
          </div>
        </div>
      </section>

      {/* Services Grid */}
      <section className="py-16">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {allServices.map((service) => {
              const IconComponent = service.icon;

              return (
                <Link
                  key={service.id}
                  href={`/services/${service.slug}`}
                  className="glass-card p-6 hover:border border-[#756af6] rounded-tl-4xl rounded-br-4xl group hover:border-primary/30 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-purple-500/10"
                >
                  <div className="flex flex-col h-full">
                    {/* Icon */}
                    <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500/20 to-cyan-500/20 flex items-center justify-center mb-4 group-hover:from-purple-500/30 group-hover:to-cyan-500/30 transition-all duration-300">
                      <IconComponent
                        size={28}
                        className="text-primary group-hover:text-purple-300 transition-colors"
                      />
                    </div>

                    {/* Content */}
                    <h3 className="text-xl font-semibold text-foreground mb-2 group-hover:text-white transition-colors">
                      {service.title}
                    </h3>
                    <p className="text-muted-foreground text-sm mb-4 flex-grow">
                      {service.shortDescription}
                    </p>

                    {/* Features Preview */}
                    {service.features && (
                      <ul className="space-y-1 mb-4">
                        {service.features.slice(0, 3).map((feature, index) => (
                          <li
                            key={index}
                            className="text-xs text-muted-foreground flex items-center gap-2"
                          >
                            <span className="w-1.5 h-1.5 rounded-full bg-primary" />
                            {feature}
                          </li>
                        ))}
                      </ul>
                    )}

                    {/* CTA */}
                    <div className="flex items-center gap-2 text-primary font-medium text-sm mt-auto pt-4 border-t border-border/50">
                      Learn More
                      <ArrowRight
                        size={14}
                        className="transition-transform duration-300 group-hover:translate-x-1"
                      />
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>

          {/* Bottom CTA */}
          <div className="mt-16 text-center ">
            <div className="glass-card inline-block p-8 max-w-2xl">
              <h2 className="text-2xl font-bold text-foreground mb-4">
                Not Sure Which Service You Need?
              </h2>
              <p className="text-muted-foreground mb-6">
                Our team of experts is here to help you find the perfect
                solution for your business needs. Get in touch for a free
                consultation.
              </p>

              <Link href="/contact">
                <Button className="flex items-center gap-2 mx-auto">
                  Get Free Consultation
                  <ArrowRight size={16} />
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
