"use client"
import React from "react";
import { Button } from "@/components/ui/button";
import {
  ArrowRight,
  Users,
  MessageCircle,
  Image,
  Video,
  Heart,
  Share2,
  Bell,
  Shield,
  Zap,
  Globe,
  Camera,
  UserPlus,
  Search,
  Hash,
  Bookmark,
} from "lucide-react";
import socialapp from "@/images/socialapp.png";
import chatting from "@/images/chatting.png";
import featuresbg from "@/images/featuresbg.png";
import app1 from "@/images/app1.png";
import app2 from "@/images/app2.png";
import app3 from "@/images/app3.png";
import app4 from "@/images/app4.png";
import app5 from "@/images/app5.png"; 
import app6 from "@/images/app6.png";
import Navbar from "@/components/Navbar";


const whyChooseFeatures = [
  {
    icon: Users,
    title: "Complete customizable and branding",
  },
  {
    icon: MessageCircle,
    title: "Available for Android and iOS",
  },
  {
    icon: Image,
    title: " Easily accommodate a number of users activity",
  },
  {
    icon: Heart,
    title: "Dedicated Admin panel to manage all the features of the application",
  },
  {
    icon: Bell,
    title: "Provide code to adopt complete customization",
  },
  {
    icon: Shield,
    title: "Free bug support in the support period",
  },
    {
    icon: Bell,
    title: "Build by a well-experienced team of Web and Mobile developers",
  },
  {
    icon: Shield,
    title: "Proper Deliverable management of product",
  },
];

const features = [
  {
    icon: Camera,
    title: "Group Chat",
    description: "Add more members to a chat to create a group chat. Assign  a name to the group chat complete with text, special characters and  emojis. Group chat admins can add or remove users. ",
  },
  {
    icon: Video,
    title: "Share Images, Audio & Video",
    description: "Without any limit to time or data, your  customer can make an audio or a video call and can share images. The call  history allows them to know at what time the call was made. The total call  time will be calculated by the system.",
  },
  {
    icon: Users,
    title: "Share Contacts",
    description: "Sharing of contact details such as phone numbers,  email address, etc. to your contacts with a few taps. ",
  },
  {
    icon: Globe,
    title: "Share Locations",
    description: "Instant messaging is not just about sending and  receiving text messages. Take your user’s messaging experience to the  next level with features like real-time location sharing.",
  },
  {
    icon: MessageCircle,
    title: "One To One Chat ",
    description: "We have made it easy to send and receive end-to-end  messages with a secured encrypted layer just as in WhatsApp. The chat  app users can send and receive HD pictures, audio, and videos instantly.",
  },
  {
    icon: UserPlus,
    title: "Voice Calls",
    description: " ​Now, using this feature it is possible to save more of your  money by making calls all over the country by utilizing this feature. This  feature enables your users to make voice calls who are listed in your  contact by using mobile data.",
  },
  {
    icon: Search,
    title: "Instant Notifications",
    description: "Be in the know of incoming messages, missed  voice/video calls, files received and much more. Your instant messaging  app can be integrated with a push notification feature that will allow users  to preview notifications without going into the app. ",
  },
  {
    icon: Hash,
    title: "Exit Group",
    description: " ​The app users can use the group invitation links to join  different groups and also can exit from groups that they no longer intend  to stay in quickly. ",
  },
  {
    icon: Share2,
    title: "Invite Contacts",
    description: " ​The app users can invite contacts to join those  who are not on app. ",
  },
  {
    icon: Heart,
    title: "Starred Messages",
    description: " ​Messages that cannot be let go? Every such message  the users can mark them star so that it helps the users to access them and  view them easily. ",
  },
  {
    icon: Bookmark,
    title: "Message Info",
    description: " ​The app user can check his message info about delivery  time and when another user has read his message. ",
  },
  {
    icon: Bell,
    title: "In-message Reply",
    description: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi consectetur maxime totam? Necessitatibus odit ex fuga quod alias corrupti laborum!",
  },
];

