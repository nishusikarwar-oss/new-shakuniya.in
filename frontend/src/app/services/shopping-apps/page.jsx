"use client"
import { Button } from "@/components/ui/button";
import {
  ArrowRight,
  ShoppingCart,
  CreditCard,
  Package,
  Truck,
  Search,
  Heart,
  Star,
  Tag,
  Percent,
  Store,
  BarChart,
  Shield,
  Users,
  Smartphone,
  Globe,
  Calendar,
} from "lucide-react";
import androidMockup from "@/images/android.png";
import app1 from "@/images/app1.png";
import app2 from "@/images/app2.png";
import app3 from "@/images/app3.png";
import app4 from "@/images/app4.png";
import app5 from "@/images/app5.png";
import app6 from "@/images/app6.png";
import shoppingimg from "@/images/shoppingimg.png";
// import androidvideo1 from "@public/videos/androidvideo1.mp4";
import androidimg from "@/images/androidimg.png";
import Navbar from "@/components/Navbar";

const packageFeatures = [
  {
    icon: Store,
    title: "Android And IOS Apps",
    description:
      "We provide awesome customizable mobile apps for both android and ios platforms.",
  },
  {
    icon: ShoppingCart,
    title: "Admin Dashboards",
    description:
      "User-friendly Administrator dashboard with Features and Characteristics for better Site Management.",
  },
  {
    icon: CreditCard,
    title: "Vendor Dashboard",
    description:
      "An effective multi vendor ecommerce script that allows an admin to have multiple vendors, serving a variety of products to customers under a single platform.",
  },
  {
    icon: Truck,
    title: "White Labeling",
    description:
      "Whitelabel iOS and Android apps for your customers and vendors with all built in native iOS and Android source code for brilliant execution.",
  },
  {
    icon: Search,
    title: "100% Source Code Ownership",
    description:
      "The entire code of the Server, Admin Dashboard, iOS, and Android apps are given with an encrypted code and complete documentation. Designed to suit your business needs..",
  },
  {
    icon: Heart,
    title: "Payment",
    description:
      "Each service delivery lists its payment gateway that may include the credit card or debit card option, or through some popular mobile wallet like PayPal, Paytm etc.",
  },
];

const androidFeatures = [
  {
    icon: Tag,
    title: "Daily Deals",
    description:
      "Provide deals of the day and best offers for your customers with this brilliant feature of our Groupon clone. This will surely evoke interest in the minds of customers to purchase those products.",
    color: "text-pink-500",
    borderColor: "border-pink-500",
  },
  {
    icon: Users,
    title: "Multi level category",
    description:
      "With three levels of categories in our E-Commerce script, buyers can choose their preferred products easily from the most appropriate categories.",
    color: "text-green-500",
    borderColor: "border-green-500",
  },
  {
    icon: Calendar,
    title: "Payment Integration",
    description:
      "With the increasing use of smartphones and the internet, the mode of payment also changed. Our Groupon clone contains all the payment gateways to make the clone user friendly.",
    color: "text-blue-500",
    borderColor: "border-blue-500",
  },
  {
    icon: Tag,
    title: "Populer Stores",
    description:
      "With this grateful feature of our E-Commerce script, display some popular stores and popular products of those stores to astonish end users and thereby influencing them to purchase those products.",
    color: "text-pink-500",
    borderColor: "border-pink-500",
  },
  {
    icon: Users,
    title: "Push Notifications",
    description:
      "Push notification allows you to announce the discount, offer, and other important updates to the customers instantly. This is the most effective way by which you can connect to your customers easily",
    color: "text-green-500",
    borderColor: "border-green-500",
  },
  {
    icon: Calendar,
    title: "Share Comments",
    description:
      "Our E-Commerce script allows end-users to comment on the products and also to share the products which they consider as useful for others..",
    color: "text-blue-500",
    borderColor: "border-blue-500",
  },
];

const userAppFlow = [
  {
    step: 1,
    title: "App Launch",
    description: "Splash screen with brand animation",
  },
  {
    step: 2,
    title: "Home Screen",
    description: "Featured products and categories",
  },
  {
    step: 3,
    title: "Product Browse",
    description: "Filter and search products",
  },
  {
    step: 4,
    title: "Product Details",
    description: "Images, specs, and reviews",
  },
  {
    step: 5,
    title: "Add to Cart",
    description: "Select variants and quantity",
  },
  { step: 6, title: "Checkout", description: "Address and payment selection" },
  {
    step: 7,
    title: "Order Confirmation",
    description: "Summary and tracking info",
  },
  { step: 8, title: "Order History", description: "Past orders and reorder" },
];

