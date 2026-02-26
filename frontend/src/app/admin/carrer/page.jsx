"use client";

import { useState } from "react";

const iconOptions = [
  "FaHeartbeat",
  "FaCalendarAlt",
  "FaGift",
  "FaMoneyBillWave",
  "FaUserShield",
  "FaUsers",
  "FaStar",
  "FaHandsHelping",
];

export default function AdminDashboard() {
  /* ===================== PERKS ===================== */
  const [perks, setPerks] = useState([
    {
      id: 1,
      title: "Health Insurance from First Day",
      icon: "FaHeartbeat",
      active: true,
    },
    {
      id: 2,
      title: "5 Days Working Per Week",
      icon: "FaCalendarAlt",
      active: true,
    },
    {
      id: 3,
      title: "Performance Appreciation Bonus",
      icon: "FaGift",
      active: true,
    },
  ]);

  const [perkForm, setPerkForm] = useState({
    title: "",
    icon: "FaGift",
  });

  const addPerk = () => {
    if (!perkForm.title) return;

    setPerks([
      ...perks,
      {
        id: Date.now(),
        title: perkForm.title,
        icon: perkForm.icon,
        active: true,
      },
    ]);

    setPerkForm({ title: "", icon: "FaGift" });
  };

  const deletePerk = (id) =>
    setPerks(perks.filter((p) => p.id !== id));

  const toggleActive = (id) =>
    setPerks(
      perks.map((p) =>
        p.id === id ? { ...p, active: !p.active } : p
      )
    );

  const editPerkTitle = (id, value) =>
    setPerks(
      perks.map((p) =>
        p.id === id ? { ...p, title: value } : p
      )
    );

  /* ===================== JOBS ===================== */
  const [jobs, setJobs] = useState([
    {
      id: 1,
      title: "Laravel Developer",
      description:
        "Experience in Laravel, HTML, CSS, JavaScript, MySQL, Ajax & jQuery. Working knowledge of REST APIs.",
      experience: "2 years",
      positions: 4,
      qualification: "Bachelor's degree in Computer Science / IT",
      location: "Chennai",
    },
    {
      id: 2,
      title: "Content Writer",
      description:
        "Understanding of SEO, keyword research, competitor analysis, and content creation.",
      experience: "0–1 years",
      positions: 4,
      qualification: "Bachelor's degree / MBA Marketing",
      location: "Chennai",
    },
  ]);

  const [jobForm, setJobForm] = useState({
    title: "",
    description: "",
    experience: "",
    positions: "",
    qualification: "",
    location: "",
  });

  const addJob = () => {
    if (!jobForm.title || !jobForm.description) return;

    setJobs([
      ...jobs,
      {
        id: Date.now(),
        ...jobForm,
        positions: Number(jobForm.positions) || 1,
      },
    ]);

    setJobForm({
      title: "",
      description: "",
      experience: "",
      positions: "",
      qualification: "",
      location: "",
    });
  };

  const deleteJob = (id) =>
    setJobs(jobs.filter((job) => job.id !== id));

  const editJobField = (id, field, value) =>
    setJobs(
      jobs.map((job) =>
        job.id === id ? { ...job, [field]: value } : job
      )
    );

  return (
    <div className="p-8 bg-[#0b0f1a] min-h-screen text-white space-y-16">
      {/* ===================== PERKS SECTION ===================== */}
      <section>
        <h1 className="text-2xl font-bold mb-6">
          Perks Admin Table
        </h1>

        {/* Add Perk */}
        <div className="bg-[#0f1525] p-4 rounded-lg mb-6 flex flex-wrap gap-3">
          <input
            type="text"
            placeholder="Perk title"
            value={perkForm.title}
            onChange={(e) =>
              setPerkForm({
                ...perkForm,
                title: e.target.value,
              })
            }
            className="bg-[#05080f] border border-white/10 px-3 py-2 rounded w-64"
          />

          <select
            value={perkForm.icon}
            onChange={(e) =>
              setPerkForm({
                ...perkForm,
                icon: e.target.value,
              })
            }
            className="bg-[#05080f] border border-white/10 px-3 py-2 rounded"
          >
            {iconOptions.map((icon) => (
              <option key={icon} value={icon}>
                {icon}
              </option>
            ))}
          </select>

          <button
            onClick={addPerk}
            className="bg-purple-600 px-5 py-2 rounded hover:bg-purple-700 transition"
          >
            Add Perk
          </button>
        </div>

        {/* Perks Table */}
        <div className="overflow-x-auto bg-[#0f1525] rounded-lg">
          <table className="w-full border-collapse">
            <thead>
              <tr className="bg-[#05080f] text-gray-300 text-sm">
                <th className="p-3 text-left">#</th>
                <th className="p-3 text-left">Title</th>
                <th className="p-3 text-left">Icon</th>
                <th className="p-3 text-center">Active</th>
                <th className="p-3 text-center">Actions</th>
              </tr>
            </thead>

            <tbody>
              {perks.map((perk, index) => (
                <tr
                  key={perk.id}
                  className="border-b border-white/5 hover:bg-white/5 transition"
                >
                  <td className="p-3">{index + 1}</td>

                  <td className="p-3">
                    <input
                      value={perk.title}
                      onChange={(e) =>
                        editPerkTitle(perk.id, e.target.value)
                      }
                      className="bg-transparent border border-white/10 px-2 py-1 rounded w-full"
                    />
                  </td>

                  <td className="p-3 text-sm text-purple-400">
                    {perk.icon}
                  </td>

                  <td className="p-3 text-center">
                    <button
                      onClick={() => toggleActive(perk.id)}
                      className={`px-3 py-1 rounded text-xs ${
                        perk.active
                          ? "bg-green-600"
                          : "bg-red-600"
                      }`}
                    >
                      {perk.active ? "Active" : "Inactive"}
                    </button>
                  </td>

                  <td className="p-3 text-center">
                    <button
                      onClick={() => deletePerk(perk.id)}
                      className="bg-red-600 px-3 py-1 rounded text-xs hover:bg-red-700"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))}

              {perks.length === 0 && (
                <tr>
                  <td
                    colSpan="5"
                    className="text-center p-6 text-gray-400"
                  >
                    No perks added
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </section>

      {/* ===================== JOBS SECTION ===================== */}
      <section className="mt-24">
        <h1 className="text-2xl font-bold mb-6">
          Jobs Admin Table
        </h1>

        {/* Add Job */}
        <div className="bg-[#0f1525] p-4 rounded-lg mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
          <input
            placeholder="Job Title"
            value={jobForm.title}
            onChange={(e) =>
              setJobForm({
                ...jobForm,
                title: e.target.value,
              })
            }
            className="bg-[#05080f] border border-white/10 px-3 py-2 rounded"
          />

          <input
            placeholder="Experience"
            value={jobForm.experience}
            onChange={(e) =>
              setJobForm({
                ...jobForm,
                experience: e.target.value,
              })
            }
            className="bg-[#05080f] border border-white/10 px-3 py-2 rounded"
          />

          <input
            type="number"
            placeholder="Positions"
            value={jobForm.positions}
            onChange={(e) =>
              setJobForm({
                ...jobForm,
                positions: e.target.value,
              })
            }
            className="bg-[#05080f] border border-white/10 px-3 py-2 rounded"
          />

          <input
            placeholder="Qualification"
            value={jobForm.qualification}
            onChange={(e) =>
              setJobForm({
                ...jobForm,
                qualification: e.target.value,
              })
            }
            className="bg-[#05080f] border border-white/10 px-3 py-2 rounded"
          />

          <input
            placeholder="Location"
            value={jobForm.location}
            onChange={(e) =>
              setJobForm({
                ...jobForm,
                location: e.target.value,
              })
            }
            className="bg-[#05080f] border border-white/10 px-3 py-2 rounded"
          />

          <textarea
            placeholder="Job Description"
            value={jobForm.description}
            onChange={(e) =>
              setJobForm({
                ...jobForm,
                description: e.target.value,
              })
            }
            className="bg-[#05080f] border border-white/10 px-3 py-2 rounded md:col-span-3"
          />

          <button
            onClick={addJob}
            className="bg-purple-600 px-6 py-2 rounded hover:bg-purple-700 transition md:col-span-3"
          >
            Add Job
          </button>
        </div>
      </section>
    </div>
  );
}
