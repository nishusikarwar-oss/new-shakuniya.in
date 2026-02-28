
"use client"
import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Upload } from "lucide-react";

const ApplyForm = () => {
  const [jobs, setJobs] = useState([]);
  const [settings, setSettings] = useState(null);
  const [loading, setLoading] = useState(true);
  const [settingsLoading, setSettingsLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [submitMessage, setSubmitMessage] = useState('');

  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    phone: "",
    job_id: "",
    message: "",
    gender: "",
    cv: null,
  });

  useEffect(() => {
    fetchJobs();
    fetchSettings();
    
    // Check localStorage on mount
    const savedJobId = localStorage.getItem('selectedJobId');
    const savedJob = localStorage.getItem('selectedJob');
    
    if (savedJobId && savedJob) {
      console.log('Found saved job:', savedJob);
      setFormData(prev => ({ ...prev, job_id: savedJobId }));
      
      // Clear after use
      localStorage.removeItem('selectedJobId');
      localStorage.removeItem('selectedJob');
    }
    
    // Listen for custom event
    const handleJobSelected = (event) => {
      console.log('Job selected:', event.detail);
      setFormData(prev => ({ 
        ...prev, 
        job_id: event.detail.id.toString() 
      }));
    };
    
    window.addEventListener('jobSelected', handleJobSelected);
    
    return () => {
      window.removeEventListener('jobSelected', handleJobSelected);
    };
  }, []);

  const fetchJobs = async () => {
    try {
      const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/jobs`);
      const data = await res.json();
      const jobsList = data.data?.data || data.data || data || [];
      setJobs(jobsList);
    } catch (error) {
      console.error('Error fetching jobs:', error);
      setJobs([]);
    } finally {
      setLoading(false);
    }
  };

  const fetchSettings = async () => {
    try {
      const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/career-settings`);
      const response = await res.json();
      const settingsData = response.data || response || {};
      setSettings(settingsData);
    } catch (error) {
      console.error('Error fetching settings:', error);
    } finally {
      setSettingsLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleFileChange = (e) => {
    if (e.target.files && e.target.files[0]) {
      setFormData((prev) => ({ ...prev, cv: e.target.files[0] }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    setSubmitMessage('');

    try {
      const formDataToSend = new FormData();
      formDataToSend.append('name', formData.fullName);
      formDataToSend.append('email', formData.email);
      formDataToSend.append('phone', formData.phone);
      formDataToSend.append('job_id', formData.job_id);
      formDataToSend.append('message', formData.message || '');
      formDataToSend.append('gender', formData.gender);
      
      if (formData.cv) {
        formDataToSend.append('resume', formData.cv);
      }

      const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/applications`, {
        method: 'POST',
        body: formDataToSend,
      });
      
      if (response.ok) {
        setSubmitMessage('Application submitted successfully!');
        setFormData({
          fullName: "",
          email: "",
          phone: "",
          job_id: "",
          message: "",
          gender: "",
          cv: null,
        });
        
        const fileInput = document.getElementById('cv');
        if (fileInput) fileInput.value = '';
        
        setTimeout(() => setSubmitMessage(''), 3000);
      } else {
        setSubmitMessage('Failed to submit application. Please try again.');
      }
    } catch (error) {
      console.error('Application submission error:', error);
      setSubmitMessage('An error occurred. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  if (settingsLoading) {
    return (
      <section id="apply-form" className="py-16 lg:py-24 bg-[#0a0a0f] relative overflow-hidden">
        <div className="container mx-auto px-4 lg:px-8 relative z-10">
          <div className="max-w-4xl mx-auto">
            <div className="glass-card bg-[#1a1a2e]/60 border border-white/10 rounded-[2rem] p-8 md:p-10 shadow-2xl backdrop-blur-xl">
              <div className="text-center">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
              </div>
            </div>
          </div>
        </div>
      </section>
    );
  }

  return (
    <section
      id="apply-form"
      className="py-16 lg:py-24 bg-[#0a0a0f] relative overflow-hidden scroll-mt-20"
    >
      <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/2" />
      <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px] translate-y-1/2 -translate-x-1/2" />

      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        <div className="max-w-4xl mx-auto">
          <div className="glass-card bg-[#1a1a2e]/60 border border-white/10 rounded-[2rem] p-8 md:p-10 shadow-2xl backdrop-blur-xl">
            <h2 className="text-2xl md:text-3xl font-bold text-white mb-8 text-center uppercase tracking-tight">
              Apply <span className="text-transparent animate-pulse bg-clip-text bg-gradient-to-r from-[#ff4dff] via-[#b366ff] to-[#00d9ff]">Now</span>
            </h2>

            {submitMessage && (
              <div className={`mb-6 p-4 rounded-lg text-center ${
                submitMessage.includes('success') 
                  ? 'bg-green-500/20 text-green-400' 
                  : 'bg-red-500/20 text-red-400'
              }`}>
                {submitMessage}
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <Label htmlFor="fullName" className="text-[#edebeb]">Full Name *</Label>
                  <Input
                    id="fullName"
                    name="fullName"
                    placeholder="Full Name"
                    value={formData.fullName}
                    onChange={handleInputChange}
                    className="bg-white/5 border-white/10 text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                  />
                </div>

                <div className="space-y-2">
                  <Label className="text-[#edebeb]">Job *</Label>
                  <Select
                    value={formData.job_id}
                    onValueChange={(value) =>
                      setFormData((prev) => ({ ...prev, job_id: value }))
                    }
                    required
                  >
                    <SelectTrigger className="bg-white/5 border-white/10 text-white focus:ring-purple-500">
                      <SelectValue placeholder="Select a job" />
                    </SelectTrigger>
                    <SelectContent className="bg-[#1a1f26] border-white/10 text-white">
                      {loading ? (
                        <SelectItem value="loading" disabled>Loading jobs...</SelectItem>
                      ) : (
                        jobs.map((job) => (
                          <SelectItem 
                            key={job.id} 
                            value={job.id.toString()}
                            className="focus:bg-purple-600 focus:text-white"
                          >
                            {job.title}
                          </SelectItem>
                        ))
                      )}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <Label htmlFor="email" className="text-[#edebeb]">Email *</Label>
                  <Input
                    id="email"
                    name="email"
                    type="email"
                    placeholder="Email"
                    value={formData.email}
                    onChange={handleInputChange}
                    className="bg-white/5 border-white/10 text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="phone" className="text-[#edebeb]">Phone Number *</Label>
                  <Input
                    id="phone"
                    name="phone"
                    placeholder="Phone Number"
                    value={formData.phone}
                    onChange={handleInputChange}
                    className="bg-white/5 border-white/10 text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <Label htmlFor="message" className="text-[#edebeb]">Your Message</Label>
                  <Textarea
                    id="message"
                    name="message"
                    placeholder="Your Message"
                    value={formData.message}
                    onChange={handleInputChange}
                    className="min-h-[100px] bg-white/5 border-white/10 text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                  />
                </div>

                <div className="space-y-2">
                  <Label className="text-[#edebeb]">Upload CV *</Label>
                  <div className="border-2 border-dashed border-white/10 rounded-lg p-4 text-center cursor-pointer hover:border-purple-500 hover:bg-white/5 transition-all">
                    <input
                      type="file"
                      id="cv"
                      name="cv"
                      accept=".pdf,.doc,.docx"
                      onChange={handleFileChange}
                      className="hidden"
                      required
                    />
                    <label htmlFor="cv" className="cursor-pointer">
                      <Upload className="w-8 h-8 mx-auto mb-2 text-gray-400" />
                      <span className="text-sm text-gray-500">
                        {formData.cv
                          ? formData.cv.name
                          : "Choose a file or drag here"}
                      </span>
                    </label>
                  </div>
                </div>
              </div>

              <div className="space-y-2">
                <Label className="text-[#edebeb]">Gender *</Label>
                <div className="flex gap-6">
                  {["male", "female", "prefer-not-to-say"].map((g) => (
                    <label key={g} className="flex items-center gap-2 cursor-pointer group">
                      <input
                        type="radio"
                        name="gender"
                        value={g}
                        checked={formData.gender === g}
                        onChange={handleInputChange}
                        className="accent-purple-500"
                        required
                      />
                      <span className="capitalize text-[#b5b0b0] group-hover:text-white transition-colors">
                        {g.replaceAll("-", " ")}
                      </span>
                    </label>
                  ))}
                </div>
              </div>

              <Button
                type="submit"
                disabled={submitting}
                className="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white py-3 text-lg font-semibold shadow-xl shadow-purple-500/20 transition-all hover:scale-[1.01] disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {submitting ? 'SUBMITTING...' : 'SUBMIT'}
              </Button>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ApplyForm;