const appFlow = [
  {
    id: 1,
    title: "Splash Screen",
    description:
      "The app contains attractive and user interactive splash screens to increase engagements.",
    image: app1,
    align: "left",
  },
  {
    id: 2,
    title: "Register",
    description:
      "Once the user enters his mobile number for registering on the social media app, an SMS is sent to the given mobile number for verification.",
    image: app2,
    align: "right",
  },
   {
    id: 3,
    title: "Home",
    description:
      " ​It contains the icons to jump on voice calls, chat, video calls, favourite list much more.",
    image: app3,
    align: "left",
  },
  {
    id: 4,
    title: "Choose Language",
    description:
      "T​he app user needs to select a language to continue with the same language in app. ",
    image: app4,
    align: "right",
  },
   {
    id: 5,
    title: "Insufficient Coins",
    description:
      " ​Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore officia perspiciatis necessitatibus laborum quos nostrum eum voluptates explicabo in culpa! ",
    image: app5,
    align: "left",
  },
  {
    id: 6,
    title: "Video Call",
    description:
      " ​This app feature helps you to remain connected with your friends, relatives and dear ones from whichever location you may be and any time. ",
    image: app6,
    align: "right",
  },
];


const SocialMedia = () => {
  return (
    <div className="min-h-screen">
<Navbar/>
      {/* Hero Section */}
      <section className="relative min-h-[70vh] mt-20 flex items-center overflow-hidden bg-[#05070a]">
        <div className="absolute inset-0 z-0">
          <div className="absolute inset-0 bg-gradient-to-br from-purple-900/40 via-transparent to-cyan-900/30">
            <div className="animated-grid absolute inset-0" />
          </div>
          <div className="absolute top-20 right-10 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl animate-float" />
          <div className="absolute bottom-20 left-10 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl animate-float-delayed" />
        </div>

        <div className="container mx-auto px-4 lg:px-8 relative z-10 pt-32 pb-16">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="relative hidden lg:block">
              <div className="w-full h-96 bg-gradient-to-br from-purple-500/20 to-cyan-500/20 rounded-2xl flex items-center justify-center border border-white/10 glass-card hover:shadow-xl shadow-purple-400/20 transition-all  duration-300 hover:scale-105">
                <video
                  src="/videos/socialmedia.mp4"
                  autoPlay
                  muted
                  loop
                  playsInline
                  className="w-full h-full object-cover rounded-2xl animate-pulse"
                />
              </div>
            </div>

            <div>
              <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-4">
                <span className="gradient-text">Social Media App</span>
              </h1>
              <h2 className="text-2xl md:text-3xl font-semibold text-[#b5b0b0] mb-6">
                Revolutionize Social Media App Design
              </h2>

              <p className="text-lg text-[#b5b0b0] mb-8 max-w-xl leading-relaxed">
                We offer amazingly robust & dynamic real-time chat app solutions
                to our clients with dynamic needs.
              </p>

              <div className="glass-card inline-block p-4 rounded-xl mb-6 border border-white/10">
                <p className="text-sm text-[#edebeb] mb-1">PRICING DETAILS</p>
                <p className="text-2xl font-bold text-[#f2f1f1]">
                  $5000.00 / ₹4,00,000 INR
                </p>
              </div>

              <div className="flex flex-wrap gap-4">
                <Button variant="hero" size="lg" className="group">
                  Buy Now
                  <ArrowRight
                    className="ml-2 group-hover:translate-x-1 transition-transform"
                    size={18}
                  />
                </Button>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Create a Social Media App */}
      <section className="py-20 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-6 text-white">
                Create a <span className="gradient-text">Social Media App</span>
              </h2>
              <p className="text-[#b5b0b0] leading-relaxed mb-6">
                If you want to experience the most exclusive messaging
                application that has completely transformed the messaging app
                market then you are at the right place. Various attempts have
                been made fruitless for the social media app as a real-time
                messenger application, however, none of them transformed into
                victory. 
              </p>
              <p className="text-[#b5b0b0] leading-relaxed mb-6">
               Despite of developing similar functionalities, no
                real-time chatting application has ever been a distinct
                advantage or have touched WhatsApp’s amazingness as an
                application. With a specific goal to build a chatting app at
                Shakuniya Solutions we are offering a detailed understanding of
                its comprehension development process and technicalities.
              </p>

              <div className="flex flex-wrap gap-4">
                <Button className="bg-purple-600 hover:bg-purple-700 text-white">
                  Check Out All Features
                </Button>
                <Button
                  variant="outline"
                  className="border-purple-600 text-purple-400 hover:bg-purple-500/10"
                >
                  Schedule Free Demo
                </Button>
              </div>
            </div>

            <div className="relative">
              <div className="w-full h-90 bg-gradient-to-br from-purple-500/10 to-cyan-500/10 rounded-2xl flex items-center justify-center border border-white/10 glass-card">
               <img src={socialapp.src} className="w-full h-full object-cover rounded-2xl hover:shadow-xl shadow-purple-400/20 transition-all  duration-300 hover:scale-105"/>
    
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Why Choose our app */}
      <section className="py-20 relative bg-[#0b0e12]">
       <div className="container mx-auto px-4 lg:px-8 py-20">
  {/* Heading */}
  <div className="text-center mb-16">
    <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold text-white">
      Why Choose <span className="gradient-text">our app?</span>
    </h2>
    <div className="w-40 h-1 bg-gradient-to-r from-purple-500 to-cyan-500 mx-auto mt-4 rounded-full" />
  </div>

  {/* Main Layout */}
  <div className="max-w-[1100px] mx-auto grid lg:grid-cols-2 gap-6 items-center">
    
    {/* LEFT : Image */}
    <div className="w-94 mx-auto  items-center ">
       <video
                  src="/videos/chooseus.mp4"
                  autoPlay
                  muted
                  loop
                  playsInline
                  className="w-full h-full object-cover rounded-2xl animate-pulse"
                />
    </div>

    {/* RIGHT : Cards */}
    <div className="grid sm:grid-cols-2 gap-6">
      {whyChooseFeatures.map((feature, index) => {
        const IconComponent = feature.icon;
        return (
          <div
            key={index}
            className="
              glass-card
              p-6
              rounded-2xl
              border border-white/10
              hover:border-purple-500/40
              hover:-translate-y-1
              transition-all duration-300
            "
          >
            <div className="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center mb-4 border border-purple-500/20">
              <IconComponent size={24} className="text-purple-400" />
            </div>

            <h3 className="text-white font-semibold text-lg mb-2">
              {feature.title}
            </h3>

            <p className="text-gray-400 text-sm leading-relaxed">
              {feature.description}
            </p>
          </div>
        );
      })}
    </div>

  </div>
