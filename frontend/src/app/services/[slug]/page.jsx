

'use client';

import { useState, useEffect } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { useParams, notFound } from 'next/navigation';
import { CheckCircle } from 'lucide-react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import bestcms from '@/images/bestcms.png';

export default function ServicePage() {
  const params = useParams();
  const slug = params.slug;
  
  const [service, setService] = useState(null);
  const [features, setFeatures] = useState([]);
  const [processSteps, setProcessSteps] = useState([]);
  const [commitments, setCommitments] = useState([]);
  const [statistics, setStatistics] = useState([]);
  const [testimonials, setTestimonials] = useState([]);
  const [whyChooseUs, setWhyChooseUs] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (slug) {
      fetchServiceData();
    }
  }, [slug]);

  const fetchServiceData = async () => {
    setLoading(true);
    try {
      // Fetch main service data
      const serviceResponse = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/services/${slug}`);
      const serviceData = await serviceResponse.json();
      
      if (!serviceData.success) {
        setError('Service not found');
        setLoading(false);
        return;
      }

      setService(serviceData.data);

      // Parallel fetch all related data
     const [
  featuresRes,
  processRes,
  commitmentsRes,
  statsRes,
  testimonialsRes,
  whyChooseUsRes,
] = await Promise.all([
  fetch(`${process.env.NEXT_PUBLIC_API_URL}/service-features`),
  fetch(`${process.env.NEXT_PUBLIC_API_URL}/process-steps`),
  fetch(`${process.env.NEXT_PUBLIC_API_URL}/commitments`),
  fetch(`${process.env.NEXT_PUBLIC_API_URL}/statistics`),
  fetch(`${process.env.NEXT_PUBLIC_API_URL}/testimonials`),
  fetch(`${process.env.NEXT_PUBLIC_API_URL}/why-choose-us-points`),
]);

    let [
  featuresData,
  processData,
  commitmentsData,
  statsData,
  testimonialsData,
  whyChooseUsData,
] = await Promise.all([
  featuresRes.json(),
  processRes.json(),
  commitmentsRes.json(),
  statsRes.json(),
  testimonialsRes.json(),
  whyChooseUsRes.json(),
]);
      featuresData=featuresData.data.data;
      processData=processData.data.data;
      commitmentsData=commitmentsData.data.data;
      statsData=statsData.data.data;
      testimonialsData=testimonialsData.data.data;
      whyChooseUsData=whyChooseUsData.data.data;
      // Filter data by service_id if available
      if (featuresData) {
        setFeatures(featuresData.filter(f => f.service_id === serviceData.data.id));
      }
      
      if (processData) {
        setProcessSteps(processData.filter(p => p.service_id === serviceData.data.id));
      }
      
      if (commitmentsData) {
        setCommitments(commitmentsData.filter(c => c.service_id === serviceData.data.id));
      }
      
      if (statsData) {
        setStatistics(statsData.filter(s => s.service_id === serviceData.data.id));
      }

     if (testimonialsData) {
  setTestimonials(
    testimonialsData
      .filter(t => t.is_active === true)
      .sort((a, b) => a.display_order - b.display_order)
  );
}
      
      if (whyChooseUsData) {
        setWhyChooseUs(whyChooseUsData.filter(w => w.service_id === serviceData.data.id));
      }

    } catch (err) {
      console.error('Error fetching service data:', err);
      setError('Failed to load service details');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#0a0a0f] flex items-center justify-center">
        <div className="w-12 h-12 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin"></div>
      </div>
    );
  }

  if (error || !service) {
    notFound();
    return null;
  }

  // Use API data or fallback to defaults if no data
  const displayFeatures = features.length > 0 ? features : [
    { title: `${service.title} Development`, description: `Professional ${service.title.toLowerCase()} development services` },
    { title: 'Expert Team', description: 'Skilled developers with years of experience' },
    { title: 'Quality Assurance', description: 'Rigorous testing for bug-free delivery' },
    { title: 'Timely Delivery', description: 'On-time project completion guaranteed' }
  ];

  const displayProcess = processSteps.length > 0 ? processSteps : [
    { title: 'Requirement Analysis', description: 'Understanding your project needs' },
    { title: 'Planning', description: 'Creating detailed project roadmap' },
    { title: 'Development', description: 'Building your solution' },
    { title: 'Testing & Deployment', description: 'Quality check and launch' }
  ];

  const displayCommitments = commitments.length > 0 ? commitments : [
    '100% Transparency',
    '95% On-time Delivery',
    'Free 30 Days Support',
    'Flexible Engagements',
    '24x7 Support'
  ];

  const displayStatistics = statistics.length > 0 ? statistics : [
    { label: 'Years of Business', value: '0+' },
    { label: 'IT Professionals', value: '5+' },
    { label: 'Clients Worldwide', value: '0+' },
    { label: 'Projects Executed', value: '1+' }
  ];

const displayTestimonials =
  testimonials.length > 0
    ? testimonials
    : [
        {
          client_name: "John Smith",
          client_position: "CEO",
          client_company: "TechStart Inc.",
          testimonial_text:
            "Outstanding experience and professional support.",
          rating: 5,
          client_image: null
        }
      ];

  const displayWhyChooseUs = whyChooseUs.length > 0 ? whyChooseUs : [
    'Assured satisfaction through product lifecycle',
    '100 percent client satisfaction guarantee',
    'Experienced development team',
    '100% data security',
    'Dedicated support'
  ];

  return (
    <div className="min-h-screen bg-[#0a0a0f]">
      <Navbar/>

      {/* Hero Section */}
      <section className="relative min-h-[70vh] flex items-center justify-center overflow-hidden pt-32 pb-20">
        <div
          className="absolute inset-0 z-0"
          style={{
            backgroundImage: `url(${bestcms.src})`,
            backgroundSize: "cover",
            backgroundPosition: "center",
          }}
        >
          <div className="absolute inset-0 bg-[#0a0a16]/80 backdrop-blur-[2px]" />
        </div>
        
        <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 className="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 tracking-tight uppercase">
            <span className="text-transparent animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">
              {service.title}
            </span>
          </h1>
          <p className="text-lg md:text-xl max-w-3xl mx-auto mb-10 text-gray-300 font-medium">
            {service.short_description}
          </p>
          <div className="flex flex-col sm:flex-row justify-center gap-4 sm:gap-6">
            <button className="px-8 py-4 rounded-xl bg-gradient-to-r from-[#9333ea] to-[#a855f7] text-white font-semibold text-lg hover:shadow-[0_0_20px_rgba(147,51,234,0.5)] transition-all duration-300">
              Get Started Now
            </button>
            <button className="px-8 py-4 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md text-white font-semibold text-lg hover:bg-white/10 transition-all duration-300">
              Learn More
            </button>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-20 relative overflow-hidden">
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-px bg-gradient-to-r from-transparent via-white/10 to-transparent" />
        <div className="container mx-auto px-12">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff] uppercase tracking-wider">
              Innovation & Excellence
            </h2>
            <div className="w-24 h-1 bg-gradient-to-r from-purple-500 to-cyan-500 mx-auto mb-8 rounded-full" />
            <p className="text-gray-400 max-w-3xl mx-auto text-lg leading-relaxed">
              We are the top <span className="text-white font-semibold">{service.title.toLowerCase()}</span> developers with a full life cycle app development approach 
              that provides solutions from concept and design to development, deployment, and maintenance.
            </p>
          </div>
          
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8 mb-16">
            {displayStatistics.map((stat, index) => (
              <div key={index} className="bg-[#1a1a2e]/40 backdrop-blur-xl border border-white/5 p-8 rounded-3xl text-center group hover:bg-[#1a1a2e]/60 transition-all duration-300 hover:border-purple-500/30">
                <div className="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 mb-2 group-hover:scale-110 transition-transform duration-300">
                  {stat.value}
                </div>
                <div className="text-sm font-medium text-gray-400 uppercase tracking-widest">{stat.label}</div>
              </div>
            ))}
          </div>
          
          <div className="text-center">
            <button className="px-10 py-4 rounded-xl bg-gradient-to-r from-purple-500 to-cyan-500 text-white font-bold text-lg hover:shadow-[0_0_30px_rgba(147,51,234,0.3)] transition-all duration-300">
              Request A Quote
            </button>
          </div>
        </div>
      </section>
      
{/* Testimonials Section */}
<section className="py-20 bg-white/[0.02]">
  <div className="container mx-auto px-12">
    <div className="text-center mb-16">
      <h2 className="text-3xl md:text-5xl font-bold text-white mb-6">
        Client <span className="bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text text-transparent">Testimonials</span>
      </h2>
      <p className="text-gray-400 max-w-2xl mx-auto">
        What our clients say about Shakuniya Solutions
      </p>
    </div>

    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      {displayTestimonials.map((item, index) => (
        <div
          key={index}
          className="bg-[#1a1a2e]/60 backdrop-blur-xl border border-white/10 p-8 rounded-[2rem] hover:border-[#00d9ff]/40 transition-all duration-300 hover:shadow-[0_0_30px_rgba(0,217,255,0.15)]"
        >
          {/* Rating Stars */}
          <div className="flex mb-4">
            {[...Array(item.rating || 5)].map((_, i) => (
              <span key={i} className="text-yellow-400 text-lg">★</span>
            ))}
          </div>

          {/* Testimonial Text */}
          <p className="text-gray-300 mb-6 italic leading-relaxed">
            "{item.testimonial_text}"
          </p>

          {/* Client Info */}
          <div className="flex items-center gap-4">
            <div className="w-14 h-14 rounded-full overflow-hidden border-2 border-purple-500">
              {item.client_image ? (
                <img
                  src={item.client_image}
                  alt={item.client_name}
                  className="w-full h-full object-cover"
                />
              ) : (
                <div className="w-full h-full flex items-center justify-center bg-gradient-to-r from-purple-500 to-cyan-500 text-white font-bold text-lg">
                  {item.client_name?.charAt(0)}
                </div>
              )}
            </div>

            <div>
              <h4 className="text-white font-semibold text-lg">
                {item.client_name}
              </h4>
              <p className="text-gray-400 text-sm">
                {item.client_position} | {item.client_company}
              </p>
            </div>
          </div>

          {/* Company Tagline */}
          {item.company?.tagline && (
            <p className="text-xs text-gray-500 mt-4 italic border-t border-white/10 pt-3">
              {item.company.tagline}
            </p>
          )}
        </div>
      ))}
    </div>
  </div>
</section>

      {/* Features Section */}
      <section className="py-20 bg-white/[0.02]">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <span className="text-purple-400 uppercase tracking-[0.3em] text-sm font-bold mb-4 block">SERVICES</span>
            <h2 className="text-3xl md:text-5xl font-bold text-white mb-6">
              Our <span className="bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text text-transparent">{service.title}</span> Services
            </h2>
          </div>
          
          <div className="grid md:grid-cols-2 gap-8 lg:gap-10">
            {displayFeatures.map((feature, index) => (
              <div key={index} className="group bg-[#1a1a2e]/60 backdrop-blur-xl border border-white/10 p-8 rounded-[2rem] hover:bg-[#1a1a2e]/80 transition-all duration-300 hover:border-[#00d9ff]/50 hover:shadow-[0_0_30px_rgba(0,217,255,0.15)]">
                <h3 className="text-2xl font-bold mb-4 text-white group-hover:text-[#00d9ff] transition-colors duration-300">
                  {feature.title}
                </h3>
                <p className="text-gray-400 text-lg leading-relaxed group-hover:text-gray-300 transition-colors">
                  {feature.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Process Section */}
      <section className="py-20">
        <div className="container mx-auto px-12">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-5xl font-bold text-white mb-6">
              Development <span className="bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">Process</span>
            </h2>
            <p className="text-gray-400 max-w-2xl mx-auto">
              Our structured approach ensures efficiency and excellence in every phase of development.
            </p>
          </div>
          
          <div className="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {displayProcess.map((step, index) => (
              <div key={index} className="group relative bg-[#1a1a2e]/40 backdrop-blur-xl border border-white/5 p-8 rounded-3xl hover:bg-[#1a1a2e]/60 transition-all duration-300 hover:border-purple-500/50">
                <div className="absolute -top-4 -left-4 w-10 h-10 bg-gradient-to-br from-purple-500 to-cyan-500 rounded-full flex items-center justify-center text-xl font-bold text-white shadow-lg group-hover:scale-110 transition-transform">
                  {index + 1}
                </div>
                <h3 className="text-xl font-bold mb-3 mt-4 text-white group-hover:text-[#00d9ff] transition-colors">
                  {step.title || step.name}
                </h3>
                <p className="text-gray-400 leading-relaxed text-sm">
                  {step.description || step.details}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Commitment Section */}
      <section className="py-20 bg-gradient-to-b from-transparent via-purple-900/10 to-transparent">
        <div className="container mx-auto px-16">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-6">
              Our Commitment & <span className="bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text text-transparent">Guaranty</span>
            </h2>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6 lg:gap-8 mb-16">
            {displayCommitments.map((item, index) => (
              <div key={index} className="text-center group p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all duration-300">
                <div className="w-14 h-14 rounded-full bg-gradient-to-br from-green-400/20 to-emerald-400/20 flex items-center justify-center mx-auto mb-4 border border-green-500/20 group-hover:scale-110 transition-transform">
                  <CheckCircle className="h-8 w-8 text-green-400" />
                </div>
                <div className="text-sm font-semibold text-gray-200 uppercase tracking-wider">
                  {typeof item === 'string' ? item : item.title || item.name}
                </div>
                  <p className="text-gray-400">
                  {item.description}
                </p>
              </div>
            ))}
          </div>
          
          <div className="max-w-4xl mx-auto p-8 rounded-3xl bg-[#1a1a2e]/40 border border-white/10 text-center">
            <p className="text-gray-300 text-lg leading-relaxed italic">
              "We believe consistent transparency with our clients, we have been providing excellent technical 
              support since inception. Our commitment to excellence is reflected in our dedication to delivering 
              high-quality results."
            </p>
          </div>
        </div>
      </section>

      {/* Why Choose Us Section */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="bg-[#1a1a2e]/60 backdrop-blur-xl border border-white/10 rounded-[3rem] p-8 md:p-16 overflow-hidden relative">
            <div className="absolute top-0 right-0 w-96 h-96 bg-purple-600/10 blur-[100px] -z-10 rounded-full" />
            <div className="absolute bottom-0 left-0 w-96 h-96 bg-cyan-600/10 blur-[100px] -z-10 rounded-full" />
            
            <h2 className="text-3xl md:text-4xl font-bold text-center text-white mb-12">
              Why Choose <span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">Shakuniya Solutions?</span>
            </h2>
            
            <div className="grid md:grid-cols-2 gap-6 lg:gap-8">
              {displayWhyChooseUs.map((item, index) => (
                <div key={index} className="flex items-start space-x-4 p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-purple-500/30 transition-all duration-300 group">
                  <div className="mt-1 flex-shrink-0">
                    <CheckCircle className="h-6 w-6 text-cyan-400 group-hover:scale-110 transition-transform" />
                  </div>
                  <p className="text-gray-300 text-lg group-hover:text-white transition-colors">
                    {typeof item === 'string' ? item : item.point_text || item.description}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>
      
      <Footer/>
    </div>
  );
}
