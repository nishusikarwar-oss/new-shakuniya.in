"use client";
import { useState, useEffect } from "react";
import { ChevronDown } from "lucide-react";

export default function Accordion() {
  const [open, setOpen] = useState(0);
  const [faqs, setFaqs] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchFaqs();
  }, []);

  const fetchFaqs = async () => {
    try {
      const response = await fetch('http://127.0.0.1:8000/api/faqs');
      const result = await response.json();
      
      console.log('API Response:', result);
      
      // IMPORTANT: FAQs ko sahi jagah se nikalna
      if (result.success && result.data && result.data.data) {
        // Ye hai actual FAQs array
        setFaqs(result.data.data);
      } else {
        setFaqs([]);
      }
      
    } catch (error) {
      console.error('Error:', error);
      setFaqs([]);
    } finally {
      setLoading(false);
    }
  };

  const toggle = (index) => {
    setOpen(open === index ? null : index);
  };

  if (loading) {
    return (
      <div className="w-full max-w-4xl mx-auto space-y-4">
        {[1, 2, 3].map((i) => (
          <div 
            key={i} 
            className="bg-card border border-borderDark rounded-2xl transition-all duration-300 p-5 md:px-8 md:py-6"
          >
            <div className="flex items-center gap-4">
              <div className="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                <div className="w-4 h-4 bg-primary/20 rounded-full animate-pulse"></div>
              </div>
              <div className="h-6 bg-gray-700 rounded w-3/4 animate-pulse"></div>
            </div>
          </div>
        ))}
      </div>
    );
  }

  if (faqs.length === 0) {
    return (
      <div className="w-full max-w-4xl mx-auto text-center py-12 text-gray-400">
        No FAQs available at the moment.
      </div>
    );
  }

  return (
    <div className="w-full max-w-4xl mx-auto space-y-4">
      {faqs.data.map((faq, index) => (
        <div
          key={faq.id || index}
          className="bg-card border border-borderDark rounded-2xl transition-all duration-300"
        >
          <button
            onClick={() => toggle(index)}
            className="w-full flex items-center justify-between text-left px-5 py-5 md:px-8 md:py-6"
          >
            <div className="flex items-center gap-4">
              <div className="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
                ?
              </div>

              <span className="text-base md:text-lg font-medium">
                {faq.question}
              </span>
            </div>

            <ChevronDown
              className={`transition-transform duration-300 ${
                open === index ? "rotate-180 text-primary" : "text-gray-400"
              }`}
            />
          </button>

          <div
            className={`grid transition-all duration-300 ease-in-out ${
              open === index
                ? "grid-rows-[1fr] opacity-100"
                : "grid-rows-[0fr] opacity-0"
            }`}
          >
            <div className="overflow-hidden">
              <p className="px-6 pb-6 md:px-14 text-gray-400 leading-relaxed">
                {faq.answer}
              </p>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}