</div>

      </section>

      {/* What makes us distinct */}
      <section className="py-20 lg:py-28 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-18">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-6 text-white">
                What makes us distinct from other{" "}
                <span className="gradient-text">
                  Chatting App development companies?
                </span>
              </h2>

              <ul className="space-y-3">
                {[
                  "Comprehensive customizations",
                  "Timely deployment",
                  "Secure installation",
                  "Multi-platform operations",
                  "Appropriate support & maintenance",
                  "Cost-efficient solutions",
                  "Powerful admin dashboard",
                  "Performance scalability",
                  "Cloud tech integration",
                ].map((item, index) => (
                  <li
                    key={index}
                    className="flex items-center gap-3 text-[#b5b0b0]"
                  >
                    <div className="w-2 h-2 rounded-full bg-purple-500" />
                    {item}
                  </li>
                ))}
              </ul>
            </div>

          <div className="relative">
  <div className="w-full h-96 md:h-[420px] lg:h-[460px]
    bg-gradient-to-br from-purple-500/20 to-cyan-500/20
    rounded-2xl flex items-center justify-center
    border border-white/10 glass-card
    overflow-hidden
    hover:shadow-xl shadow-purple-400/20
    transition-all duration-300 hover:scale-105"
  >
    <img
      src={chatting.src}
      alt="Chat Preview"
      className="w-full h-full object-cover rounded-2xl"
    />
  </div>
</div>

          </div>
        </div>
      </section>

      {/* Features */}
     <section className="py-20 relative bg-[#0b0e12] overflow-hidden">
  
  {/* Background Image */}
  <div className="absolute inset-0 z-10 ">
    <img
      src={featuresbg.src}
      alt="Background"
      className="w-full h-full object-cover object-center scale-105"
    />
    {/* Dark overlay */}
    <div className="absolute inset-0 bg-black/70 backdrop-blur-xs" />
  </div>

  <div className="container mx-auto px-4 lg:px-24 relative z-10">
    <div className="text-center mb-16">
      <h2 className="text-3xl md:text-4xl font-bold text-white">
        <span className="gradient-text">Features</span>
      </h2>
    </div>

    <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
      {features.map((feature, index) => {
        const IconComponent = feature.icon;
        return (
          <div
            key={index}
            className="glass-card border border-white/10 p-5 rounded-xl hover:bg-white/5 transition-all hover:shadow-2xl shadow-purple-400/20 duration-300 hover:scale-105 text-center group"
          >
            <div className="w-12 h-12 rounded-lg bg-purple-500/10 flex items-center justify-center mb-4 mx-auto border border-purple-500/20 group-hover:bg-purple-500/20 transition-all">
              <IconComponent
                size={24}
                className="text-purple-400"
              />
            </div>
            <h3 className="font-bold text-white mb-2 text-sm">
              {feature.title}
            </h3>
            <p className="text-[#b5b0b0] text-xs">
              {feature.description}
            </p>
          </div>
        );
      })}
    </div>
  </div>
