'use client';

import Link from 'next/link';
import { ArrowLeft, ArrowRight } from 'lucide-react';
import { useState, useEffect } from 'react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';

export default function ServicesPage() {
  const [services, setServices] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchServices();
  }, []);

  const fetchServices = async () => {
    try {
      const response = await fetch('http://127.0.0.1:8000/api/services');
      const data = await response.json();
      
      if (data.success) {
        setServices(data.data);
      } else {
        setError('Failed to load services');
      }
    } catch (err) {
      setError('Network error');
    } finally {
      setLoading(false);
    }
  };

  // Icon mapping based on service type or title
  const getServiceIcon = (title) => {
    const iconMap = {
      'android': '📱',
      'ios': '🍎',
      'website': '🌐',
      'software': '💻',
      'social': '📱',
      'live': '🎥',
      'shopping': '🛍️',
      'audio': '🎵'
    };
    
    const lowerTitle = title.toLowerCase();
    for (const [key, icon] of Object.entries(iconMap)) {
      if (lowerTitle.includes(key)) return icon;
    }
    return '🚀'; // default icon
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#0a0a0f] flex items-center justify-center">
        <div className="w-12 h-12 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-[#0a0a0f] flex items-center justify-center">
        <div className="text-center text-red-500 p-8">
          <p className="text-xl mb-4 font-bold">{error}</p>
          <button 
            onClick={fetchServices}
            className="px-6 py-2 bg-gradient-to-r from-purple-500 to-cyan-500 text-white rounded-lg hover:shadow-lg transition-all duration-300"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#0a0a0f]">
      <Navbar />
      
      {/* Hero Section */}
      <section className="relative pt-32 pb-20 overflow-hidden">
        
        <div className="container mx-auto px-4 relative z-10 text-center">
          <span className="text-purple-400 uppercase tracking-[0.3em] text-sm font-bold mb-4 block animate-fade-in">
            OUR EXPERTISE
          </span>
          <h1 className="text-5xl md:text-7xl font-bold mb-6 text-white tracking-tight uppercase">
            Our <span className="text-transparent animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">Services</span>
          </h1>
          <p className="text-gray-300 text-lg md:text-xl max-w-3xl mx-auto font-medium">
            Transforming ideas into powerful digital solutions with cutting-edge technology and innovation
          </p>
        </div>
      </section>

      {/* Services Grid Section */}
      <section className="py-20 relative">
        <div className="container mx-auto px-16">
          <div className="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            {services.data.map((service) => (
              <Link
                key={service.id}
                href={`/services/${service.slug}`}
                className="group block h-full"
              >
                {/* Card */}
                <div className="bg-[#1a1a2e]/60 backdrop-blur-xl border border-white/10 p-8 rounded-[2rem] 
                              hover:bg-[#1a1a2e]/80 transition-all duration-500 
                              hover:border-[#00d9ff]/50 hover:shadow-[0_0_40px_rgba(0,217,255,0.15)]
                              hover:-translate-y-2 h-full flex flex-col">
                  
                  {/* Icon */}
                  <div className="w-16 h-16 bg-gradient-to-br from-purple-500/20 to-cyan-500/20 border border-white/10 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-gradient-to-br group-hover:from-purple-500 group-hover:to-cyan-500 transition-all duration-500 shadow-lg">
                    {service.icon || getServiceIcon(service.title)}
                  </div>
                  
                  {/* Title */}
                  <h2 className="text-2xl font-bold text-white mb-4 group-hover:text-[#00d9ff] transition-colors duration-300">
                    {service.title}
                  </h2>
                  
                  {/* Description */}
                  <p className="text-gray-400 text-base mb-6 line-clamp-3 leading-relaxed group-hover:text-gray-300 transition-colors">
                    {service.short_description || service.description}
                  </p>
                  
                  {/* Features List */}
                  <div className="space-y-3 mb-8 flex-grow">
                    {(service.features || []).slice(0, 3).map((feature, index) => (
                      <div key={index} className="flex items-center text-gray-500 text-sm group-hover:text-gray-400 transition-colors">
                        <span className="w-1.5 h-1.5 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full mr-3 shadow-[0_0_5px_rgba(147,51,234,0.5)]"></span>
                        {typeof feature === 'string' ? feature : feature.title || feature.name}
                      </div>
                    ))}
                  </div>
                  
                  {/* View Service Link */}
                  <div className="inline-flex items-center text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 group-hover:from-white group-hover:to-white transition-all duration-300 mt-auto pt-4 border-t border-white/5 uppercase tracking-widest">
                    Explore Details
                    <ArrowRight className="h-4 w-4 ml-2 group-hover:translate-x-2 transition-transform" />
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
      
      <Footer />
    </div>
  );
}

