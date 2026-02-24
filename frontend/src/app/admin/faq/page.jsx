"use client";

import { useState } from "react";

export default function FaqPage() {
  const [search, setSearch] = useState("");

  return (
    <div className="min-h-screen bg-slate-950 p-4 md:p-8 text-slate-200">
      {/* Header */}
      <div className="mb-6">
        <h1 className="text-2xl font-semibold text-white">Add FAQ</h1>
        <p className="text-sm text-slate-400 mt-1">
          Admin / FAQ Management / Add FAQ
        </p>
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {/* LEFT : ADD FAQ */}
        <div className="bg-slate-900 border border-slate-800 rounded-xl p-6">
          <h2 className="text-lg font-semibold text-white mb-4">
            FAQ Details
          </h2>

          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium mb-1 text-slate-300">
                Question <span className="text-red-500">*</span>
              </label>
              <textarea
                rows={3}
                placeholder="Enter Question"
                className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              />
            </div>

            <div>
              <label className="block text-sm font-medium mb-1 text-slate-300">
                Answer <span className="text-red-500">*</span>
              </label>
              <textarea
                rows={5}
                placeholder="Enter Answer"
                className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
              />
            </div>

            <button className="w-full bg-indigo-600 hover:bg-indigo-700 transition text-white py-2 rounded-lg font-medium">
              Save FAQ
            </button>
          </div>
        </div>

        {/* RIGHT : FAQ LIST */}
        <div className="xl:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6">
          <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
            <h2 className="text-lg font-semibold text-white">
              FAQ List
            </h2>

            <input
              type="text"
              placeholder="Search..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none w-full md:w-64"
            />
          </div>

          {/* TABLE */}
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-slate-800 text-slate-300">
                <tr>
                  <th className="px-4 py-3 text-left">ID</th>
                  <th className="px-4 py-3 text-left">Question</th>
                  <th className="px-4 py-3 text-left">Answer</th>
                  <th className="px-4 py-3 text-center">Action</th>
                </tr>
              </thead>

              <tbody className="divide-y divide-slate-800">
                {[1, 2, 3, 4, 5].map((id) => (
                  <tr
                    key={id}
                    className="hover:bg-slate-800 transition"
                  >
                    <td className="px-4 py-3">{id}</td>
                    <td className="px-4 py-3 max-w-xs truncate">
                      Sample question text goes here?
                    </td>
                    <td className="px-4 py-3 max-w-md truncate text-slate-400">
                      Sample answer text goes here explaining the FAQ...
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex justify-center gap-2">
                        <button className="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                          ✎
                        </button>
                        <button className="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                          ✕
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* FOOTER */}
          <div className="flex justify-between items-center mt-4 text-sm text-slate-400">
            <span>Showing 1 to 5 of 10 entries</span>
            <div className="flex gap-2">
              <button className="px-3 py-1 bg-slate-800 rounded">
                Previous
              </button>
              <button className="px-3 py-1 bg-indigo-600 text-white rounded">
                1
              </button>
              <button className="px-3 py-1 bg-slate-800 rounded">
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
