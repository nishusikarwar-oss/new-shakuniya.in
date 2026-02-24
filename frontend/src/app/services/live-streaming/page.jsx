"use client"
import { Button } from "@/components/ui/button";
import livestreaming from "@/images/livestreaming.png";
import {
  ArrowRight,
  Users,
  MessageCircle,
  Gift,
  Shield,
  Zap,
  Star,
  Play,
  Heart,
  Share2,
  DollarSign,
  Globe,
  Smartphone,
  LayoutDashboard, List, ScanQrCode, Hash, Goal, UserRoundPen, SquareKanban, UserStar, Mic, Apple, TrendingUp, Megaphone,
} from "lucide-react";
import money from "@/images/money.png";
import live1 from "@/images/live1.png";
import live2 from "@/images/live2.png";
import live3 from "@/images/live3.png";
import live4 from "@/images/live4.png";
import costbg from "@/images/costbg.png";
import bestcms from "@/images/bestcms.png";
import chatcall from "@/images/chatcall.png";
import app1 from "@/images/app1.png";
import app2 from "@/images/app2.png";
import app3 from "@/images/app3.png";
import app4 from "@/images/app4.png";
import app5 from "@/images/app5.png"; 
import app6 from "@/images/app6.png";
import Navbar from "@/components/Navbar";


const features = [
  // {
  //   icon: Video,
  //   title: "HD Live Streaming",
  //   description:
  //     "Stream in high definition with minimal latency for the best viewer experience.",
  // },
  {
    icon: Users,
    title: " Produce Lip Dub Videos",
    description:
      "Users of the app can record and upload lip dub videos to the app. The app showcases videos made by other content creators. Users can select any of the videos to create their own or customized videos..",
  },
  {
    icon: MessageCircle,
    title: " Editing And Filtering Options",
    description:
      "The app provides a variety of editing options and filters for users to use when editing their videos in order to pique the user's interest. Instead of making a standard video, these editing options will give their videos more personality.",
  },
  {
    icon: Gift,
    title: " Music/Audio Selection",
    description:
      "Users can personalize their videos by selecting songs and audio from the app. They are free to use and dub those songs in their videos.",
  },
  {
    icon: Shield,
    title: " Option for Multiple Sharing",
    description:
      "What's the point of simply making videos and storing them? The app allows users to share their dubbed videos with other users. The videos can also be shared on other social media platforms.",
  },
  {
    icon: Zap,
    title: " Chats within the app",
    description:
      "Using it, you can also message others and start a conversation. You can talk to your friends, followers, or even complete strangers.",
  },
  {
    icon: Star,
    title: "Streaming in Real Time",
    description: "Real-time beauty filters and AR effects for streamers.",
  },
  {
    icon: Play,
    title: "Notifications",
    description:
      "The app notifies users of important updates and events. For example, if any of the users in their following list performs or enters live streaming, the app will notify them.",
  },
  {
    icon: Play,
    title: " Multiple Media Attachment Chat Options",
    description:
      "social video dubbing & sharing script is integrated with an in-app chat module with a wide variety of multimedia sharing options.",
  },
  {
    icon: Heart,
    title: " GoLive",
    description:
      "Going live is such a lot easier on our live video streaming solution. Their friends are going to be notified and that they can watch the live stream in real time on our live video streaming solution.",
  },
  {
    icon: Share2,
    title: " In-Built Video editor and music library",
    description:
      "The app offers up-to-date means for video editing and choosing music. The toolset includes a variety of filters, AR effects, animations and stickers, intuitive editing tools, and an outsized music library of any genre.",
  },
  {
    icon: DollarSign,
    title: " Secret Chats Between Users",
    description:
      "Users can create in-app groups with other users to have secret chats with them . Inspired by Snapchat, the app is integrated with a ‘Secret Chat’ feature allowing users to have private conversations, with other users, that will be erased automatically once done.",
  },
  {
    icon: Globe,
    title: "Global CDN",
    description: "Worldwide content delivery for smooth streaming anywhere.",
  },
];

