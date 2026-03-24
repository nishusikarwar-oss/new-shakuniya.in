"use client";

import { useState, useEffect } from "react";
import {
  Eye,
  Pencil,
  Trash2,
  Search,
  Download,
  FileText,
  FileSpreadsheet,
  Printer,
  Plus,
  X,
  Loader2
} from "lucide-react";
import CustomEditor from "@/components/CustomEditor";
import { services as serviceApi } from "@/lib/api";


// ── Service Dropdown Component (moved outside to prevent remount) ──

const ServiceDropdown = ({ value, onChange, placeholder = "Select Service", services }) => (
  <select
    value={value}
    onChange={(e) => onChange(e.target.value)}
    className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
  >
    <option value="">{placeholder}</option>
    {services.map((service) => (
      <option key={service.service_id} value={service.service_id}>
        {service.title}-{service.service_id}  
      </option>
      
    ))}
  </select>
);

// ── Statistics Section (moved outside to prevent remount) ──
const StatisticsSection = ({
  statistics, services, showStatModal, setShowStatModal,
  editingStat, setEditingStat, statForm, setStatForm,
  saveStatistic, deleteStatistic, flash
}) => (
  <div className="bg-[#111827] rounded-lg p-6" id="statistics">
    <div className="flex justify-between items-center mb-6">
      <h2 className="text-xl font-bold text-white">Statistics</h2>
      <button
        onClick={() => {
          setEditingStat(null);
          setStatForm({ label: "", value: "", service_id: "" });
          setShowStatModal(true);
        }}
        className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <Plus className="h-4 w-4" /> Add Statistic
      </button>
    </div>

    {statistics.length === 0 ? (
      <p className="text-gray-400 text-center py-8">No statistics found. Click "Add Statistic" to create one.</p>
    ) : (
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead className="border-b border-gray-700">
            <tr className="text-left text-gray-400">
              <th className="pb-3">Label</th>
              <th className="pb-3">Value</th>
              <th className="pb-3">Service</th>
              <th className="pb-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {statistics.map((stat) => {
              const service = services.find(s => s.service_id == stat.service_id);
              return (
                <tr key={stat.stat_id} className="border-b border-gray-800">
                  <td className="py-3">{stat.label}</td>
                  <td className="py-3">{stat.value}</td>
                  <td className="py-3">
                    {stat.service_id ? service?.title || `ID: ${stat.service_id}` : "Global"}
                  </td>
                  <td className="py-3">
                    <div className="flex gap-2">
                      <button
                        onClick={() => {
                          setEditingStat(stat);
                          setStatForm({ label: stat.label, value: stat.value, service_id: stat.service_id || "" });
                          setShowStatModal(true);
                        }}
                        className="text-blue-400 hover:text-blue-300"
                      >
                        <Pencil className="h-4 w-4" />
                      </button>
                      <button
                        onClick={() => deleteStatistic(stat.statistic_id)}
                        className="text-red-400 hover:text-red-300"
                      >
                        <Trash2 className="h-4 w-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    )}

    {showStatModal && (
      <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div className="bg-[#111827] rounded-lg p-6 w-full max-w-md">
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-xl font-bold text-white">{editingStat ? "Edit" : "Add"} Statistic</h3>
            <button onClick={() => setShowStatModal(false)} className="text-gray-400 hover:text-white">
              <X className="h-5 w-5" />
            </button>
          </div>
          <div className="space-y-4">
            <div>
              <label className="text-sm font-medium text-gray-300">Label</label>
              <input
                type="text"
                value={statForm.label}
                onChange={(e) => setStatForm({ ...statForm, label: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>
            <div>
              <label className="text-sm font-medium text-gray-300">Value</label>
              <input
                type="text"
                value={statForm.value}
                onChange={(e) => setStatForm({ ...statForm, value: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>
            <div>
              <label className="text-sm font-medium text-gray-300">Service (Optional)</label>
              <ServiceDropdown
                value={statForm.service_id}
                onChange={(value) => setStatForm({ ...statForm, service_id: value })}
                placeholder="Leave empty for global"
                services={services}
              />
            </div>
            <button
              onClick={saveStatistic}
              className="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    )}
  </div>
);

// ── Testimonials Section (moved outside to prevent remount) ──
const TestimonialsSection = ({
  testimonials, services, showTestimonialModal, setShowTestimonialModal,
  editingTestimonial, setEditingTestimonial, testimonialForm, setTestimonialForm,
  saveTestimonial, deleteTestimonial
}) => (
  <div className="bg-[#111827] rounded-lg p-6" id="testimonials">
    <div className="flex justify-between items-center mb-6">
      <h2 className="text-xl font-bold text-white">Testimonials</h2>
      <button
        onClick={() => {
          setEditingTestimonial(null);
          setTestimonialForm({
            client_name: "",
            client_position: "",
            client_company: "",
            testimonial_text: "",
            rating: 5,
            client_image: null,
            is_active: true,
            display_order: "",
            service_id: ""
          });
          setShowTestimonialModal(true);
        }}
        className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <Plus className="h-4 w-4" /> Add Testimonial
      </button>
    </div>

    {testimonials.length === 0 ? (
      <p className="text-gray-400 text-center py-8">No testimonials found. Click "Add Testimonial" to create one.</p>
    ) : (
      <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        {testimonials.map((testimonial) => {
          const service = services.find(s => s.id == testimonial.service_id);
          return (
            <div key={testimonial.testimonial_id} className="bg-[#0b1220] rounded-lg p-6 border border-gray-700">
              <div className="flex mb-3">
                {[...Array(testimonial.rating || 5)].map((_, i) => (
                  <span key={i} className="text-yellow-400">★</span>
                ))}
              </div>

              <p className="text-gray-300 mb-4 italic">"{testimonial.testimonial_text}"</p>

              <div className="flex items-center gap-3">
                {testimonial.client_image && (
                  <img
                    src={testimonial.client_image}
                    alt={testimonial.client_name}
                    className="w-10 h-10 rounded-full object-cover"
                  />
                )}

                <div>
                  <h4 className="text-white font-semibold">{testimonial.client_name}</h4>
                  <p className="text-gray-400 text-sm">
                    {testimonial.client_position} | {testimonial.client_company}
                  </p>

                  {testimonial.service_id && (
                    <p className="text-indigo-400 text-xs mt-1">
                      Service: {service?.title || "N/A"}
                    </p>
                  )}
                </div>
              </div>

              <div className="flex gap-2 mt-4 pt-3 border-t border-gray-700">
                <button
                  onClick={() => {
                    setEditingTestimonial(testimonial);
                    setTestimonialForm({
                      client_name: testimonial.client_name,
                      client_position: testimonial.client_position,
                      client_company: testimonial.client_company,
                      testimonial_text: testimonial.testimonial_text,
                      rating: testimonial.rating,
                      client_image: null,
                      is_active: testimonial.is_active,
                      display_order: testimonial.display_order,
                      service_id: testimonial.service_id || ""
                    });
                    setShowTestimonialModal(true);
                  }}
                  className="text-blue-400 hover:text-blue-300"
                >
                  <Pencil className="h-4 w-4" />
                </button>

                <button
                  onClick={() => deleteTestimonial(testimonial.testimonial_id)}
                  className="text-red-400 hover:text-red-300"
                >
                  <Trash2 className="h-4 w-4" />
                </button>
              </div>
            </div>
          );
        })}
      </div>
    )}

    {showTestimonialModal && (
      <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50 overflow-y-auto">
        <div className="bg-[#111827] rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">

          <div className="flex justify-between items-center mb-4">
            <h3 className="text-xl font-bold text-white">
              {editingTestimonial ? "Edit" : "Add"} Testimonial
            </h3>

            <button
              onClick={() => setShowTestimonialModal(false)}
              className="text-gray-400 hover:text-white"
            >
              <X className="h-5 w-5" />
            </button>
          </div>

          <div className="space-y-4">

            <div>
              <label className="text-sm font-medium text-gray-300">Client Name</label>
              <input
                type="text"
                value={testimonialForm.client_name}
                onChange={(e) => setTestimonialForm({ ...testimonialForm, client_name: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>

            <div>
              <label className="text-sm font-medium text-gray-300">Position</label>
              <input
                type="text"
                value={testimonialForm.client_position}
                onChange={(e) => setTestimonialForm({ ...testimonialForm, client_position: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>

            <div>
              <label className="text-sm font-medium text-gray-300">Company</label>
              <input
                type="text"
                value={testimonialForm.client_company}
                onChange={(e) => setTestimonialForm({ ...testimonialForm, client_company: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>

            <div>
              <label className="text-sm font-medium text-gray-300">Testimonial Text</label>
              <textarea
                rows={4}
                value={testimonialForm.testimonial_text}
                onChange={(e) => setTestimonialForm({ ...testimonialForm, testimonial_text: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>

            <div>
              <label className="text-sm font-medium text-gray-300">Rating (1-5)</label>
              <input
                type="number"
                min="1"
                max="5"
                value={testimonialForm.rating}
                onChange={(e) => setTestimonialForm({ ...testimonialForm, rating: parseInt(e.target.value) })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>

            {/* CLIENT IMAGE SECTION */}
            <div>
              <label className="text-sm font-medium text-gray-300">Client Image</label>

              {(testimonialForm.client_image || editingTestimonial?.client_image) && (
                <div className="mb-2">
                  <img
                    src={
                      testimonialForm.client_image
                        ? URL.createObjectURL(testimonialForm.client_image)
                        : editingTestimonial?.client_image
                    }
                    alt="Client Preview"
                    className="w-16 h-16 rounded-full object-cover border border-gray-600"
                  />
                </div>
              )}

              <input
                type="file"
                accept="image/*"
                onChange={(e) =>
                  setTestimonialForm({
                    ...testimonialForm,
                    client_image: e.target.files[0]
                  })
                }
                className="w-full mt-1 text-gray-400"
              />
            </div>

            <div>
              <label className="text-sm font-medium text-gray-300">Display Order</label>
              <input
                type="number"
                value={testimonialForm.display_order}
                onChange={(e) => setTestimonialForm({ ...testimonialForm, display_order: parseInt(e.target.value) })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>

            <div>
              <label className="text-sm font-medium text-gray-300">Service (Optional)</label>
              <ServiceDropdown
                value={testimonialForm.service_id}
                onChange={(value) => setTestimonialForm({ ...testimonialForm, service_id: value })}
                placeholder="Leave empty for global"
                services={services}
              />
            </div>

            <div>
              <label className="flex items-center gap-2">
                <input
                  type="checkbox"
                  checked={testimonialForm.is_active}
                  onChange={(e) => setTestimonialForm({ ...testimonialForm, is_active: e.target.checked })}
                  className="rounded"
                />
                <span className="text-sm text-gray-300">Active</span>
              </label>
            </div>

            <button
              onClick={saveTestimonial}
              className="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg"
            >
              Save
            </button>

          </div>
        </div>
      </div>
    )}
  </div>
);

// ── Process Steps Section (moved outside to prevent remount) ──
const ProcessStepsSection = ({
  processSteps, services, showProcessModal, setShowProcessModal,
  editingProcess, setEditingProcess, processForm, setProcessForm,
  saveProcessStep, deleteProcessStep
}) => (
  <div className="bg-[#111827] rounded-lg p-6" id="process-steps">
    <div className="flex justify-between items-center mb-6">
      <h2 className="text-xl font-bold text-white">Process Steps</h2>
      <button
        onClick={() => {
          setEditingProcess(null);
          setProcessForm({ title: "", description: "", step_number: processSteps.length + 1, service_id: "" });
          setShowProcessModal(true);
        }}
        className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <Plus className="h-4 w-4" /> Add Step
      </button>
    </div>

    {processSteps.length === 0 ? (
      <p className="text-gray-400 text-center py-8">No process steps found. Click "Add Step" to create one.</p>
    ) : (
      <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        {processSteps.sort((a, b) => a.step_number - b.step_number).map((step) => {
          const service = services.find(s => s.id == step.step_id);
          return (
            <div key={step.step_id} className="bg-[#0b1220] rounded-lg p-6 border border-gray-700 relative">
              <div className="absolute -top-3 -left-3 w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                {step.step_number}
              </div>
              <h3 className="text-lg font-bold text-white mb-2 mt-2">{step.title}</h3>
              <p className="text-gray-400 text-sm">{step.description}</p>
              {step.service_id && (
                <p className="text-indigo-400 text-xs mt-2">Service: {service?.title || "N/A"}</p>
              )}
              <div className="flex gap-2 mt-4 pt-3 border-t border-gray-700">
                <button
                  onClick={() => {
                    setEditingProcess(step);
                    setProcessForm({
                      title: step.title,
                      description: step.description,
                      step_number: step.step_number,
                      service_id: step.service_id || ""
                    });
                    setShowProcessModal(true);
                  }}
                  className="text-blue-400 hover:text-blue-300"
                >
                  <Pencil className="h-4 w-4" />
                </button>
                <button
                  onClick={() => deleteProcessStep(step.id)}
                  className="text-red-400 hover:text-red-300"
                >
                  <Trash2 className="h-4 w-4" />
                </button>
              </div>
            </div>
          );
        })}
      </div>
    )}

    {showProcessModal && (
      <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div className="bg-[#111827] rounded-lg p-6 w-full max-w-md">
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-xl font-bold text-white">{editingProcess ? "Edit" : "Add"} Process Step</h3>
            <button onClick={() => setShowProcessModal(false)} className="text-gray-400 hover:text-white">
              <X className="h-5 w-5" />
            </button>
          </div>
          <div className="space-y-4">
            <div>
              <label className="text-sm font-medium text-gray-300">Step Number</label>
              <input
                type="number"
                value={processForm.step_number}
                onChange={(e) => setProcessForm({ ...processForm, step_number: parseInt(e.target.value) })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>
            <div>
              <label className="text-sm font-medium text-gray-300">Title</label>
              <input
                type="text"
                value={processForm.title}
                onChange={(e) => setProcessForm({ ...processForm, title: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>
            <div>
              <label className="text-sm font-medium text-gray-300">Description</label>
              <textarea
                rows={3}
                value={processForm.description}
                onChange={(e) => setProcessForm({ ...processForm, description: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>
            <div>
              <label className="text-sm font-medium text-gray-300">Service (Optional)</label>
              <ServiceDropdown
                value={processForm.service_id}
                onChange={(value) => setProcessForm({ ...processForm, service_id: value })}
                placeholder="Leave empty for global"
                services={services}
              />
            </div>
            <button
              onClick={saveProcessStep}
              className="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    )}
  </div>
);

// ── Commitments Section (moved outside to prevent remount) ──
const CommitmentsSection = ({
  commitments, services, showCommitmentModal, setShowCommitmentModal,
  editingCommitment, setEditingCommitment, commitmentForm, setCommitmentForm,
  saveCommitment, deleteCommitment
}) => (
  <div className="bg-[#111827] rounded-lg p-6" id="commitments">
    <div className="flex justify-between items-center mb-6">
      <h2 className="text-xl font-bold text-white">Commitments</h2>
      <button
        onClick={() => {
          setEditingCommitment(null);
          setCommitmentForm({ title: "", description: "", service_id: "" });
          setShowCommitmentModal(true);
        }}
        className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <Plus className="h-4 w-4" /> Add Commitment
      </button>
    </div>

    {commitments.length === 0 ? (
      <p className="text-gray-400 text-center py-8">No commitments found. Click "Add Commitment" to create one.</p>
    ) : (
      <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        {commitments.map((commitment) => {
          const service = services.find(s => s.id == commitment.service_id);
          return (
            <div key={commitment.commitment_id} className="bg-[#0b1220] rounded-lg p-6 border border-gray-700 text-center">
              <h3 className="text-lg font-bold text-white mb-2">{commitment.title}</h3>
              <p className="text-gray-400 text-sm">{commitment.description}</p>
              {commitment.service_id && (
                <p className="text-indigo-400 text-xs mt-2">Service: {service?.title || "N/A"}</p>
              )}
              <div className="flex gap-2 justify-center mt-4 pt-3 border-t border-gray-700">
                <button
                  onClick={() => {
                    setEditingCommitment(commitment);
                    setCommitmentForm({
                      title: commitment.title,
                      description: commitment.description,
                      service_id: commitment.service_id || ""
                    });
                    setShowCommitmentModal(true);
                  }}
                  className="text-blue-400 hover:text-blue-300"
                >
                  <Pencil className="h-4 w-4" />
                </button>
                <button
                  onClick={() => deleteCommitment(commitment.commitment_id)}
                  className="text-red-400 hover:text-red-300"
                >
                  <Trash2 className="h-4 w-4" />
                </button>
              </div>
            </div>
          );
        })}
      </div>
    )}

    {showCommitmentModal && (
      <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div className="bg-[#111827] rounded-lg p-6 w-full max-w-md">
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-xl font-bold text-white">{editingCommitment ? "Edit" : "Add"} Commitment</h3>
            <button onClick={() => setShowCommitmentModal(false)} className="text-gray-400 hover:text-white">
              <X className="h-5 w-5" />
            </button>
          </div>
          <div className="space-y-4">
            <div>
              <label className="text-sm font-medium text-gray-300">Title</label>
              <input
                type="text"
                value={commitmentForm.title}
                onChange={(e) => setCommitmentForm({ ...commitmentForm, title: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>
            <div>
              <label className="text-sm font-medium text-gray-300">Description</label>
              <textarea
                rows={3}
                value={commitmentForm.description}
                onChange={(e) => setCommitmentForm({ ...commitmentForm, description: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>
            <div>
              <label className="text-sm font-medium text-gray-300">Service (Optional)</label>
              <ServiceDropdown
                value={commitmentForm.service_id}
                onChange={(value) => setCommitmentForm({ ...commitmentForm, service_id: value })}
                placeholder="Leave empty for global"
                services={services}
              />
            </div>
            <button
              onClick={saveCommitment}
              className="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    )}
  </div>
);

// ── Why Choose Us Section (moved outside to prevent remount) ──
const WhyChooseUsSection = ({
  whyChooseUs, services, showWhyChooseModal, setShowWhyChooseModal,
  editingWhyChoose, setEditingWhyChoose, whyChooseForm, setWhyChooseForm,
  saveWhyChoose, deleteWhyChoose
}) => (
  <div className="bg-[#111827] rounded-lg p-6" id="why-choose-us">
    <div className="flex justify-between items-center mb-6">
      <h2 className="text-xl font-bold text-white">Why Choose Us Points</h2>
      <button
        onClick={() => {
          setEditingWhyChoose(null);
          setWhyChooseForm({ point_text: "", service_id: "" });
          setShowWhyChooseModal(true);
        }}
        className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <Plus className="h-4 w-4" /> Add Point
      </button>
    </div>

    {whyChooseUs.length === 0 ? (
      <p className="text-gray-400 text-center py-8">No points found. Click "Add Point" to create one.</p>
    ) : (
      <div className="grid md:grid-cols-2 gap-4">
        {whyChooseUs.map((point) => {
          const service = services.find(s => s.id == point.service_id);
          return (
            <div key={point.point_id} className="bg-[#0b1220] rounded-lg p-4 border border-gray-700">
              <div className="flex items-center justify-between">
                <p className="text-gray-300 flex-1">{point.point_text}</p>
                <div className="flex gap-2 ml-4">
                  <button
                    onClick={() => {
                      setEditingWhyChoose(point);
                      setWhyChooseForm({
                        point_text: point.point_text,
                        service_id: point.service_id || ""
                      });
                      setShowWhyChooseModal(true);
                    }}
                    className="text-blue-400 hover:text-blue-300"
                  >
                    <Pencil className="h-4 w-4" />
                  </button>
                  <button
                    onClick={() => deleteWhyChoose(point.id)}
                    className="text-red-400 hover:text-red-300"
                  >
                    <Trash2 className="h-4 w-4" />
                  </button>
                </div>
              </div>
              {point.service_id && (
                <p className="text-indigo-400 text-xs mt-2">Service: {service?.title || "N/A"}</p>
              )}
            </div>
          );
        })}
      </div>
    )}

    {showWhyChooseModal && (
      <div className="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
        <div className="bg-[#111827] rounded-lg p-6 w-full max-w-md">
          <div className="flex justify-between items-center mb-4">
            <h3 className="text-xl font-bold text-white">{editingWhyChoose ? "Edit" : "Add"} Point</h3>
            <button onClick={() => setShowWhyChooseModal(false)} className="text-gray-400 hover:text-white">
              <X className="h-5 w-5" />
            </button>
          </div>
          <div className="space-y-4">
            <div>
              <label className="text-sm font-medium text-gray-300">Point Text</label>
              <textarea
                rows={3}
                value={whyChooseForm.point_text}
                onChange={(e) => setWhyChooseForm({ ...whyChooseForm, point_text: e.target.value })}
                className="w-full mt-1 bg-[#0b1220] border border-gray-700 rounded px-3 py-2 text-white"
              />
            </div>
            <div>
              <label className="text-sm font-medium text-gray-300">Service (Optional)</label>
              <ServiceDropdown
                value={whyChooseForm.service_id}
                onChange={(value) => setWhyChooseForm({ ...whyChooseForm, service_id: value })}
                placeholder="Leave empty for global"
                services={services}
              />
            </div>
            <button
              onClick={saveWhyChoose}
              className="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    )}
  </div>
);

// ── Tab Navigation (moved outside to prevent remount) ──
const TAB_ITEMS = [
  { key: "services", label: "Services" },
  { key: "statistics", label: "Statistics" },
  { key: "testimonials", label: "Testimonials" },
  { key: "process", label: "Process Steps" },
  { key: "commitments", label: "Commitments" },
  { key: "whyChooseUs", label: "Why Choose Us" }
];

const TabNavigation = ({ activeTab, setActiveTab }) => (
  <div className="flex flex-wrap gap-2 border-b border-gray-800 mb-6">
    {TAB_ITEMS.map((item) => (
      <button
        key={item.key}
        onClick={() => setActiveTab(item.key)}
        className={`px-6 py-3 rounded-t-lg text-sm font-medium transition-all duration-300 ${
          activeTab === item.key
            ? "bg-indigo-600 text-white"
            : "bg-[#111827] text-gray-400 hover:text-white hover:bg-[#1f2937]"
        }`}
      >
        {item.label}
      </button>
    ))}
  </div>
);

export default function ServiceDetailsPage() {
  const [search, setSearch] = useState("");
  const [activeTab, setActiveTab] = useState("services");

  // Services State
  const [services, setServices] = useState([]);
  const [loadingServices, setLoadingServices] = useState(true);
  const [saving, setSaving] = useState(false);
  const [editId, setEditId] = useState(null);
  const [msg, setMsg] = useState(null);

  // Service Form State
  const [title, setTitle] = useState("");
  const [slug, setSlug] = useState("");
  const [shortDesc, setShortDesc] = useState("");
  const [isActive, setIsActive] = useState(true);
  const [isFeatured, setIsFeatured] = useState(false);

  // Statistics State
  const [statistics, setStatistics] = useState([]);
  const [showStatModal, setShowStatModal] = useState(false);
  const [editingStat, setEditingStat] = useState(null);
  const [statForm, setStatForm] = useState({ label: "", value: "", service_id: "" });

  // Testimonials State
  const [testimonials, setTestimonials] = useState([]);
  const [showTestimonialModal, setShowTestimonialModal] = useState(false);
  const [editingTestimonial, setEditingTestimonial] = useState(null);
  const [testimonialForm, setTestimonialForm] = useState({
    client_name: "",
    client_position: "",
    client_company: "",
    testimonial_text: "",
    rating: 5,
    client_image: null,
    is_active: true,
    display_order: "",
    service_id: ""
  });

  // Process Steps State
  const [processSteps, setProcessSteps] = useState([]);
  const [showProcessModal, setShowProcessModal] = useState(false);
  const [editingProcess, setEditingProcess] = useState(null);
  const [processForm, setProcessForm] = useState({ title: "", description: "", step_number: 1, service_id: "" });

  // Commitments State
  const [commitments, setCommitments] = useState([]);
  const [showCommitmentModal, setShowCommitmentModal] = useState(false);
  const [editingCommitment, setEditingCommitment] = useState(null);
  const [commitmentForm, setCommitmentForm] = useState({ title: "", description: "", service_id: "" });

  // Why Choose Us State
  const [whyChooseUs, setWhyChooseUs] = useState([]);
  const [showWhyChooseModal, setShowWhyChooseModal] = useState(false);
  const [editingWhyChoose, setEditingWhyChoose] = useState(null);
  const [whyChooseForm, setWhyChooseForm] = useState({ point_text: "", service_id: "" });

  // SEO/Social Media State
  const [logo, setLogo] = useState(null);
  const [featured, setFeatured] = useState(null);
  const [status, setStatus] = useState("active");
  const [ogImage, setOgImage] = useState(null);
  const [twitterImage, setTwitterImage] = useState(null);
  const [tab, setTab] = useState("openGraph");

  const flash = (type, text) => {
    setMsg({ type, text });
    setTimeout(() => setMsg(null), 4000);
  };

  const safeExtractArray = (data) => {
    if (Array.isArray(data)) return data;
    if (data?.data?.data && Array.isArray(data.data.data)) return data.data.data;
    if (data?.data && Array.isArray(data.data)) return data.data;
    return [];
  };

  // ── Services Functions ──
  const fetchServices = async () => {
    setLoadingServices(true);
    try {
      const res = await serviceApi.list({ per_page: 50 });
      const raw = res?.data;
      const data = raw?.data ?? (Array.isArray(raw) ? raw : []);
      setServices(data);
    } catch (e) {
      flash("error", e.message);
    } finally {
      setLoadingServices(false);
    }
  };

  useEffect(() => {
    fetchServices();
    fetchAllOtherData();
  }, []);

  const fetchAllOtherData = async () => {
    try {
      await Promise.all([
        fetchStatistics(),
        fetchTestimonials(),
        fetchProcessSteps(),
        fetchCommitments(),
        fetchWhyChooseUs()
      ]);
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  };

  const handleTitleChange = (v) => {
    setTitle(v);
    if (!editId) setSlug(v.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/(^-|-$)/g, ""));
  };

  const handleSaveService = async (e) => {
    e.preventDefault();
    if (!title.trim()) { flash("error", "Service title is required."); return; }
    setSaving(true);
    try {
      const payload = {
        title,
        slug: slug || undefined,
        short_description: shortDesc,
        is_active: isActive,
        is_featured: isFeatured
      };
      if (editId) {
        await serviceApi.update(editId, payload);
        flash("success", "Service updated.");
      } else {
        await serviceApi.create(payload);
        flash("success", "Service created.");
      }
      resetServiceForm();
      fetchServices();
    } catch (e) {
      flash("error", e.message);
    } finally {
      setSaving(false);
    }
  };

  const handleDeleteService = async (id) => {
    if (!confirm("Delete this service?")) return;
    try {
      await serviceApi.remove(id);
      flash("success", "Service deleted.");
      fetchServices();
    } catch (e) { flash("error", e.message); }
  };

  const startEdit = (s) => {
    setEditId(s.id);
    setTitle(s.title || "");
    setSlug(s.slug || "");
    setShortDesc(s.short_description || "");
    setIsActive(s.is_active ?? true);
    setIsFeatured(s.is_featured ?? false);
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const resetServiceForm = () => {
    setEditId(null);
    setTitle("");
    setSlug("");
    setShortDesc("");
    setIsActive(true);
    setIsFeatured(false);
  };

  const filteredServices = services.filter((s) => s.title?.toLowerCase().includes(search.toLowerCase()));

  // ── Statistics Functions ──
  const fetchStatistics = async () => {
    try {
      const response = await fetch("http://127.0.0.1:8000/api/statistics");
      const data = await response.json();
      setStatistics(safeExtractArray(data));
    } catch (error) {
      console.error("Error fetching statistics:", error);
      setStatistics([]);
    }
  };

  const saveStatistic = async () => {
    try {
      const url = editingStat
        ? `http://127.0.0.1:8000/api/statistics/${editingStat.id}`
        : "http://127.0.0.1:8000/api/statistics";
      const method = editingStat ? "PUT" : "POST";
      const response = await fetch(url, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(statForm)
      });
      if (response.ok) {
        fetchStatistics();
        setShowStatModal(false);
        setEditingStat(null);
        setStatForm({ label: "", value: "", service_id: "" });
        flash("success", "Statistic saved successfully");
      }
    } catch (error) {
      console.error("Error saving statistic:", error);
      flash("error", "Error saving statistic");
    }
  };

  const deleteStatistic = async (id) => {
    if (!confirm("Are you sure you want to delete this statistic?")) return;
    try {
      const response = await fetch(`http://127.0.0.1:8000/api/statistics/${id}`, { method: "DELETE" });
      if (response.ok) {
        fetchStatistics();
        flash("success", "Statistic deleted successfully");
      }
    } catch (error) {
      console.error("Error deleting statistic:", error);
      flash("error", "Error deleting statistic");
    }
  };

  // ── Testimonials Functions ──
  const fetchTestimonials = async () => {
    try {
      const response = await fetch("http://127.0.0.1:8000/api/testimonials");
      const data = await response.json();
      setTestimonials(safeExtractArray(data));
    } catch (error) {
      console.error("Error fetching testimonials:", error);
      setTestimonials([]);
    }
  };

  const saveTestimonial = async () => {
    try {
      const formData = new FormData();
      Object.keys(testimonialForm).forEach(key => {
        if (key === 'client_image' && testimonialForm.client_image) {
          formData.append(key, testimonialForm.client_image);
        } else if (key !== 'client_image') {
          formData.append(key, testimonialForm[key]);
        }
      });
      const url = editingTestimonial
        ? `http://127.0.0.1:8000/api/testimonials/${editingTestimonial.id}`
        : "http://127.0.0.1:8000/api/testimonials";
      const method = editingTestimonial ? "PUT" : "POST";
      const response = await fetch(url, { method, body: formData });
      if (response.ok) {
        fetchTestimonials();
        setShowTestimonialModal(false);
        setEditingTestimonial(null);
        setTestimonialForm({
          client_name: "", client_position: "", client_company: "",
          testimonial_text: "", rating: 5, client_image: null,
          is_active: true, display_order: "", service_id: ""
        });
        flash("success", "Testimonial saved successfully");
      }
    } catch (error) {
      console.error("Error saving testimonial:", error);
      flash("error", "Error saving testimonial");
    }
  };

  const deleteTestimonial = async (id) => {
    if (!confirm("Are you sure you want to delete this testimonial?")) return;
    try {
      const response = await fetch(`http://127.0.0.1:8000/api/testimonials/${id}`, { method: "DELETE" });
      if (response.ok) {
        fetchTestimonials();
        flash("success", "Testimonial deleted successfully");
      }
    } catch (error) {
      console.error("Error deleting testimonial:", error);
      flash("error", "Error deleting testimonial");
    }
  };

  // ── Process Steps Functions ──
  const fetchProcessSteps = async () => {
    try {
      const response = await fetch("http://127.0.0.1:8000/api/process-steps");
      const data = await response.json();
      setProcessSteps(safeExtractArray(data));
    } catch (error) {
      console.error("Error fetching process steps:", error);
      setProcessSteps([]);
    }
  };

  const saveProcessStep = async () => {
    try {
      const url = editingProcess
        ? `http://127.0.0.1:8000/api/process-steps/${editingProcess.id}`
        : "http://127.0.0.1:8000/api/process-steps";
      const method = editingProcess ? "PUT" : "POST";
      const response = await fetch(url, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(processForm)
      });
      if (response.ok) {
        fetchProcessSteps();
        setShowProcessModal(false);
        setEditingProcess(null);
        setProcessForm({ title: "", description: "", step_number: processSteps.length + 1, service_id: "" });
        flash("success", "Process step saved successfully");
      }
    } catch (error) {
      console.error("Error saving process step:", error);
      flash("error", "Error saving process step");
    }
  };

  const deleteProcessStep = async (id) => {
    if (!confirm("Are you sure you want to delete this process step?")) return;
    try {
      const response = await fetch(`http://127.0.0.1:8000/api/process-steps/${id}`, { method: "DELETE" });
      if (response.ok) {
        fetchProcessSteps();
        flash("success", "Process step deleted successfully");
      }
    } catch (error) {
      console.error("Error deleting process step:", error);
      flash("error", "Error deleting process step");
    }
  };

  // ── Commitments Functions ──
  const fetchCommitments = async () => {
    try {
      const response = await fetch("http://127.0.0.1:8000/api/commitments");
      const data = await response.json();
      setCommitments(safeExtractArray(data));
    } catch (error) {
      console.error("Error fetching commitments:", error);
      setCommitments([]);
    }
  };

  const saveCommitment = async () => {
    try {
      const url = editingCommitment
        ? `http://127.0.0.1:8000/api/commitments/${editingCommitment.id}`
        : "http://127.0.0.1:8000/api/commitments";
      const method = editingCommitment ? "PUT" : "POST";
      const response = await fetch(url, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(commitmentForm)
      });
      if (response.ok) {
        fetchCommitments();
        setShowCommitmentModal(false);
        setEditingCommitment(null);
        setCommitmentForm({ title: "", description: "", service_id: "" });
        flash("success", "Commitment saved successfully");
      }
    } catch (error) {
      console.error("Error saving commitment:", error);
      flash("error", "Error saving commitment");
    }
  };

  const deleteCommitment = async (id) => {
    if (!confirm("Are you sure you want to delete this commitment?")) return;
    try {
      const response = await fetch(`http://127.0.0.1:8000/api/commitments/${id}`, { method: "DELETE" });
      if (response.ok) {
        fetchCommitments();
        flash("success", "Commitment deleted successfully");
      }
    } catch (error) {
      console.error("Error deleting commitment:", error);
      flash("error", "Error deleting commitment");
    }
  };

  // ── Why Choose Us Functions ──
  const fetchWhyChooseUs = async () => {
    try {
      const response = await fetch("http://127.0.0.1:8000/api/why-choose-us-points");
      const data = await response.json();
      setWhyChooseUs(safeExtractArray(data));
    } catch (error) {
      console.error("Error fetching why choose us:", error);
      setWhyChooseUs([]);
    }
  };

  const saveWhyChoose = async () => {
    try {
      const url = editingWhyChoose
        ? `http://127.0.0.1:8000/api/why-choose-us-points/${editingWhyChoose.id}`
        : "http://127.0.0.1:8000/api/why-choose-us-points";
      const method = editingWhyChoose ? "PUT" : "POST";
      const response = await fetch(url, {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(whyChooseForm)
      });
      if (response.ok) {
        fetchWhyChooseUs();
        setShowWhyChooseModal(false);
        setEditingWhyChoose(null);
        setWhyChooseForm({ point_text: "", service_id: "" });
        flash("success", "Point saved successfully");
      }
    } catch (error) {
      console.error("Error saving why choose point:", error);
      flash("error", "Error saving point");
    }
  };

  const deleteWhyChoose = async (id) => {
    if (!confirm("Are you sure you want to delete this point?")) return;
    try {
      const response = await fetch(`http://127.0.0.1:8000/api/why-choose-us-points/${id}`, { method: "DELETE" });
      if (response.ok) {
        fetchWhyChooseUs();
        flash("success", "Point deleted successfully");
      }
    } catch (error) {
      console.error("Error deleting why choose point:", error);
      flash("error", "Error deleting point");
    }
  };

  return (
    <div className="min-h-screen bg-[#0b1220] text-gray-200 p-4 md:p-6 space-y-6" id="services">
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 className="text-2xl font-bold text-white">Services Dashboard</h1>
        <span className="text-sm text-gray-400">Admin Panel</span>
      </div>

      {msg && (
        <div className={`p-3 rounded-lg text-sm ${
          msg.type === "success" ? "bg-green-500/20 text-green-400 border border-green-500/30" : "bg-red-500/20 text-red-400 border border-red-500/30"
        }`}>{msg.text}</div>
      )}

      <TabNavigation activeTab={activeTab} setActiveTab={setActiveTab} />

      {activeTab === "services" && (
        <>
          {/* Service Form */}
          <div className="bg-slate-900 rounded-xl border border-slate-800 p-6 space-y-4">
            <h2 className="text-lg font-semibold text-white">{editId ? "Edit Service" : "Add Service"}</h2>
            <p className="text-sm text-slate-400 mt-1">Admin / Service Management</p>

            <form onSubmit={handleSaveService} className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1 text-slate-300">Service Title *</label>
                  <input
                    value={title}
                    onChange={(e) => handleTitleChange(e.target.value)}
                    required
                    className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1 text-slate-300">URL Slug</label>
                  <input
                    value={slug}
                    onChange={(e) => setSlug(e.target.value)}
                    className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium mb-1 text-slate-300">Short Description</label>
                <textarea
                  rows={3}
                  value={shortDesc}
                  onChange={(e) => setShortDesc(e.target.value)}
                  className="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
              </div>
              <div className="flex gap-6">
                <label className="flex items-center gap-2 text-sm text-slate-300">
                  <input type="checkbox" checked={isActive} onChange={(e) => setIsActive(e.target.checked)} className="accent-indigo-500 w-4 h-4" /> Active
                </label>
                <label className="flex items-center gap-2 text-sm text-slate-300">
                  <input type="checkbox" checked={isFeatured} onChange={(e) => setIsFeatured(e.target.checked)} className="accent-indigo-500 w-4 h-4" /> Featured
                </label>
              </div>
              <div className="flex justify-end gap-3">
                {editId && (
                  <button type="button" onClick={resetServiceForm} className="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg">
                    Cancel
                  </button>
                )}
                <button type="submit" disabled={saving}
                  className="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white px-8 py-2 rounded-lg flex items-center gap-2">
                  {saving && <Loader2 size={16} className="animate-spin" />}
                  {editId ? "Update Service" : "Save Service"}
                </button>
              </div>
            </form>
          </div>

          {/* Services List Table */}
          <div className="bg-slate-900 rounded-xl border border-slate-800 p-6">
            <div className="flex flex-col md:flex-row md:items-center gap-4 mb-4">
              <h2 className="text-lg font-semibold text-white flex-1">Services ({filteredServices.length})</h2>
              <input
                placeholder="Search…"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 w-full md:w-64 focus:outline-none"
              />
            </div>

            {loadingServices ? (
              <div className="text-center py-8 flex items-center justify-center gap-2 text-slate-400">
                <Loader2 size={18} className="animate-spin" /> Loading…
              </div>
            ) : filteredServices.length === 0 ? (
              <p className="text-center text-slate-400 py-8">No services found.</p>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead className="bg-slate-800 text-slate-300">
                    <tr>
                      <th className="px-4 py-3 text-left">#</th>
                      <th className="px-4 py-3 text-left">Title</th>
                      <th className="px-4 py-3 text-left">Slug</th>
                      <th className="px-4 py-3 text-center">Active</th>
                      <th className="px-4 py-3 text-center">Featured</th>
                      <th className="px-4 py-3 text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-800">
                    {filteredServices.map((s, i) => (
                      <tr key={s.service_id} className="hover:bg-slate-800/50">
                        <td className="px-4 py-3">{i + 1}</td>
                        <td className="px-4 py-3 font-medium">{s.title}</td>
                        <td className="px-4 py-3 text-slate-400 text-xs">{s.slug}</td>
                        <td className="px-4 py-3 text-center">
                          <span className={`px-2 py-0.5 rounded text-xs ${s.is_active ? "bg-green-500/20 text-green-400" : "bg-red-500/20 text-red-400"}`}>
                            {s.is_active ? "Yes" : "No"}
                          </span>
                        </td>
                        <td className="px-4 py-3 text-center">
                          <span className={`px-2 py-0.5 rounded text-xs ${s.is_featured ? "bg-purple-500/20 text-purple-400" : "bg-slate-600/40 text-slate-400"}`}>
                            {s.is_featured ? "Yes" : "No"}
                          </span>
                        </td>
                        <td className="px-4 py-3">
                          <div className="flex justify-center gap-2">
                            <button onClick={() => startEdit(s)} className="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded">
                              <Pencil size={13} />
                            </button>
                            <button onClick={() => handleDeleteService(s.service_id)} className="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">
                              <Trash2 size={13} />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </>
      )}

      {activeTab === "statistics" && (
        <StatisticsSection
          statistics={statistics}
          services={services}
          showStatModal={showStatModal}
          setShowStatModal={setShowStatModal}
          editingStat={editingStat}
          setEditingStat={setEditingStat}
          statForm={statForm}
          setStatForm={setStatForm}
          saveStatistic={saveStatistic}
          deleteStatistic={deleteStatistic}
          flash={flash}
        />
      )}

      {activeTab === "testimonials" && (
        <TestimonialsSection
          testimonials={testimonials}
          services={services}
          showTestimonialModal={showTestimonialModal}
          setShowTestimonialModal={setShowTestimonialModal}
          editingTestimonial={editingTestimonial}
          setEditingTestimonial={setEditingTestimonial}
          testimonialForm={testimonialForm}
          setTestimonialForm={setTestimonialForm}
          saveTestimonial={saveTestimonial}
          deleteTestimonial={deleteTestimonial}
        />
      )}

      {activeTab === "process" && (
        <ProcessStepsSection
          processSteps={processSteps}
          services={services}
          showProcessModal={showProcessModal}
          setShowProcessModal={setShowProcessModal}
          editingProcess={editingProcess}
          setEditingProcess={setEditingProcess}
          processForm={processForm}
          setProcessForm={setProcessForm}
          saveProcessStep={saveProcessStep}
          deleteProcessStep={deleteProcessStep}
        />
      )}

      {activeTab === "commitments" && (
        <CommitmentsSection
          commitments={commitments}
          services={services}
          showCommitmentModal={showCommitmentModal}
          setShowCommitmentModal={setShowCommitmentModal}
          editingCommitment={editingCommitment}
          setEditingCommitment={setEditingCommitment}
          commitmentForm={commitmentForm}
          setCommitmentForm={setCommitmentForm}
          saveCommitment={saveCommitment}
          deleteCommitment={deleteCommitment}
        />
      )}

      {activeTab === "whyChooseUs" && (
        <WhyChooseUsSection
          whyChooseUs={whyChooseUs}
          services={services}
          showWhyChooseModal={showWhyChooseModal}
          setShowWhyChooseModal={setShowWhyChooseModal}
          editingWhyChoose={editingWhyChoose}
          setEditingWhyChoose={setEditingWhyChoose}
          whyChooseForm={whyChooseForm}
          setWhyChooseForm={setWhyChooseForm}
          saveWhyChoose={saveWhyChoose}
          deleteWhyChoose={deleteWhyChoose}
        />
      )}
    </div>
  );
}