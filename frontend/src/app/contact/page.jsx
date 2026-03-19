

"use client";
import React from "react";
import { MapPin, Phone, Mail } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { useState } from "react";
import Footer from "@/components/Footer";
import Navbar from "@/components/Navbar";

function Contact() {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    message: "",
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState(null);

  // Contact Details
  const phoneNumber = " 81908 38230"; // +91 ke bina
  const emailAddress = "hr@shakuniya.in";

//   const handleSubmit = async (e) => {
//     e.preventDefault();
//     setIsSubmitting(true);
//     setSubmitStatus(null);
    
//     // Email aur WhatsApp dono par message bhejein
//     const messageData = {
//       name: formData.name,
//       email: formData.email,
//       message: formData.message,
//     };

//     try {
//       // Create query string for GET request
//       const queryParams = new URLSearchParams({
//         name: formData.name,
//         email: formData.email,
//         message: formData.message
//       }).toString();

//       // API call to Laravel backend using GET
//       const response = await fetch(`http://127.0.0.1:8000/api/contact-inquiries?${queryParams}`, {
//         method: 'GET',
//         headers: {
//           'Accept': 'application/json',
//         },
//       });

//       const data = await response.json();

//       if (response.ok) {
//         setSubmitStatus({ type: 'success', message: 'Message sent successfully!' });
        
//         // 1. WhatsApp par message
//         const whatsappMessage = `*New Contact Form Submission*%0A%0A
// *Name:* ${messageData.name}%0A
// *Email:* ${messageData.email}%0A
// *Message:* ${messageData.message}`;
        
//         const whatsappLink = `https://wa.me/${phoneNumber}?text=${whatsappMessage}`;
        
//         // 2. Email par message (mailto)
//         const subject = `Contact Form Query from ${messageData.name}`;
//         const body = `Name: ${messageData.name}%0AEmail: ${messageData.email}%0AMessage: ${messageData.message}`;
//         const mailtoLink = `mailto:${emailAddress}?subject=${subject}&body=${body}`;

//         // Dono open karein
//         window.open(mailtoLink, '_blank');
//         setTimeout(() => {
//           window.open(whatsappLink, '_blank');
//         }, 1000);

//         // Clear form after successful submission
//         setFormData({ name: "", email: "", message: "" });
        
//         // Clear success message after 5 seconds
//         setTimeout(() => setSubmitStatus(null), 5000);
//       } else {
//         setSubmitStatus({ 
//           type: 'error', 
//           message: data.message || 'Failed to send message. Please try again.' 
//         });
//       }
//     } catch (error) {
//       console.error('Error submitting form:', error);
//       setSubmitStatus({ 
//         type: 'error', 
//         message: 'Network error. Please check your connection and try again.' 
//       });
//     } finally {
//       setIsSubmitting(false);
//     }

//     console.log("Form submitted:", messageData);
//   };

  // Click handlers
  
const handleSubmit = async (e) => {
  e.preventDefault();

  setIsSubmitting(true);
  setSubmitStatus(null);

  try {
    const payload = {
      name: formData.name,
      email: formData.email,
      message: formData.message,
    };

    const response = await fetch("http://127.0.0.1:8000/api/contact", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(payload),
    });

    const data = await response.json();

    if (response.ok) {
      setSubmitStatus({
        type: "success",
        message: "Message sent successfully!",
      });

      setFormData({
        name: "",
        email: "",
        message: "",
      });
    } else {
      setSubmitStatus({
        type: "error",
        message: data.message || "Failed to send message",
      });
    }
  } catch (error) {
    console.error(error);

    setSubmitStatus({
      type: "error",
      message: "Network error",
    });
  } finally {
    setIsSubmitting(false);
  }
};
  
  const handlePhoneClick = () => {
    window.location.href = `tel:+91${phoneNumber}`;
  };

  const handleWhatsAppClick = () => {
    window.open(`https://wa.me/${phoneNumber}`, '_blank');
  };

  const handleEmailClick = () => {
    window.location.href = `mailto:${emailAddress}`;
  };

  return (
    <div className="min-h-screen bg-[#05070a]">
      {/* Hero Section */}
      <Navbar/>

      <section className="relative h-[500px] pt-32 pb-16 overflow-hidden">
        <video
          src="/videos/contact.mp4"
          autoPlay
          loop
          muted
          playsInline
          className="absolute inset-0 w-full h-full object-cover opacity-20"
        />

        <div className="absolute inset-0 bg-gradient-to-br from-[#05070a] via-transparent to-[#05070a]/80" />
        <div className="absolute top-20 right-10 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl animate-float" />
        <div className="absolute bottom-10 left-10 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl animate-float-delayed" />

        <div className="container mx-auto px-4 lg:px-8 relative z-10">
          <div className="text-center mb-12">
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-white">
              <span className="gradient-text">Contact</span> Us
            </h1>
            <p className="text-[#b5b0b0] text-lg max-w-2xl mx-auto">
              Get in touch with us for any inquiries or project discussions
            </p>
          </div>
        </div>
      </section>

      {/* Contact Section */}
      <section className="py-16 lg:py-24 relative">
        <div className="max-w-[1200px] mx-auto px-4 lg:px-8">
          <div className="glass-card bg-[#0f1217]/50 border border-white/10 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-xl hover:shadow-purple-500/10 ">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-white/10">
              {/* Left Side */}
              <div className="p-8 lg:p-12">
                <div className="space-y-10">
                  {/* Address */}
                  <div className="text-center sm:text-left">
                    <div className="w-14 h-14 mx-auto sm:mx-0 mb-4 rounded-full bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                      <MapPin className="w-7 h-7 text-purple-400" />
                    </div>
                    <h3 className="text-xl font-bold text-white mb-3">
                      Address
                    </h3>
                    <p className="text-[#b5b0b0] leading-relaxed">
                      E 308 Vijay Raja Ideal Homes,
                      Gudapakkam, Thiruvallur,
                      <br />
                      Chennai - 600124, TamilNadu,
                      <br />
                      India
                    </p>
                    <p className="text-[#b5b0b0] leading-relaxed mt-3">
                     601, Apex Tower, Tonk Road, Jaipur, 332025
                     <br /> India
                    </p>
                  </div>

                  <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Phone - Now Clickable for both Call and WhatsApp */}
                    <div className="text-center sm:text-left">
                      <div 
                        onClick={handlePhoneClick}
                        className="w-14 h-14 mx-auto sm:mx-0 mb-4 rounded-full bg-purple-500/10 flex items-center justify-center border border-purple-500/20 cursor-pointer hover:bg-purple-500/20 transition-all hover:scale-105"
                        title="Call us"
                      >
                        <Phone className="w-7 h-7 text-purple-400" />
                      </div>
                      <h3 className="text-xl font-bold text-white mb-3">
                        Call Us
                      </h3>
                      <a 
                        href={`tel:+91${phoneNumber}`}
                        className="text-[#b5b0b0] hover:text-purple-400 transition-colors"
                      >
                        +91  81908 38230
                      </a>
                      
                      {/* WhatsApp Link */}
                      <div className="mt-2">
                        <button
                          onClick={handleWhatsAppClick}
                          className="text-green-400 hover:text-green-300 transition-colors text-sm flex items-center justify-center sm:justify-start gap-2 mx-auto sm:mx-0"
                        >
                          <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            viewBox="0 0 24 24" 
                            fill="currentColor" 
                            className="w-4 h-4"
                          >
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.554 4.118 1.523 5.856L.05 23.95l6.144-1.466A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.837 0-3.61-.494-5.13-1.36l-.368-.212-4.088.975 1.074-3.98-.194-.374A9.94 9.94 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/>
                          </svg>
                          WhatsApp: +91  81908 38230
                        </button>
                      </div>
                    </div>

                    {/* Email */}
                    <div className="text-center sm:text-left">
                      <div 
                        onClick={handleEmailClick}
                        className="w-14 h-14 mx-auto sm:mx-0 mb-4 rounded-full bg-purple-500/10 flex items-center justify-center border border-purple-500/20 cursor-pointer hover:bg-purple-500/20 transition-all hover:scale-110"
                        title="Email us"
                      >
                        <Mail className="w-7 h-7 text-purple-400" />
                      </div>
                      <h3 className="text-xl font-bold text-white mb-3">
                        Email
                      </h3>
                      <a
                        href={`mailto:${emailAddress}`}
                        className="text-purple-400 hover:text-purple-300 transition-colors"
                      >
                        {emailAddress}
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              {/* Right Side - Form */}
              <div className="p-8 lg:p-12">
                <h2 className="text-2xl font-bold text-white mb-2">
                  Send us a message
                </h2>
                <p className="text-[#b5b0b0] mb-8">
                  If you have any work from me or any types of queries related
                  to my tutorial, you can send me message from here.
                </p>

                {submitStatus && (
                  <div className={`mb-4 p-3 rounded-lg ${
                    submitStatus.type === 'success' 
                      ? 'bg-green-500/20 text-green-400 border border-green-500/30' 
                      : 'bg-red-500/20 text-red-400 border border-red-500/30'
                  }`}>
                    {submitStatus.message}
                  </div>
                )}

                <form  method="post" className="space-y-5">
                  <Input
                    type="text"
                    placeholder="Enter your name"
                    value={formData.name}
                  
                    onChange={(e) =>
                      setFormData({ ...formData, name: e.target.value })
                    }
                    className="bg-white/5 border-white/10 h-12 text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                    disabled={isSubmitting}
                  />

                  <Input
                    type="email"
                    placeholder="Enter your email"
                    value={formData.email}
                    onChange={(e) =>
                      setFormData({ ...formData, email: e.target.value })
                    }
                    className="bg-white/5 border-white/10 h-12 text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                    disabled={isSubmitting}
                  />

                  <Textarea
                    placeholder="Enter your message"
                    value={formData.message}
                    onChange={(e) =>
                      setFormData({ ...formData, message: e.target.value })
                    }
                    className="bg-white/5 border-white/10 min-h-[120px] resize-none text-white placeholder:text-gray-500 focus:border-purple-500 transition-all"
                    required
                    disabled={isSubmitting}
                  />

                  <Button
                    type="submit"
                    onClick={handleSubmit}
                    className="w-full h-12 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg shadow-purple-500/20 transition-all hover:scale-[1.01] disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {isSubmitting ? 'Sending...' : 'Send Now'}
                  </Button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
      <Footer />
    </div>
  );
}

export default Contact;