const imageCards = [
  { id: 1, img: live4 },
  { id: 2, img: live2 },
  { id: 3, img: live3 },
  { id: 4, img: live1 },
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

const appFlowSteps = [
  {
    title: "User Registration",
    description: "Easy sign-up with email, phone, or social media accounts.",
  },
  {
    title: "Profile Setup",
    description:
      "Customize profile with avatar, bio, and streaming preferences.",
  },
  {
    title: "Go Live",
    description: "One-tap to start streaming with real-time viewer count.",
  },
  {
    title: "Engage & Earn",
    description: "Interact with viewers and receive virtual gifts.",
  },
];

const monetizationData = [
  {
    title: "App Purchase",
    desc: "Users can purchase premium features and subscriptions directly from the app.",
    icon: DollarSign,
  },
  {
    title: "Venture Capital",
    desc: "Raise funding through investors and venture capital opportunities.",
    icon: TrendingUp,
  },
  {
    title: "Selling the App",
    desc: "Generate revenue by selling the app or licensing it to businesses.",
    icon: Smartphone,
  },
  {
    title: "Advertising",
    desc: "Earn money through in-app ads, sponsorships, and promotions.",
    icon: Megaphone,
  },
];

const adminFeatures = [
  {
    title: "Dashboard",
    description:
      "It gives visual portrayal of key execution pointers (KPIs) and different measurements. The data can be as diagrams, graphs, or maps and with alternatives for clients to additionally penetrate down the information.",
    icon: LayoutDashboard,
  },
  {
    title: "User Management",
    description:
      "The Users List permits executives to deal with all the clients representing the association, including, expelling, and altering singular records varying.",
    icon: Users,
  },
  {
    title: "Categories",
    description:
      "By showing Category pages on our page, we give that individual a significant level review of the points we've expounded on. This allows the client to tap on the things that he/she is keen on..",
    icon: List,
  },

  {
    title: "Channels",
    description:
      "By demonstrating numerous Channels on your page, we give that individual a noteworthy level audit of the focuses we've clarified. This permits the customer to tap on the things that he/she is enthused about.",
    icon: ScanQrCode ,
  },
  {
    title: "App Description",
    description:
      "It significantly impacts your item's prosperity. There are three parts of an application's 'definition': its Name, its Description in the commercial center, and Screenshots.",
    icon: Apple,
  },
  {
    title: "Jingles",
    description:
      "It contains at least one snares and implies that unequivocally advances the item or administration being publicized, as a rule using at least one promoting trademarks.",
    icon: Mic,
  },

  {
    title: "Star User",
    description:
      " Customers using the app can star or save their favorite posts or user of the post for future use.",
    icon: UserStar,
  },
  {
    title: "Business Button",
    description:
      "People who use social media as a basic necessity of their exhibiting framework or business generally showcase quantifiable results and can make their profiles open and make it a business page by the app element i.e; the business button.",
    icon: SquareKanban,
  },
  {
    title: "Business Profile",
    description:
      "Individuals who utilize web-based social networking as an essential piece of their showcasing system for the most part observe quantifiable outcomes can make their profiles public and make it a business page by the app’s feature.",
    icon: UserRoundPen,
  },

    {
    title: "Reasons for Reporting",
    description:
      " Users have the advantage to report a spam or troll by mentioning the reasons which will already be self built in the app.",
    icon: Goal,
  },
  {
    title: "Rewards",
    description:
      "Users will receive more and more reward points with time and usage of the app.",
    icon: Gift,
  },
  {
    title: "Hashtag",
    description:
      "Enter the hashtag ( #hashtag #example ) in the video's title or portrayal. Hashtags in the title and portrayal will be hyperlinked. In the event that there are no hashtags in the title, the initial three hashtags in the portrayal will show over the video title.",
    icon: Hash,
  },
];

const LiveStreaming = () => {
  return (
    <div className="min-h-screen ">
      <Navbar/>
      {/* Hero Section */}
      <section className="relative min-h-[70vh] mt-20 flex items-center overflow-hidden bg-[#05070a]">
        <div className="absolute inset-0 z-0">
          <div className="absolute inset-0 z-0 bg-gradient-to-br from-purple-900/40 to-cyan-900/30 pointer-events-none">
            <div className="animated-grid absolute inset-0" />
          </div>
          <div className="absolute top-20 right-10 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl animate-float" />
          <div className="absolute bottom-20 left-10 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl animate-float-delayed" />
        </div>

        <div className="container  mx-auto px-4 lg:px-8 relative z-10 pt-32 pb-16">
  {/* Background Video */}
  <video
    autoPlay
    loop
    muted
    playsInline
    className="absolute inset-0 w-full h-full object-cover scale-110 blur-xs"
  >
    <source src="/videos/video2.mp4" type="video/mp4" />
  </video>

  {/* Dark Overlay */}
  <div className="absolute inset-0 bg-black/70"></div>

  {/* Content */}
  <div className="relative z-10 container mx-auto px-6">
    <div className="max-w-3xl">
      <span className="inline-block px-4 py-2 rounded-full bg-purple-500/20 text-purple-300 text-sm font-medium mb-6">
        Live Streaming App
      </span>

      <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
        <span className="gradient-text">Live Streaming</span>
        <br />
        <span className="text-foreground text-[#cdcdcd]">
          App Development
        </span>
      </h1>

      <p className="text-lg md:text-xl text-muted-foreground text-[#b5b0b0] mb-8 max-w-2xl leading-relaxed">
        Live Streaming meets all of your requirements for a
        live-streaming-like application. A Live Streaming App is the best
        way to expand your business. The script includes every feature you
        could want in a chat application. A Live Streaming App can assist
        your company in developing a way to interact with your target
        audience. You can easily get your Live Streaming App-like instant
        messaging app with the help of a live streaming app. The script is
        a set of codes that can be used to create apps like Live Streaming
        App. The Live Streaming App is available for both iOS and Android
        users.
      </p>

      <div className="glass-card inline-block p-4 rounded-xl mb-6">
        <p className="text-sm text-muted-foreground mb-1 text-[#edebeb]">
          PRICING DETAILS
        </p>
        <p className="text-2xl font-bold text-foreground text-[#f2f1f1]">
          $7000.00 / ₹5,25,000 INR
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

        <Button variant="heroOutline" size="lg">
          View Demo
        </Button>
      </div>
    </div>
  </div>

        </div>
      </section>

      {/* What is Live Streaming App */}
      <section className="py-20 relative bg-[#0b0e12] ">
        <div className="container mx-auto px-4 lg:px-8 ">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-6 text-foreground text-white">
                What is{" "}
                <span className="gradient-text">Live Streaming App?</span>
              </h2>
              <p className="text-muted-foreground leading-relaxed mb-6 text-[#edebeb]">
                Our livestreaming app is a user-friendly and cutting-edge
                platform that enables real-time connection with your audience.
                With seamless integration across multiple platforms, it allows
                you to easily broadcast your content and engage with your
                followers.
              </p>
              <p className="text-muted-foreground leading-relaxed text-[#edebeb]">
                The app features a customizable interface, giving you full
                control over the look and feel of your livestream. Whether
                you're looking to showcase your talents, share your thoughts or
                connect with your community, our app is the perfect solution for
                all your livestreaming needs. Download now and start
                broadcasting to the world!
              </p>

              <div className="flex flex-wrap gap-4 mt-8">
                <Button variant="hero" size="lg" className="group">
                  Buy Now
                  <ArrowRight
                    className="ml-2 group-hover:translate-x-1 transition-transform"
                    size={18}
                  />
                </Button>
                <Button
                  variant="heroOutline"
                  size="lg"
                  className="text-[#edebeb]"
                >
                  Connect Whatsapp..
                </Button>
              </div>
            </div>

            <div className="relative">
              <div className="w-full h-80 rounded-2xl bg-gradient-to-br from-purple-500/10 to-cyan-500/10 rounded-2xl flex items-center justify-center border-5 border-t-purple-400 glass-card">
                <img
                  src={livestreaming.src}
                  className="h-full w-full object-cover rounded-xl  group-hover:animate-none
      transition-all duration-300 hover:shadow-indigo-500/50 hover:scale-105 "
                />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Exciting Features */}
      <section className="py-20 lg:py-28 relative bg-[#0b0e12] ">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-white">
              Exciting Live Streaming{" "}
              <span className="gradient-text ">App Features</span>
            </h2>
            <p className="text-muted-foreground text-lg max-w-2xl mx-auto text-amber-50">
              Packed with all the features you need to create an engaging
              streaming platform
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {features.map((feature, index) => {
              const IconComponent = feature.icon;
              return (
                <div
                  key={index}
                  className="glass-card p-6 hover:bg-white/10 hover:rounded-bl-xl hover:rounded-tr-xl transition-all duration-300 group hover:-translate-y-2"
                >
                  <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500/20 to-cyan-500/20 flex items-center justify-center mb-4 group-hover:from-purple-500/30 group-hover:to-cyan-500/30 transition-all ">
                    <IconComponent size={24} className="text-purple-400" />
                  </div>
                  <h3 className="text-lg font-bold mb-2 text-foreground text-gray-200">
                    {feature.title}
                  </h3>
                  <p className="text-muted-foreground text-sm leading-relaxed text-gray-300">
                    {feature.description}
                  </p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Live streaming app development section */}
      <section className="py-20 bg-black  bg-gradient-to-br from-purple-900/30 via-background to-cyan-900/20">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-x4l lg:text-5xl font-bold mb-4 text-white">
              Why your{" "}
              <span className="gradient-text">Live Streaming App </span>
              Look Like..
            </h2>
          </div>

          <div className="max-w-6xl mx-auto text-center ">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 justify-center ">
              {imageCards.map((item) => (
                <div
                  key={item.id}
                  className="group overflow-hidden rounded-2xl border-5 border-l-purple-400 glass-card"
                >
                  <img
                    src={item.img.src}
                    alt="feature"
                    className="
          w-full h-full object-cover
          rounded-2xl
          transition-all duration-300
          group-hover:scale-105
          group-hover:animate-none
        "
                  />
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Make Money with life streaming app ............ */}
      {/* <section className="py-20 bg-black  bg-gradient-to-br from-purple-900/30 via-background to-cyan-900/20"> */}
        {/* <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-white">
              Make Money With{" "}
              <span className="gradient-text">Live Streaming App</span>
            </h2>
          </div>

          <div className="max-w-[1000px] mx-auto items-center justify-center  grid md:grid-cols-1 lg:grid-cols-2  border-2 ">
            <div className=" border-amber-100 border-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
              {[
                {
                  title: "Scalable Architecture",
                  desc: "Handle millions of concurrent viewers with our robust infrastructure.",
                },
                {
                  title: "Custom Branding",
                  desc: "White-label solution with full customization options.",
                },
                {
                  title: "24/7 Support",
                  desc: "Round-the-clock technical support and maintenance.",
                },
                {
                  title: "Scalable Architecture",
                  desc: "Handle millions of concurrent viewers with our robust infrastructure.",
                },
              ].map((item, index) => (
                <div
                  key={index}
                  className=" p-6 text-center rounded-2xl hover:border-2 border-purple-500 hover:bg-white/10  transition-all duration-300 hover:scale-105"
                >
                  <h3 className="text-xl font-bold mb-3 text-foreground text-white">
                    {item.title}
                  </h3>
                  <p className="text-muted-foreground text-gray-400">
                    {item.desc}
                  </p>
                </div>
              ))}
            </div>

            <div className="max-w-[250px] mx-auto h-auto">
              <img src={live2} className="w-full h-full object-cover" />
            </div>
          </div>
        </div> */}
        {/* <div className="container mx-auto px-4 lg:px-8 py-16"> */}
  {/* Heading */}
  {/* <div className="text-center mb-14">
    <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold text-white">
      Make Money With{" "}
      <span className="gradient-text">Live Streaming App</span>
    </h2>
  </div> */}

  {/* Main Wrapper */}
  {/* <div className="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"> */}
    
    {/* LEFT : Cards */}
    {/* <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
      {[
      {
    title: "App Purchase",
    desc: "Users can purchase premium features and subscriptions directly from the app.",
    icon: DollarSign,
  },
  {
    title: "Venture Capital",
    desc: "Raise funding through investors and venture capital opportunities.",
    icon: TrendingUp,
  },
  {
    title: "Selling the App",
    desc: "Generate revenue by selling the app or licensing it to businesses.",
    icon: Smartphone,
  },
  {
    title: "Advertising",
    desc: "Earn money through in-app ads, sponsorships, and promotions.",
    icon: Megaphone,
  },
      ].map((item, index) => (
        <div
          key={index}
          className="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 
          hover:scale-105 hover:border-purple-500 transition-all duration-300"
        >
          <h3 className="text-xl font-semibold text-white mb-3">
            {item.title}
          </h3>
          <p className="text-gray-400 text-sm leading-relaxed">
            {item.desc}
          </p>
        </div>
      ))}
    </div> */}

    {/* RIGHT : Mobile Image */}
    {/* <div className="flex justify-center lg:justify-end">
      <div className="w-[220px] sm:w-[260px] md:w-[300px]">
        <img
          src={live2}
          alt="Live Streaming App"
          className="w-full h-auto object-contain drop-shadow-2xl"
        />
      </div>
    </div>
  </div> */}
{/* </div> */}

      {/* </section> */}
    <div className="container mx-auto px-4 lg:px-8 py-20 bg-[#0b0f1a]">
  {/* Heading */}
  <div className="text-center mb-16">
    <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold text-white">
      Make Money With{" "}
      <span className="bg-gradient-to-r from-pink-500 to-purple-500 bg-clip-text text-transparent">
        Live Streaming App
      </span>
    </h2>
  </div>

  {/* Main Layout */}
  <div className="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    
    {/* LEFT : Cards */}
    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
      {monetizationData.map((item, index) => {
        const Icon = item.icon;
        return (
          <div
            key={index}
            className="relative bg-[#11141d] rounded-2xl p-6 
            border border-white/10 shadow-lg
            hover:shadow-purple-500/20 hover:border-purple-500/40
            transition-all duration-300 hover:-translate-y-1"
          >
            {/* Icon Badge */}
            <div className="absolute -top-4 -right-4 w-12 h-12 rounded-xl 
            bg-gradient-to-br from-pink-500 to-purple-500
            flex items-center justify-center shadow-lg">
              <Icon className="w-6 h-6 text-white" />
            </div>

            <h3 className="text-xl font-semibold text-white mb-3">
              {item.title}
            </h3>
            <p className="text-gray-400 text-sm leading-relaxed">
              {item.desc}
            </p>
          </div>
        );
      })}
    </div>

    {/* RIGHT : Mobile Image */}
    <div className="flex justify-center lg:justify-end">
      <div className="w-[220px] sm:w-[260px] md:w-[300px]">
        <img
          src={money.src}
          alt="Live Streaming App"
          className="w-full h-auto object-contain drop-shadow-[0_20px_40px_rgba(168,85,247,0.35)]"
        />
      </div>
    </div>
  </div>
</div>



      {/* What is the cost............ */}

      <section
        className="py-20 bg-black relative overflow-hidden  bg-gradient-to-br from-purple-900/30 via-background to-cyan-900/20"
        style={{
          backgroundImage: `url(${costbg})`,
          backgroundSize: "cover",
          backgroundPosition: "center",
          borderRadius: "10px",
        }}
      >
        <div className="absolute inset-0 bg-black/70" />
        <div className="max-w-[1100px] relative z-10 mx-auto px-4 lg:px-8 text-center items-center">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-white">
              What is the Cost <br />
              <span className="gradient-text pt-2">
                Of Live Streaming App Development.?
              </span>
            </h2>
            <p className="text-white text-center pt-4">
              {" "}
              Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
              eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
              enim ad minim veniam, quis nostrud exercitation ullamco laboris
              nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
              reprehenderit in voluptate velit esse cillum dolore eu fugiat
              nulla pariatur. Excepteur sint occaecat cupidatat non proident,
              sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
          </div>
          <Button variant="heroOutline" size="lg" className="text-[#edebeb]">
            Request Demo..
          </Button>
        </div>
      </section>

      {/* Why Choose Us */}
      <section className="py-20 bg-black  bg-gradient-to-br from-purple-900/30 via-background to-cyan-900/20">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-white">
              Why Choose Shakuniya for <br />
              <span className="gradient-text">
                Live Streaming App Development?
              </span>
            </h2>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[
              {
                title: "Scalable Architecture",
                desc: "Handle millions of concurrent viewers with our robust infrastructure.",
              },
              {
                title: "Custom Branding",
                desc: "White-label solution with full customization options.",
              },
              {
                title: "24/7 Support",
                desc: "Round-the-clock technical support and maintenance.",
              },
              {
                title: "Scalable Architecture",
                desc: "Handle millions of concurrent viewers with our robust infrastructure.",
              },
              {
                title: "Custom Branding",
                desc: "White-label solution with full customization options.",
              },
              {
                title: "24/7 Support",
                desc: "Round-the-clock technical support and maintenance.",
              },
              {
                title: "Regular Updates",
                desc: "Continuous feature updates and security patches.",
              },
              {
                title: "Analytics Dashboard",
                desc: "Comprehensive insights into user behavior and revenue.",
              },
              {
                title: "Quick Deployment",
                desc: "Launch your platform in weeks, not months.",
              },
            ].map((item, index) => (
              <div
                key={index}
                className=" p-6 text-center rounded-2xl hover:border-2 border-purple-500 hover:bg-white/10  transition-all duration-300 hover:scale-105"
              >
                <h3 className="text-xl font-bold mb-3 text-foreground text-white">
                  {item.title}
                </h3>
                <p className="text-muted-foreground text-gray-400">
                  {item.desc}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* App Application Flow */}
      <section className="py-20 lg:py-28 bg-[#0b0e12] relative overflow-hidden">
        {/* Optional: Background pattern */}
        <div className="absolute inset-0 opacity-[0.05] pointer-events-none" 
             style={{ backgroundImage: 'radial-gradient(#fff 1px, transparent 1px)', backgroundSize: '30px 30px' }} />
        
        <div className="container mx-auto px-4 lg:px-8 relative z-10">
          {/* Heading */}
          <div className="text-center mb-20">
            <h2 className="text-4xl md:text-5xl font-bold text-white">
             Our <span className="gradient-text">Application Flow</span>
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

      {/* Best CMS for live streaming app */}
      <section className="bg-[#0b0f19] py-16 lg:py-24">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          {/* Heading */}
          <h2 className="text-center text-3xl md:text-4xl lg:text-5xl font-bold mb-14">
            <span className="text-white">Best CMS</span>{" "}
            <span className="gradient-text">For Live Streaming Apps</span>
          </h2>

          {/* Content */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {/* Left Image */}
            <div className="flex justify-center hover:border-2 border-purple-300 rounded-xl hover:bg-white/10  transition-all duration-300 hover:scale-105">
              <img
                src={bestcms.src}
                alt="CMS Dashboard"
                className="w-full max-w-xl rounded-xl shadow-2xl hover:shadow-purple-500/50 transition-shadow duration-300"
              />
            </div>

            {/* Right Content */}
            <div className="space-y-10">
              {/* Item */}
              <div className="flex gap-4">
                <div className="mt-2 h-4 w-4 rounded-full border-2 border-purple-500 flex items-center justify-center">
                  <div className="h-2 w-2 bg-purple-500 rounded-full"></div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold text-white mb-2">
                    Centralized Dashboard for Overall Management
                  </h3>
                  <p className="text-gray-400 leading-relaxed">
                    A centralised dashboard will plug into your different
                    information sources and concentrate precisely what is
                    essential for your business execution examinations, plug
                    into your current frameworks implies that you can continue
                    utilizing the frameworks that work for you. Another
                    significant positive of utilizing a brought together
                    dashboard is that you can have the specific data that you
                    need.
                  </p>
                </div>
              </div>

              {/* Item */}
              <div className="flex gap-4">
                <div className="mt-2 h-4 w-4 rounded-full border-2 border-purple-500 flex items-center justify-center">
                  <div className="h-2 w-2 bg-purple-500 rounded-full"></div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold text-white mb-2">
                    Diverse Posts
                  </h3>
                  <p className="text-gray-400 leading-relaxed">
                    you can share recordings and pictures from over 25+ Channels
                    on the stage including design, parody and jokes, natively
                    constructed medicinal services arrangements, amusement,
                    singing, sports, news and some more.
                  </p>
                </div>
              </div>

              {/* Item */}
              <div className="flex gap-4">
                <div className="mt-2 h-4 w-4 rounded-full border-2 border-purple-500 flex items-center justify-center">
                  <div className="h-2 w-2 bg-purple-500 rounded-full"></div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold text-white mb-2">
                    Live Stream
                  </h3>
                  <p className="text-gray-400 leading-relaxed">
                    Users can do live stream for media advancements for making
                    systems of live sight and sound shared among individuals,
                    organizations and associations.
                  </p>
                </div>
              </div>
            </div>

            {/* Right Content */}
            <div className="space-y-10">
              {/* Item */}
              <div className="flex gap-4">
                <div className="mt-2 h-4 w-4 rounded-full border-2 border-purple-500 flex items-center justify-center">
                  <div className="h-2 w-2 bg-purple-500 rounded-full"></div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold text-white mb-2">
                    Wallet
                  </h3>
                  <p className="text-gray-400 leading-relaxed">
                    The versatile wallet is an application that can be
                    introduced on a cell phone or it is a current work in
                    highlight of a cell phone. A versatile wallet stores
                    Mastercard, charge card, coupons, or prize cards data.
                  </p>
                </div>
              </div>

              {/* Item */}
              <div className="flex gap-4">
                <div className="mt-2 h-4 w-4 rounded-full border-2 border-purple-500 flex items-center justify-center">
                  <div className="h-2 w-2 bg-purple-500 rounded-full"></div>
                </div>
                <div>
                  <h3 className="text-xl font-semibold text-white mb-2">
                    Chat & Call
                  </h3>
                  <p className="text-gray-400 leading-relaxed">
                    Chat features provide an upgraded, rich messaging and
                    calling experience.
                  </p>
                </div>
              </div>
            </div>

            {/* Left Image */}
            <div className="flex justify-center mt-6 hover:border-2 border-purple-300 rounded-xl hover:bg-white/10  transition-all duration-300 hover:scale-105">
              <img
                src={chatcall.src}
                alt="CMS Dashboard"
                className="w-full max-w-xl rounded-xl shadow-2xl hover:shadow-purple-500/50 transition-shadow duration-300"
              />
            </div>
          </div>
        </div>
      </section>

      {/* Overall admin features  */}
      <section className="py-16 lg:py-24 bg-[#0b0f19]">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          {/* Heading */}
          <h2 className="text-center text-3xl md:text-4xl lg:text-5xl font-bold mb-14">
            <span className="gradient-text">Overall</span>{" "}
            <span className="text-white">Admin Features</span>
          </h2>

          {/* Cards */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {adminFeatures.map((item, index) => {
              const Icon = item.icon;
              return (
                <div
                  key={index}
                  className="group
                rounded-2xl hover:border-2 border-purple-500 hover:bg-white/10  transition-all duration-300 hover:scale-105 rounded-2xl p-8 text-center"
                >
                  {/* Icon */}
                  <div className="flex justify-center mb-6">
                    <div
                      className="w-20 h-20 rounded-full bg-purple-100 flex items-center justify-center
                  group-hover:bg-purple-600 transition-colors duration-300"
                    >
                      <Icon className="w-10 h-10 text-purple-600 group-hover:text-white transition-colors" />
                    </div>
                  </div>

                  {/* Title */}
                  <h3 className="text-xl font-semibold text-white mb-4 underline decoration-dotted underline-offset-4">
                    {item.title}
                  </h3>

                  {/* Description */}
                  <p className="text-gray-300 leading-relaxed text-sm">
                    {item.description}
                  </p>
                </div>
              );
            })}
          </div>
        </div>
      </section>
    </div>
  );
};

export default LiveStreaming;