const vendorAppFlow = [
  {
    step: 1,
    title: "Vendor Dashboard",
    description: "Sales overview and analytics",
  },
  {
    step: 2,
    title: "Product Management",
    description: "Add and edit products",
  },
  {
    step: 3,
    title: "Order Management",
    description: "Process and fulfill orders",
  },
  {
    step: 4,
    title: "Inventory Control",
    description: "Stock management and alerts",
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

const ShoppingApps = () => {
  return (
    <div className="min-h-screen">
      <Navbar/>
      {/* Hero Section */}
      <section className="relative min-h-[70vh] mt-20 flex items-center overflow-hidden bg-[#05070a]">
        <div className="absolute inset-0 z-0">
          <video
            src="/videos/shoppingbg.mp4"
            autoPlay
            muted
            loop
            playsInline
            className="w-full h-full object-cover opacity-40 "
          />
          <div className="absolute inset-0 bg-gradient-to-br from-[#05070a] via-transparent to-[#05070a]/80" />
          <div className="absolute top-20 right-10 w-72 h-72 bg-purple-500/20 rounded-full blur-xl animate-float" />
          <div className="absolute bottom-20 left-10 w-96 h-96 bg-cyan-500/15 rounded-full blur-xl animate-float-delayed" />
        </div>

        <div className="container mx-auto px-4 lg:px-8 relative z-10 pt-32 pb-16">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-4">
                <span className="gradient-text">E-Commerce App</span>
              </h1>
              <h2 className="text-2xl md:text-3xl font-semibold text-[#b5b0b0] mb-6">
                App Development
              </h2>

              <p className="text-lg text-[#b5b0b0] mb-8 max-w-xl leading-relaxed">
                A mobile E-commerce App provides a complete solution for
                promoting, buying and selling products. Our e-commerce app
                development services help businesses create seamless shopping
                experiences that drive conversions and customer loyalty.
              </p>

              <div className="glass-card inline-block p-4 rounded-xl mb-6 border border-white/10">
                <p className="text-sm text-[#edebeb] mb-1">PRICING DETAILS</p>
                <p className="text-2xl font-bold text-[#f2f1f1]">
                  $4000.00 / ₹3,00,000 INR
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

            {/* <div className="relative hidden lg:block">
              <div className="w-full h-96 bg-gradient-to-br from-purple-500/20 to-cyan-500/20 rounded-2xl flex items-center justify-center border border-white/10 glass-card">
                <img src={shoppingimg} className="w-full h-full object-cover rounded-2xl " />
              </div>
            </div> */}
          </div>
        </div>
      </section>

      {/* E-commerce App Development */}
      <section className="py-20 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16 max-w-3xl mx-auto">
            <h2 className="text-3xl md:text-4xl font-bold text-white">
              E-commerce{" "}
              <span className="gradient-text">- Script Development</span>
            </h2>
            <p className="text-gray-200 py-3">
              As online businesses are growing, it is giving hope to numerous
              entrepreneurs to start their own ventures. Witnessing the change
              we all are aware of how we used to conduct the business in earlier
              times and presently how we do it. The way the landscape has
              widened with the internet market is commendable.
            </p>
          </div>

          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold text-white py-6">
                Why Choose{" "}
                <span className="gradient-text">- E-commerce App Script</span>
              </h2>

              <p className="text-[#b5b0b0] leading-relaxed mb-6">
                Shakuniya Solutions is one of the top mobile app development
                company. Our E-Commerce App Script is the best eCommerce
                solution to start your own online store and to globalize your
                customer base. It is the best script that can be integrated into
                your eCommerce website and it’s the best choice for people who
                want to sell their products online. Our E-Commerce platform has
                standard features, and it allows you to kick-start your
                eCommerce store quickly with quality. The advantage of working
                with us is the fact that we have already got the base ready for
                you and since the platform is highly expandable and
                customizable, we can easily modify it to match with your
                specifications.
              </p>

              <p className="text-[#b5b0b0] leading-relaxed mb-6">
                Most of the business uses some form of the internet; actually,
                most of the leading companies have their own websites, social
                platforms or ​ready-made eCommerce app to grab the attention of
                millions of targeted customers. Using these e-commerce platforms
                companies are able to reach the people in each corner of the
                world.
              </p>
              {/* <div className="glass-card border border-white/10 p-6 rounded-xl mb-6 bg-purple-500/5">
                <h3 className="font-bold text-white mb-3">
                  Why Choose E-commerce App Me?
                </h3>
                <p className="text-[#b5b0b0] text-sm">
                  With years of experience in e-commerce development, we
                  understand what it takes to build successful shopping
                  applications that convert browsers into buyers.
                </p>
              </div> */}
            </div>

            <div className="relative">
              <div className="w-full h-80 bg-gradient-to-br from-purple-500/10 to-cyan-500/10 rounded-2xl flex items-center justify-center border border-white/10 glass-card">
                <img
                  src={shoppingimg.src}
                  className="w-full h-full rounded-2xl hover:shadow-xl shadow-purple-500 transition-all duration-300 hover:scale-105 object-cover"
                />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Our E-commerce Packages Include */}
      <section className="py-20 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-white">
              Our E-commerce{" "}
              <span className="gradient-text">Packages Include</span>
            </h2>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {packageFeatures.map((feature, index) => {
              const IconComponent = feature.icon;
              return (
                <div
                  key={index}
                  className="glass-card border-2 border-white/10 p-6 rounded-xl hover:bg-white/5 transition-all hover:shadow-2xs shadow-purple-400 duration-300 hover:scale-105  text-center group"
                >
                  <div className="w-14 h-14 rounded-full bg-purple-500/10 flex items-center justify-center mb-4 mx-auto border border-purple-500/20 group-hover:bg-purple-500/20 transition-all">
                    <IconComponent
                      size={28}
                      className="text-purple-400"
                    />
                  </div>
                  <h3 className="font-bold text-white mb-2 underline decoration-wavy decoration-sky-500">
                    {feature.title}
                  </h3>
                  <p className="text-[#b5b0b0] text-sm">
                    {feature.description}
                  </p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Android App Features */}
      <section className="py-20 lg:py-28 relative bg-[#0b0e12]">
        <h2 className="text-3xl md:text-4xl font-bold text-white text-center mb-12">
          Features <span className="gradient-text">Include</span>
        </h2>
        <div className="container mx-auto px-4 lg:px-16 mt-12">
          <div className="grid lg:grid-cols-2 gap-12 items-stretch">
            {/* Left side: Feature Cards */}
            <div className="space-y-6 flex flex-col justify-between">
              {androidFeatures.slice(0, 3).map((feature, index) => {
                const IconComponent = feature.icon;
                return (
                  <div
                    key={index}
                    className="relative group overflow-hidden border border-white/10 p-6 rounded-xl hover:bg-white/5 transition-all hover:shadow-2xs shadow-purple-400 duration-300 hover:scale-[1.02] flex-1"
                  >
                    {/* Decorative Corner Accents */}
                    <div
                      className={`absolute top-0 left-0 w-16 h-16 border-t-2 border-l-2 rounded-tl-2xl ${feature.borderColor} -translate-x-1 -translate-y-1 opacity-60`}
                    />
                    <div
                      className={`absolute bottom-0 right-0 w-16 h-16 border-b-2 border-r-2 rounded-br-2xl ${feature.borderColor} translate-x-1 translate-y-1 opacity-60`}
                    />

                    <div className="flex items-start gap-5 relative z-10">
                      <div className={`p-2 rounded-lg  ${feature.color}`}>
                        <IconComponent size={28} />
                      </div>
                      <div>
                        <h3
                          className={`text-xl font-bold mb-3 ${feature.color}`}
                        >
                          {feature.title}
                        </h3>
                        <p className="text-gray-400 text-sm md:text-base leading-relaxed">
                          {feature.description}
                        </p>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>

            {/* Right side: Phone Mockup */}
            <div className="relative flex justify-center items-center">
              <div className="relative w-full max-w-[320px] h-full min-h-[500px] group">
                {/* Glow effect */}
                <div className="absolute -inset-4 bg-gradient-to-tr from-purple-500/20 to-cyan-500/20 rounded-[3rem] blur-2xl opacity-50 group-hover:opacity-100 transition-opacity" />

                {/* Phone Frame */}
                <div className="relative h-full w-full border-8 border-[#1a1f26] rounded-[3rem] overflow-hidden bg-[#05070a] shadow-2xl transition-transform duration-500 group-hover:scale-[1.02]">
                  {/* Camera Notch */}
                  <div className="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-[#1a1f26] rounded-b-2xl z-20" />

                  <video
                    src="/videos/androidvideo1.mp4"
                    autoPlay
                    muted
                    loop
                    playsInline
                    className="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity duration-300"
                  />
                </div>
              </div>
            </div>
          </div>

          <div className="grid lg:grid-cols-2 gap-12 items-stretch mt-20">
            {/* left side: Phone Mockup */}
            <div className="relative flex justify-center items-center">
              <div className="relative w-full max-w-[320px] h-full min-h-[500px] group">
                {/* Glow effect */}
                <div className="absolute -inset-4 bg-gradient-to-tr from-purple-500/20 to-cyan-500/20 rounded-[3rem] blur-2xl opacity-50 group-hover:opacity-100 transition-opacity" />

                {/* Phone Frame */}
                <div className="relative h-full w-full border-8 border-[#1a1f26] rounded-[3rem] overflow-hidden bg-[#05070a] shadow-2xl transition-transform duration-500 group-hover:scale-[1.02]">
                  {/* Camera Notch */}
                  <div className="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-[#1a1f26] rounded-b-2xl z-20" />

                  <img
                    src={androidimg.src}
                    className="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity duration-300"
                  />
                </div>
              </div>
            </div>

            {/* right side: Feature Cards */}
            <div className="space-y-6 flex flex-col justify-between">
              {androidFeatures.slice(-3).map((feature, index) => {
                const IconComponent = feature.icon;
                return (
                  <div
                    key={index}
                    className="relative group overflow-hidden border border-white/10 p-6 rounded-xl hover:bg-white/5 transition-all hover:shadow-2xs shadow-purple-400 duration-300 hover:scale-[1.02] flex-1"
                  >
                    {/* Decorative Corner Accents */}
                    <div
                      className={`absolute top-0 left-0 w-16 h-16 border-t-2 border-l-2 rounded-tl-2xl ${feature.borderColor} -translate-x-1 -translate-y-1 opacity-60`}
                    />
                    <div
                      className={`absolute bottom-0 right-0 w-16 h-16 border-b-2 border-r-2 rounded-br-2xl ${feature.borderColor} translate-x-1 translate-y-1 opacity-60`}
                    />

                    <div className="flex items-start gap-5 relative z-10">
                      <div className={`p-2 rounded-lg  ${feature.color}`}>
                        <IconComponent size={28} />
                      </div>
                      <div>
                        <h3
                          className={`text-xl font-bold mb-3 ${feature.color}`}
                        >
                          {feature.title}
                        </h3>
                        <p className="text-gray-400 text-sm md:text-base leading-relaxed">
                          {feature.description}
                        </p>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </section>

      {/* Vendor Web Application Flow */}
      {/* <section className="py-20 relative bg-[#0b0e12]">
        <div className="container mx-auto px-4 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-white">
              Vendor Web <span className="gradient-text">Application Flow</span>
            </h2>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {vendorAppFlow.map((item) => (
              <div
                key={item.step}
                className="glass-card border border-white/10 p-6 rounded-xl text-center hover:bg-white/5 transition-all"
              >
                <div className="w-10 h-10 rounded-full bg-gradient-to-r from-purple-600 to-cyan-600 text-white flex items-center justify-center font-bold mb-4 mx-auto">
                  {item.step}
                </div>
                <h3 className="font-bold text-white mb-2">{item.title}</h3>
                <p className="text-[#b5b0b0] text-sm">{item.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section> */}

      {/* User Part Application Flow */}
      <section className="py-20 lg:py-28 bg-[#0b0e12] relative overflow-hidden">
        {/* Optional: Background pattern */}
        <div
          className="absolute inset-0 opacity-[0.05] pointer-events-none"
          style={{
            backgroundImage: "radial-gradient(#fff 1px, transparent 1px)",
            backgroundSize: "30px 30px",
          }}
        />

        <div className="container mx-auto px-4 lg:px-8 relative z-10">
          {/* Heading */}
          <div className="text-center mb-20">
            <h2 className="text-4xl md:text-5xl font-bold text-white">
              Vendor Part{" "}
              <span className="gradient-text">Application Flow</span>
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
                    <h3 className="font-bold text-xl mb-3 gradient-text inline-block">
                      {item.title}
                    </h3>
                    <p className="text-sm md:text-base text-gray-400 leading-relaxed">
                      {item.description}
                    </p>
                  </div>

                  {/* Arrow pointing to image */}
                  <div
                    className={`hidden lg:block absolute top-1/2 -translate-y-1/2 z-0 ${
                      item.align === "right" ? "-right-3" : "-left-3"
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
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.94 3.659 1.437 5.634 1.437h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
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
              Ready to Build Your <span className="gradient-text">E-Commerce App?</span>
            </h2>
            <p className="text-[#b5b0b0] text-lg mb-8">
              Start selling online with a powerful shopping app
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Button variant="hero" size="lg" className="group">
                Get Started Now
                <ArrowRight className="ml-2 group-hover:translate-x-1 transition-transform" size={18} />
              </Button>
              <Button variant="heroOutline" size="lg" className="text-white border-white/20 hover:bg-white/10">
                Contact Sales
              </Button>
            </div>
          </div>
        </div>
      </section> */}
    </div>
  );
};

export default ShoppingApps;
