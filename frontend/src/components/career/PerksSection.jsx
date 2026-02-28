
"use client"
import React, { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { 
  Heart, Calendar, Gift, DollarSign, Coffee, PartyPopper, 
  Coffee as CoffeeIcon, Users, Umbrella, Building, BookOpen, Award,
  Smile, Star, Shield, Zap, Globe, Clock, Battery, Cpu
} from "lucide-react";

// Icon mapping for API icon_name
const iconMap = {
  // Health icons
  "Heart": Heart,
  "Shield": Shield,
  "Activity": Zap,
  "Smile": Smile,
  
  // Work-life icons
  "Calendar": Calendar,
  "Clock": Clock,
  "Umbrella": Umbrella,
  "Coffee": Coffee,
  
  // Rewards icons
  "Gift": Gift,
  "Award": Award,
  "Star": Star,
  "DollarSign": DollarSign,
  
  // Culture icons
  "Users": Users,
  "Globe": Globe,
  "BookOpen": BookOpen,
  "Building": Building,
  
  // Default fallback
  "default": Gift
};

// Category colors mapping
const categoryColors = {
  health: "from-green-500 to-emerald-600",
  work_life: "from-blue-500 to-cyan-600",
  rewards: "from-yellow-500 to-orange-600",
  culture: "from-purple-500 to-pink-600",
  default: "from-purple-500 to-indigo-600"
};

const PerksSection = () => {
  const [perks, setPerks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [groupedPerks, setGroupedPerks] = useState({});

  useEffect(() => {
    fetchPerks();
  }, []);

  const fetchPerks = async () => {
    try {
      const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/perks`);
      const response = await res.json();
      
      // Extract data from the nested structure
      const perksData = response.data?.data || response.data || [];
      
      if (perksData.length > 0) {
        // Sort by display_order
        const sortedPerks = perksData.sort((a, b) => 
          (a.display_order || 0) - (b.display_order || 0)
        );
        
        setPerks(sortedPerks);
        
        // Group by category for potential category-based display
        const grouped = sortedPerks.reduce((acc, perk) => {
          const category = perk.category || 'other';
          if (!acc[category]) acc[category] = [];
          acc[category].push(perk);
          return acc;
        }, {});
        
        setGroupedPerks(grouped);
      } else {
        setPerks([]);
      }
    } catch (error) {
      console.error('Error fetching perks:', error);
      setPerks([]);
    } finally {
      setLoading(false);
    }
  };

  const scrollToOpenings = () => {
    const element = document.getElementById("current-openings");
    if (element) {
      element.scrollIntoView({ behavior: "smooth" });
    }
  };

  // Get appropriate icon component
  const getIconComponent = (iconName) => {
    const IconComponent = iconMap[iconName] || iconMap.default;
    return IconComponent;
  };

  // Get category color
  const getCategoryColor = (category) => {
    return categoryColors[category] || categoryColors.default;
  };

  if (loading) {
    return (
      <section className="py-16 lg:py-24 bg-[#0a0a0f]">
        <div className="container mx-auto px-4 lg:px-8 text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
        </div>
      </section>
    );
  }

  return (
    <section className="py-16 lg:py-24 bg-[#0a0a0f]">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="text-center mb-12">
          <h2 className="text-3xl md:text-4xl font-bold text-white mb-4 uppercase tracking-tight">
            PERKS & <span className="text-transparent animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">BENEFITS</span>
          </h2>

          <p className="text-gray-400 max-w-2xl mx-auto mb-8 font-medium">
            We care deeply about our team members and support their career growth.
            Here are some of the rewards and perks we offer to our employees.
          </p>

          <Button
            onClick={scrollToOpenings}
            className="bg-gradient-to-r from-purple-500 to-cyan-500 text-white rounded-lg px-8 py-6 shadow-lg hover:shadow-purple-500/20 transition-all duration-300 font-bold tracking-wider"
          >
            VIEW CURRENT OPENINGS
          </Button>
        </div>

        {perks.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 px-12">
            {perks.map((perk) => {
              const IconComponent = getIconComponent(perk.icon_name);
              const categoryColor = getCategoryColor(perk.category);
              
              return (
                <div
                  key={perk.id}
                  className="glass-card border border-white/10 rounded-2xl p-6 text-center
                  hover:bg-white/5 transition-all duration-300 group hover:scale-[1.02] hover:shadow-2xl hover:shadow-purple-500/10 relative overflow-hidden"
                >
                  {/* Animated gradient background on hover */}
                  <div className={`absolute inset-0 bg-gradient-to-br ${categoryColor} opacity-0 group-hover:opacity-10 transition-opacity duration-500`} />
                  
                  {/* Icon container */}
                  <div
                    className={`w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4
                    transition-all duration-500 group-hover:scale-110 group-hover:rotate-3
                    bg-gradient-to-br ${categoryColor} bg-opacity-20 border border-white/10 relative overflow-hidden`}
                  >
                    <IconComponent className="w-8 h-8 text-white transition-transform duration-500 group-hover:scale-110" strokeWidth={1.5} />
                    
                    {/* Glow effect */}
                    <div className={`absolute inset-0 bg-gradient-to-br ${categoryColor} opacity-0 group-hover:opacity-20 blur-xl transition-opacity duration-500`} />
                  </div>

                  {/* Title */}
                  <h3 className="text-sm font-semibold text-[#edebeb] leading-tight mb-2">
                    {perk.title}
                  </h3>

                  {/* Description (if available) */}
                  {perk.description && (
                    <p className="text-xs text-[#b5b0b0] mt-1 line-clamp-2">
                      {perk.description}
                    </p>
                  )}

                  {/* Optional category badge (can be removed if not needed) */}
                  {perk.category && (
                    <div className="mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                      <span className={`text-[10px] px-2 py-0.5 rounded-full bg-gradient-to-r ${categoryColor} text-white`}>
                        {perk.category.replace('_', ' ')}
                      </span>
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        ) : (
          <div className="text-center text-[#b5b0b0] py-12">
            No perks available at the moment.
          </div>
        )}

        {/* Optional: Category-wise grouping (if you want to show sections) */}
        {Object.keys(groupedPerks).length > 1 && (
          <div className="mt-12 space-y-8 px-10">
            {Object.entries(groupedPerks).map(([category, categoryPerks]) => (
              <div key={category} className="space-y-4">
                <h3 className="text-xl font-semibold text-white capitalize border-l-4 border-purple-600 pl-3">
                  {category.replace('_', ' ')} Benefits
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 px-12">
                  {categoryPerks.map((perk) => {
                    const IconComponent = getIconComponent(perk.icon_name);
                    const categoryColor = getCategoryColor(perk.category);
                    
                    return (
                      <div
                        key={perk.id}
                        className="glass-card border border-white/10 rounded-2xl p-6 text-center
                        hover:bg-white/5 transition-all duration-300 group hover:scale-[1.02] hover:shadow-2xl hover:shadow-purple-500/10"
                      >
                        <div
                          className={`w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4
                          transition-all duration-300 group-hover:scale-110
                          bg-gradient-to-br ${categoryColor} bg-opacity-20 border border-white/10`}
                        >
                          <IconComponent className="w-8 h-8 text-white" strokeWidth={1.5} />
                        </div>

                        <h3 className="text-sm font-semibold text-[#edebeb] leading-tight mb-2">
                          {perk.title}
                        </h3>

                        {perk.description && (
                          <p className="text-xs text-[#b5b0b0] line-clamp-2">
                            {perk.description}
                          </p>
                        )}
                      </div>
                    );
                  })}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

export default PerksSection;