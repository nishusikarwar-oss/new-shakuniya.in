import {
  Facebook,
  Twitter,
  Instagram,
  Linkedin,
  Youtube,
  MapPin,
  Phone,
  Mail,
} from "lucide-react";

const footerLinks = {
  company: [
    { name: "Home", href: "/" },
    { name: "About", href: "/about" },
    { name: "Partnership", href: "#" },
    { name: "Career", href: "/career" },
    { name: "Contact", href: "/contact" },
  ],
  services: [
    { name: "Android Development", href: "#" },
    { name: "iOS Development", href: "#" },
    { name: "Website Development", href: "#" },
    { name: "Software Development", href: "#" },
    { name: "IT Consultant Service", href: "#" },
     { name: "Live Streaming Apps", href: "#" },
    { name: "Audio Streaming Apps", href: "#" },
    { name: "Social Media Apps", href: "#" },
    { name: "E-Commerce Apps", href: "#" },
  ],
  products: [
    { name: "Vyaparbot", href: "#" },
    { name: "EWS", href: "#" },
    { name: "NiyaMeet", href: "#" },
    { name: "Bio Linker", href: "#" },
  ],
};

const socialLinks = [
  { icon: Facebook, href: "#", color: "hover:text-blue-400" },
  { icon: Twitter, href: "#", color: "hover:text-sky-400" },
  { icon: Instagram, href: "#", color: "hover:text-pink-400" },
  { icon: Linkedin, href: "#", color: "hover:text-blue-500" },
  { icon: Youtube, href: "#", color: "hover:text-red-500" },
];

const Footer = () => {
  return (
    <footer className="bg-[#050510] border-t border-white/5 pt-20 pb-10">
      <div className="container mx-auto px-4 lg:px-8">
     {/* Logo & Social */}
        
          <div className="flex justify-between px-6 mb-6">
            <div className="flex items-center gap-3 mb-8">
              <div className="w-12 h-12 bg-gradient-to-r from-[#9333ea] to-[#a855f7] rounded-xl flex items-center justify-center shadow-lg">
                <span className="text-white font-bold text-2xl">S</span>
              </div>
              <span className="text-2xl font-bold text-white tracking-tight">
                Shakuniya
              </span>
            </div>

            <div className="flex gap-4">
              {socialLinks.map((social, index) => (
                <a
                  key={index}
                  href={social.href}
                  className="w-10 h-10 rounded-full bg-[#16162a] border border-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-[#1c1c36] transition-all duration-300"
                >
                  <social.icon className="w-5 h-5" />
                </a>
              ))}
            </div>
          </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-20 px-6">
          {/* Company */}
          <div>
            <h4 className="font-bold text-white text-lg mb-8 uppercase tracking-wider">
              Company
            </h4>
            <ul className="space-y-4">
              {footerLinks.company.map((link) => (
                <li key={link.name}>
                  <a
                    href={link.href}
                    className="text-gray-400 hover:text-[#b366ff] transition-colors duration-300 font-medium"
                  >
                    {link.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Services */}
          <div>
            <h4 className="font-bold text-white text-lg mb-8 uppercase tracking-wider">
              Services
            </h4>
            <ul className="space-y-4">
              {footerLinks.services.map((link) => (
                <li key={link.name}>
                  <a
                    href={link.href}
                    className="text-gray-400 hover:text-[#b366ff] transition-colors duration-300 font-medium"
                  >
                    {link.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Products */}
          <div>
            <h4 className="font-bold text-white text-lg mb-8 uppercase tracking-wider">
              Products
            </h4>
            <ul className="space-y-4">
              {footerLinks.products.map((link) => (
                <li key={link.name}>
                  <a
                    href={link.href}
                    className="text-gray-400 hover:text-[#b366ff] transition-colors duration-300 font-medium"
                  >
                    {link.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="font-bold text-white text-lg mb-8 uppercase tracking-wider">
              Registered Office
            </h4>

            <div className="space-y-6 text-gray-400">
              <div className="flex items-start gap-4">
                <MapPin className="w-5 h-5 mt-1 text-[#b366ff] flex-shrink-0" />
                <p className="font-medium leading-relaxed">
                  E-308, 3rd Floor, Vijay Raja Ideal Homes, Gudapakkam,
                  Thiruvalur, Chennai 600124, TamilNadu, India
                </p>
              </div>

              <div className="flex items-start gap-4">
                <MapPin className="w-5 h-5 mt-1 text-[#b366ff] flex-shrink-0" />
                <p className="font-medium leading-relaxed">
                  601, Apex Tower, Tonk Road, Jaipur,
                 332025
                </p>
              </div>

              <div className="flex items-center gap-4">
                <Phone className="w-5 h-5 text-[#b366ff] flex-shrink-0" />
                <a
                  href="tel:+918108383230"
                  className="hover:text-white transition-colors font-medium"
                >
                  +91 81083 83230
                </a>
              </div>

              <div className="flex items-center gap-4">
                <Mail className="w-5 h-5 text-[#b366ff] flex-shrink-0" />
                <a
                  href="mailto:info@shakuniya.com"
                  className="hover:text-white transition-colors font-medium"
                >
                  info@shakuniya.com
                </a>
              </div>
            </div>
          </div>

          <div className="">
              <h5 className="font-bold text-white text-lg mb-8 uppercase tracking-wider">
                Branch Office UAE
              </h5>
              <p className="text-gray-400 font-medium">
                317, Sunrise Building, Al Karama Dubai UAE
              </p>
              <p className="text-[#b366ff] font-bold mt-2">
                +971 5245 54284
              </p>

               
                 <h5 className="font-bold text-white text-lg mb-8 uppercase tracking-wider mt-12">
                Branch Office UK
              </h5>
              <p className="text-gray-400 font-medium">
               Queensberry House 106 Queens Road, Brighton, BN13XF, United Kingdom
              </p>
              <p className="text-[#b366ff] font-bold mt-2">
               +44 7520 644714
              </p>

            </div>
        </div>

        {/* Bottom */}
        <div className="border-t border-white/5 pt-10">
          <div className="flex flex-col md:flex-row justify-between items-center gap-6">
            <p className="text-gray-500 font-medium">
              Copyright © 2025 Shakuniya Solutions. All rights reserved.
            </p>
            <div className="flex flex-wrap justify-center gap-8 text-sm text-gray-500 font-medium">
              <a href="#" className="hover:text-white transition-colors">
                Terms & Conditions
              </a>
              <a href="#" className="hover:text-white transition-colors">
                Privacy policy
              </a>
              <a href="#" className="hover:text-white transition-colors">
                Cancellation & Refund Policy
              </a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
