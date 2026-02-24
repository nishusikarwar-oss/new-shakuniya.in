"use client";

import { useState } from "react";
import Link from "next/link";
import { ChevronDown, ArrowRight } from "lucide-react";
import { getDropdownServices } from "@/data/services";

const ServicesDropdown = () => {
  const [isOpen, setIsOpen] = useState(false);
  const dropdownServices = getDropdownServices(4);

  return (
    <div
      className="relative"
      onMouseEnter={() => setIsOpen(true)}
      onMouseLeave={() => setIsOpen(false)}
    >
      {/* Trigger */}
      <button
        type="button"
        className="flex items-center gap-1 text-muted-foreground hover:text-foreground transition-colors duration-300 text-sm font-medium"
      >
        Services
        <ChevronDown
          size={14}
          className={`transition-transform duration-300 ${
            isOpen ? "rotate-180" : ""
          }`}
        />
      </button>

      {/* Dropdown Menu */}
      <div
        className={`absolute top-full left-1/2 -translate-x-1/2 pt-4 transition-all duration-300 ease-out ${
          isOpen
            ? "opacity-100 translate-y-0 pointer-events-auto"
            : "opacity-0 -translate-y-2 pointer-events-none"
        }`}
      >
        <div className="glass-card p-2 min-w-[280px] shadow-2xl shadow-[#00d9ff]/10 relative">
          {/* Arrow indicator */}
          <div className="absolute -top-[6px] left-1/2 -translate-x-1/2 w-3 h-3 rotate-45 bg-gradient-to-br from-white/10 to-white/5 border-l border-t border-white/10" />

          <div className="space-y-1">
            {/* Top 4 Services */}
            {dropdownServices.map((service) => {
              const IconComponent = service.icon;

              return (
                <Link
                  key={service.id}
                  href={`/services/${service.slug}`}
                  className="flex items-center gap-3 px-4 py-3 rounded-xl border border-transparent hover:bg-white/5 hover:border-[#00d9ff]/20 transition-all duration-200 group"
                >
                  <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500/20 to-cyan-500/20 flex items-center justify-center group-hover:from-purple-500/30 group-hover:to-cyan-500/30 transition-all duration-200">
                    {IconComponent && (
                      <IconComponent
                        size={20}
                        className="text-purple-400 group-hover:text-purple-300 transition-colors"
                      />
                    )}
                  </div>

                  <div>
                    <span className="block text-sm font-medium text-foreground group-hover:text-white transition-colors">
                      {service.title}
                    </span>
                    <span className="block text-xs text-muted-foreground">
                      {service.shortDescription}
                    </span>
                  </div>
                </Link>
              );
            })}

            {/* View More Services */}
            <Link
              href="/services"
              className="flex items-center justify-between px-4 py-3 mt-2 rounded-xl bg-gradient-to-r from-purple-500/10 to-cyan-500/10 hover:from-purple-500/20 hover:to-cyan-500/20 transition-all duration-200 group border-t border-white/5"
            >
              <span className="text-sm font-medium text-primary group-hover:text-white transition-colors">
                View More Services
              </span>
              <ArrowRight
                size={16}
                className="text-primary group-hover:text-white transition-all duration-300 group-hover:translate-x-1"
              />
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ServicesDropdown;
