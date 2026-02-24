"use client"
import { MapPin, Briefcase, GraduationCap, Users } from "lucide-react";
import { Button } from "@/components/ui/button";

const jobOpenings = [
  {
    title: "Laravel Developer",
    description:
      "Experience in Laravel, HTML, CSS, JavaScript, MySQL, Ajax & jQuery. Working knowledge of REST APIs and scalable PHP applications.",
    experience: "2 years",
    positions: "4",
    qualification: "Bachelor's degree in Computer Science / IT / Engineering",
    location: "Chennai",
  },
  {
    title: "Content Writer",
    description:
      "Understanding of SEO, keyword research, competitor analysis, and content creation for blogs, websites & social media.",
    experience: "0–1 years",
    positions: "4",
    qualification:
      "Bachelor's degree / MBA Marketing / Any background with experience",
    location: "Chennai",
  },
  {
    title: "Digital Marketer / SEO",
    description:
      "Hands-on experience with paid ads, Google Analytics, HubSpot, AdWords, email and social media marketing.",
    experience: "5 years",
    positions: "4",
    qualification: "BS / MS / MBA Marketing or related field",
    location: "Chennai",
  },
  {
    title: ".Net Developer",
    description:
      "Experience in C#, .NET Core, ASP.NET MVC, Web API, Entity Framework and cloud platforms like Azure or AWS.",
    experience: "5–7 years",
    positions: "4",
    qualification: "BTech / MTech / MCA or equivalent",
    location: "Chennai",
  },
  {
    title: "PHP Developer",
    description:
      "Experience with PHP, MySQL, JavaScript, jQuery and frameworks like CodeIgniter or Yii.",
    experience: "Fresher",
    positions: "4",
    qualification: "BTech / MCA / BCA / B.Sc IT / CS",
    location: "Chennai",
  },
  {
    title: "Microsoft PowerApps Developer",
    description:
      "Strong knowledge of PowerApps, Microsoft 365, Dataverse, Flow integration and low-code platforms.",
    experience: "2 years",
    positions: "4",
    qualification: "Bachelor's degree in CS / IT",
    location: "Chennai",
  },
  {
    title: "Android Developer",
    description:
      "Experience in native Android development, Kotlin, Android SDK and handling different screen sizes.",
    experience: "0–2 years",
    positions: "4",
    qualification: "BTech / MCA / BCA / B.Sc IT / CS",
    location: "Chennai",
  },
  {
    title: "Sales Executive",
    description:
      "B2B & B2C sales experience, lead conversion, client handling and excellent communication skills.",
    experience: "2 years",
    positions: "4",
    qualification: "BE / BTech / BCA / MBA",
    location: "Chennai",
  },
];

const CurrentOpenings = () => {
  const scrollToForm = () => {
    document.getElementById("apply-form")?.scrollIntoView({
      behavior: "smooth",
    });
  };

  return (
    <section id="current-openings" className="py-16 lg:py-24 bg-[#0b0e12]">
      <div className="container mx-auto px-4  lg:px-8">
        <div className="text-center  mb-14">
          <h2 className="text-3xl md:text-4xl font-bold text-white">
            CURRENT <span className="gradient-text">OPENINGS</span>
          </h2>
        </div>

        <div className="max-w-4xl mx-auto divide-y-4 divide-white/10 bg-[#0f1217] rounded-2xl shadow-2xl border border-white/5 overflow-hidden">
          {jobOpenings.map((job, index) => (
            <div
              key={index}
              className="p-6 md:p-8 hover:bg-white/[0.02] transition-all group shadow-2xl hover:shadow-purple-500/10 duration-300 hover:scale-105"
            >
              <div className="flex flex-col lg:flex-row lg:justify-between gap-6">
                <div className="flex-1">
                  <h3 className="text-xl font-semibold text-white mb-3 border-l-4 border-purple-600 pl-3 group-hover:border-cyan-500 transition-colors">
                    {job.title}
                  </h3>

                  <p className="text-[#b5b0b0] text-sm mb-5 leading-relaxed">
                    {job.description}
                  </p>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <Info icon={Briefcase} label="Experience" value={job.experience} />
                    <Info icon={Users} label="Positions" value={job.positions} />
                    <Info
                      icon={GraduationCap}
                      label="Qualification"
                      value={job.qualification}
                    />
                    <Info icon={MapPin} label="Location" value={job.location} />
                  </div>
                </div>

                <div className="flex items-start">
                  <Button
                    onClick={scrollToForm}
                    className="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg shadow-lg shadow-purple-500/20"
                  >
                    APPLY NOW
                  </Button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

const Info = ({ icon: Icon, label, value }) => (
  <div className="flex items-start gap-2">
    <Icon className="w-4 h-4 text-purple-400 mt-0.5" />
    <span className="font-medium text-[#edebeb]">{label}:</span>
    <span className="text-[#b5b0b0]">{value}</span>
  </div>
);

export default CurrentOpenings;
