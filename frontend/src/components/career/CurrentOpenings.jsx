"use client"
import { MapPin, Briefcase, GraduationCap, Users, Clock, DollarSign, Calendar, Award, ChevronDown, ChevronUp } from "lucide-react";
import { Button } from "@/components/ui/button";
import { useState, useEffect } from "react";

// Employment type badges
const employmentTypeColors = {
  "full-time": "bg-green-500/20 text-green-400",
  "part-time": "bg-blue-500/20 text-blue-400",
  "contract": "bg-yellow-500/20 text-yellow-400",
  "internship": "bg-purple-500/20 text-purple-400",
  "freelance": "bg-orange-500/20 text-orange-400"
};

// Work type badges
const workTypeColors = {
  "remote": "bg-cyan-500/20 text-cyan-400",
  "hybrid": "bg-indigo-500/20 text-indigo-400",
  "onsite": "bg-gray-500/20 text-gray-400"
};

const CurrentOpenings = () => {
  const [jobs, setJobs] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedCategory, setSelectedCategory] = useState("all");
  const [expandedJob, setExpandedJob] = useState(null);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      
      // Direct fetch calls
      const jobsRes = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/jobs`);
      const categoriesRes = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/job-categories`);
      
      const jobsData = await jobsRes.json();
      const categoriesData = await categoriesRes.json();

      // Handle nested data structure
      const jobsList = jobsData.data?.data || jobsData.data || jobsData || [];
      const categoriesList = categoriesData.data?.data || categoriesData.data || categoriesData || [];

        console.log('Jobs fetched:', jobsList); // Debug log
      console.log('Categories fetched:', categoriesList); // Debug log
      
      setJobs(jobsList);
      setCategories(categoriesList);
      
    } catch (error) {
      console.error('Error fetching jobs:', error);
      setJobs([]);
    } finally {
      setLoading(false);
    }
  };

  const filteredJobs = selectedCategory === "all" 
    ? jobs 
    : jobs.filter(job => job.department_id === selectedCategory);

  const getExperienceText = (job) => {
    if (job.experience_min && job.experience_max) {
      return `${job.experience_min}-${job.experience_max} years`;
    } else if (job.experience_min) {
      return `${job.experience_min}+ years`;
    } else if (job.experience_max) {
      return `Up to ${job.experience_max} years`;
    }
    return "Not specified";
  };

  const scrollToForm = (jobTitle, jobId) => {
    console.log('Applying for job:', jobTitle, jobId); // Debug log
    
    // Store selected job in localStorage (more persistent than sessionStorage)
    localStorage.setItem('selectedJob', jobTitle);
    localStorage.setItem('selectedJobId', jobId.toString());
    
    // Also store in sessionStorage as backup
    sessionStorage.setItem('selectedJob', jobTitle);
    sessionStorage.setItem('selectedJobId', jobId.toString());
    
    // Dispatch a custom event so ApplyForm can listen
    const event = new CustomEvent('jobSelected', { 
      detail: { title: jobTitle, id: jobId } 
    });
    window.dispatchEvent(event);
    
    // Scroll to form with a small delay to ensure it exists
    setTimeout(() => {
      const element = document.getElementById("apply-form");
      if (element) {
        element.scrollIntoView({ 
          behavior: "smooth",
          block: "start"
        });
      } else {
        console.log('Apply form element not found');
      }
    }, 100);
  };

  const toggleExpand = (jobId) => {
    setExpandedJob(expandedJob === jobId ? null : jobId);
  };

  if (loading) {
    return (
      <section className="py-16 lg:py-24 bg-[#0a0a0f]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
          </div>
        </div>
      </section>
    );
  }

  return (
    <section id="current-openings" className="py-16 lg:py-24 bg-[#0a0a0f]">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="text-center mb-10">
          <h2 className="text-3xl md:text-4xl font-bold text-white mb-8 uppercase tracking-tight">
            CURRENT <span className="text-transparent animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">OPENINGS</span>
          </h2>

          {/* Category Filters */}
          {categories.length > 0 && (
            <div className="flex flex-wrap justify-center gap-3 mb-8">
              <button
                onClick={() => setSelectedCategory("all")}
                className={`px-6 py-2 rounded-full text-sm font-bold tracking-wider transition-all duration-300 ${
                  selectedCategory === "all"
                    ? "bg-gradient-to-r from-purple-500 to-cyan-500 text-white shadow-lg"
                    : "bg-white/5 text-gray-400 hover:bg-white/10"
                }`}
              >
                ALL
              </button>
              {categories.map((cat) => (
                <button
                  key={cat.id}
                  onClick={() => setSelectedCategory(cat.id)}
                  className={`px-6 py-2 rounded-full text-sm font-bold tracking-wider transition-all duration-300 ${
                    selectedCategory === cat.id
                      ? "bg-gradient-to-r from-purple-500 to-cyan-500 text-white shadow-lg"
                      : "bg-white/5 text-gray-400 hover:bg-white/10"
                  }`}
                >
                  {cat.name.toUpperCase()}
                </button>
              ))}
            </div>
          )}
        </div>

        <div className="max-w-4xl mx-auto divide-y-4 divide-white/5 bg-[#1a1a2e]/40 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-white/10 overflow-hidden">
          {filteredJobs.length > 0 ? (
            filteredJobs.map((job) => (
              <div
                key={job.id}
                className="p-6 md:p-8 hover:bg-white/5 transition-all group"
              >
                {/* Main Job Info */}
                <div className="flex flex-col lg:flex-row lg:justify-between gap-6">
                  <div className="flex-1">
                    {/* Title with badges */}
                    <div className="flex flex-wrap items-center gap-3 mb-3">
                      <h3 className="text-xl font-semibold text-white border-l-4 border-purple-500 pl-3 group-hover:border-cyan-500 transition-colors">
                        {job.title}
                      </h3>
                      
                      {/* Employment Type Badge */}
                      {job.employment_type && (
                        <span className={`text-[10px] uppercase tracking-widest font-bold px-2 py-1 rounded-full ${employmentTypeColors[job.employment_type] || 'bg-gray-500/20 text-gray-400'}`}>
                          {job.employment_type.replace('-', ' ')}
                        </span>
                      )}
                      
                      {/* Work Type Badge */}
                      {job.work_type && (
                        <span className={`text-[10px] uppercase tracking-widest font-bold px-2 py-1 rounded-full ${workTypeColors[job.work_type] || 'bg-gray-500/20 text-gray-400'}`}>
                          {job.work_type}
                        </span>
                      )}
                    </div>

                    {/* Description */}
                    <p className="text-gray-400 text-sm mb-4 leading-relaxed font-medium">
                      {job.description || job.short_description}
                    </p>

                    {/* Key Info Grid */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                      <Info 
                        icon={Briefcase} 
                        label="Experience" 
                        value={getExperienceText(job)} 
                      />
                      
                      <Info 
                        icon={Users} 
                        label="Positions" 
                        value={job.positions_available ? `${job.positions_available} ${job.positions_available > 1 ? 'Openings' : 'Opening'}` : "Multiple"} 
                      />
                      
                      {job.qualification && (
                        <Info
                          icon={GraduationCap}
                          label="Qualification"
                          value={job.qualification}
                        />
                      )}
                      
                      <Info 
                        icon={MapPin} 
                        label="Location" 
                        value={job.location} 
                      />
                      
                      {job.salary_range && (
                        <Info 
                          icon={DollarSign} 
                          label="Salary" 
                          value={job.salary_range} 
                        />
                      )}
                      
                      {job.employment_type && (
                        <Info 
                          icon={Calendar} 
                          label="Job Type" 
                          value={job.employment_type.replace('-', ' ')} 
                        />
                      )}
                    </div>

                    {/* Expand/Collapse Button for more details */}
                    {(job.responsibilities?.length > 0 || job.requirements?.length > 0 || job.benefits?.length > 0) && (
                      <button
                        onClick={() => toggleExpand(job.id)}
                        className="flex items-center gap-2 text-sm text-purple-400 hover:text-purple-300 mt-4 transition-colors"
                      >
                        {expandedJob === job.id ? (
                          <>Show less <ChevronUp className="w-4 h-4" /></>
                        ) : (
                          <>View full details <ChevronDown className="w-4 h-4" /></>
                        )}
                      </button>
                    )}
                  </div>

                  <div className="flex items-start">
                    <Button
                      onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        scrollToForm(job.title, job.id);
                      }}
                      className="bg-gradient-to-r from-purple-500 to-cyan-500 text-white px-8 py-2 rounded-lg shadow-lg hover:shadow-purple-500/20 transition-all duration-300 font-bold tracking-wider whitespace-nowrap hover:scale-105"
                    >
                      APPLY NOW
                    </Button>
                  </div>
                </div>

                {/* Expanded Details Section */}
                {expandedJob === job.id && (
                  <div className="mt-6 pt-6 border-t border-white/10 space-y-4">
                    
                    {/* Responsibilities */}
                    {job.responsibilities?.length > 0 && (
                      <div>
                        <h4 className="text-white font-semibold mb-2 flex items-center gap-2">
                          <Award className="w-4 h-4 text-purple-400" />
                          Responsibilities
                        </h4>
                        <ul className="grid grid-cols-1 md:grid-cols-2 gap-2">
                          {job.responsibilities.map((resp, idx) => (
                            <li key={idx} className="text-sm text-[#b5b0b0] flex items-start gap-2">
                              <span className="text-purple-400 mt-1">•</span>
                              {resp}
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}

                    {/* Requirements */}
                    {job.requirements?.length > 0 && (
                      <div>
                        <h4 className="text-white font-semibold mb-2 flex items-center gap-2">
                          <Briefcase className="w-4 h-4 text-purple-400" />
                          Requirements
                        </h4>
                        <ul className="grid grid-cols-1 md:grid-cols-2 gap-2">
                          {job.requirements.map((req, idx) => (
                            <li key={idx} className="text-sm text-[#b5b0b0] flex items-start gap-2">
                              <span className="text-purple-400 mt-1">•</span>
                              {req}
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}

                    {/* Benefits */}
                    {job.benefits?.length > 0 && (
                      <div>
                        <h4 className="text-white font-semibold mb-2 flex items-center gap-2">
                          <Award className="w-4 h-4 text-purple-400" />
                          Benefits
                        </h4>
                        <div className="flex flex-wrap gap-2">
                          {job.benefits.map((benefit, idx) => (
                            <span
                              key={idx}
                              className="text-xs px-3 py-1 bg-white/5 text-[#b5b0b0] rounded-full"
                            >
                              {benefit}
                            </span>
                          ))}
                        </div>
                      </div>
                    )}
                  </div>
                )}
              </div>
            ))
          ) : (
            <div className="p-8 text-center text-[#b5b0b0]">
              No openings available in this category.
            </div>
          )}
        </div>
      </div>
    </section>
  );
};

const Info = ({ icon: Icon, label, value }) => (
  <div className="flex items-start gap-2">
    <Icon className="w-4 h-4 text-purple-400 mt-0.5 flex-shrink-0" />
    <span className="font-medium text-[#edebeb]">{label}:</span>
    <span className="text-[#b5b0b0]">{value}</span>
  </div>
);

export default CurrentOpenings;