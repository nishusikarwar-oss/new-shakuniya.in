"use client"
import { useState } from "react";
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
// import { useToast } from "@/hooks/use-toast";

const jobOptions = [
  "Laravel Developer",
  "Content Writer",
  "Digital Marketer / SEO",
  ".Net Developer",
  "PHP Developer",
  "Microsoft Powerapps Developer",
  "Android Developer",
  "Sales Executive",
];

const ApplyForm = () => {
//   const { toast } = useToast();

  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    phone: "",
    job: "",
    message: "",
    gender: "",
    cv: null,
  });

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleFileChange = (e) => {
    if (e.target.files && e.target.files[0]) {
      setFormData((prev) => ({ ...prev, cv: e.target.files[0] }));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    // toast({
    //   title: "Application Submitted!",
    //   description: "Thank you for your interest. We'll get back to you soon.",
    // });

    setFormData({
      fullName: "",
      email: "",
      phone: "",
      job: "",
      message: "",
      gender: "",
      cv: null,
    });
  };

  return (
    <section
      id="apply-form"
      className="py-16 lg:py-24 bg-[#05070a] relative overflow-hidden"
    >
      {/* Decorative background elements */}
      <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/2" />
      <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px] translate-y-1/2 -translate-x-1/2" />

      <div className="container mx-auto px-4 lg:px-8 relative z-10">
        <div className="max-w-4xl mx-auto">
          <div className="glass-card bg-[#0f1217]/50 border border-white/10 rounded-2xl p-8 md:p-10 shadow-2xl backdrop-blur-xl">
            <h2 className="text-2xl md:text-3xl font-bold text-white mb-8 text-center">
              Apply <span className="gradient-text">Now</span>
            </h2>

            <form onSubmit={handleSubmit} className="space-y-6">
              {/* Name & Job */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <Label htmlFor="fullName" className="text-[#edebeb]">Full Name</Label>
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
                  <Label className="text-[#edebeb]">Job</Label>
                  <Select
                    value={formData.job}
                    onValueChange={(value) =>
                      setFormData((prev) => ({ ...prev, job: value }))
                    }
                  >
                    <SelectTrigger className="bg-white/5 border-white/10 text-white focus:ring-purple-500">
                      <SelectValue placeholder="Select a job" />
                    </SelectTrigger>
                    <SelectContent className="bg-[#1a1f26] border-white/10 text-white">
                      {jobOptions.map((job) => (
                        <SelectItem key={job} value={job} className="focus:bg-purple-600 focus:text-white">
                          {job}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              {/* Email & Phone */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <Label htmlFor="email" className="text-[#edebeb]">Email</Label>
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
                  <Label htmlFor="phone" className="text-[#edebeb]">Phone Number</Label>
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

              {/* Message & CV */}
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
                  <Label className="text-[#edebeb]">Upload CV</Label>
                  <div className="border-2 border-dashed border-white/10 rounded-lg p-4 text-center cursor-pointer hover:border-purple-500 hover:bg-white/5 transition-all">
                    <input
                      type="file"
                      id="cv"
                      accept=".pdf,.doc,.docx"
                      onChange={handleFileChange}
                      className="hidden"
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

              {/* Gender */}
              <div className="space-y-2">
                <Label className="text-[#edebeb]">Gender</Label>
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
                      />
                      <span className="capitalize text-[#b5b0b0] group-hover:text-white transition-colors">{g.replaceAll("-", " ")}</span>
                    </label>
                  ))}
                </div>
              </div>

              <Button
                type="submit"
                className="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white py-3 text-lg font-semibold shadow-xl shadow-purple-500/20 transition-all hover:scale-[1.01]"
              >
                SUBMIT
              </Button>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ApplyForm;