</section>


      {/* User Part Application Flow */}
      <section className="py-20 lg:py-28 bg-[#0b0e12] relative overflow-hidden">
        {/* Optional: Background pattern */}
        <div className="absolute inset-0 opacity-[0.05] pointer-events-none" 
             style={{ backgroundImage: 'radial-gradient(#fff 1px, transparent 1px)', backgroundSize: '30px 30px' }} />
        
        <div className="container mx-auto px-4 lg:px-8 relative z-10">
          {/* Heading */}
          <div className="text-center mb-20">
            <h2 className="text-4xl md:text-5xl font-bold text-white">
              User Part <span className="gradient-text">Application Flow</span>
            </h2>
            <div className="w-24 h-1 bg-gradient-to-r from-purple-500 to-cyan-500 mx-auto mt-4 rounded-full" />
          </div>

          {/* Flow */}
          <div className="space-y-24 max-w-6xl mx-auto">
            {appFlow.map((item, index) => (
              <div
                key={index}
                className={`flex flex-col ${
                  item.align === "right" ? "lg:flex-row-reverse" : "lg:flex-row"
                } items-center justify-center gap-8 lg:gap-20`}
              >
                {/* Image */}
                <div className="w-full lg:w-1/2 flex justify-center">
                  <div className="relative group">
                    {/* Glow effect */}
                    <div className="absolute -inset-4 bg-gradient-to-tr from-purple-500/20 to-cyan-500/20 rounded-3xl blur-2xl opacity-50 group-hover:opacity-100 transition-opacity" />
                    <img
                      src={item.image.src}
                      alt={item.title}
                      className="relative max-w-[280px] md:max-w-[320px] rounded-[2.5rem] shadow-[0_0_50px_-12px_rgba(168,85,247,0.4)] border-4 border-white/10"
                    />
                  </div>
                </div>

                {/* Speech Bubble */}
                <div className="relative w-full lg:w-1/3">
                  <div className="bg-gradient-to-b from-[#1a1f26] to-[#111418] text-white p-8 rounded-3xl shadow-2xl border border-white/5 relative z-10 transform transition-transform hover:scale-[1.02]">
                    <h3 className="font-bold text-xl mb-3 gradient-text inline-block">{item.title}</h3>
                    <p className="text-sm md:text-base text-gray-400 leading-relaxed">
                      {item.description}
                    </p>
                  </div>

                  {/* Arrow pointing to image */}
                  <div
                    className={`hidden lg:block absolute top-1/2 -translate-y-1/2 z-0 ${
                      item.align === "right"
                        ? "-right-3"
                        : "-left-3"
                    }`}
                  >
                    <div className="w-10 h-10 bg-[#1a1f26] border-l border-t border-white/5 transform rotate-45 shadow-lg" />
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Floating WhatsApp Button to match screenshot */}
        <div className="fixed bottom-6 right-6 z-50">
          <button className="bg-[#25D366] hover:bg-[#20ba5a] text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-2 font-semibold transition-all hover:scale-105 active:scale-95">
            <svg viewBox="0 0 24 24" className="w-6 h-6 fill-current">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.94 3.659 1.437 5.634 1.437h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            WhatsApp Us
          </button>
        </div>
      </section>


      {/* CTA Section */}
      {/* <section className="py-20 lg:py-28 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="glass-card p-8 md:p-12 text-center max-w-4xl mx-auto border border-white/10">
            <h2 className="text-3xl md:text-4xl font-bold mb-4 text-white">
              Ready to Build Your{" "}
              <span className="gradient-text">Social Media App?</span>
            </h2>
            <p className="text-[#b5b0b0] text-lg mb-8">
              Connect people worldwide with your own social platform
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Button variant="hero" size="lg" className="group">
                Get Started Now
                <ArrowRight
                  className="ml-2 group-hover:translate-x-1 transition-transform"
                  size={18}
                />
              </Button>
              <Button
                variant="heroOutline"
                size="lg"
                className="text-white border-white/20 hover:bg-white/10"
              >
                Contact Sales
              </Button>
            </div>
          </div>
        </div>
      </section> */}
    </div>
  );
};

export default SocialMedia;
