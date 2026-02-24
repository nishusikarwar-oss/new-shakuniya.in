import {
  Smartphone,
  Apple,
  Globe,
  Code,
  Headphones,
  Video,
  Handbag,
  Bot,
} from "lucide-react";

export const services = [
  {
    id: "1",
    title: "Android Development",
    slug: "android",
    shortDescription: "Native Android apps with cutting-edge technology",
    fullDescription:
      "We build high-performance, scalable Android applications using the latest technologies and best practices. Our team specializes in creating intuitive, user-friendly mobile experiences that drive engagement and business growth.",
    icon: Smartphone,
    features: [
      "Native Android Development",
      "Kotlin & Java Expertise",
      "Material Design Implementation",
      "API Integration",
      "Performance Optimization",
      "Play Store Publishing",
    ],
  },
  {
    id: "2",
    title: "iOS Development",
    slug: "ios",
    shortDescription: "Premium iOS applications for Apple devices",
    fullDescription:
      "We create stunning iOS applications that leverage the full power of Apple's ecosystem. From iPhone to iPad, our apps deliver seamless experiences with beautiful interfaces and robust functionality.",
    icon: Apple,
    features: [
      "Native iOS Development",
      "Swift & SwiftUI",
      "Human Interface Guidelines",
      "ARKit & Core ML Integration",
      "App Store Optimization",
      "Enterprise Solutions",
    ],
  },
  {
    id: "3",
    title: "Website Development",
    slug: "website",
    shortDescription: "Modern, responsive web solutions",
    fullDescription:
      "We develop modern, responsive websites that captivate users and drive conversions. From corporate sites to e-commerce platforms, we deliver web solutions that perform beautifully across all devices.",
    icon: Globe,
    features: [
      "Responsive Design",
      "React & Next.js",
      "E-commerce Solutions",
      "CMS Integration",
      "SEO Optimization",
      "Performance Tuning",
    ],
  },
  {
    id: "4",
    title: "Software Development",
    slug: "software",
    shortDescription: "Custom software systems tailored to your needs",
    fullDescription:
      "We design and develop custom software solutions that streamline operations and boost productivity. Our software is built to scale with your business and adapt to your evolving needs.",
    icon: Code,
    features: [
      "Custom Software Solutions",
      "Enterprise Applications",
      "Cloud Integration",
      "Database Design",
      "System Architecture",
      "Legacy Modernization",
    ],
  },
  {
    id: "5",
    title: "IT Consultant Service",
    slug: "it-consultant",
    shortDescription: "Expert IT consulting and strategic guidance",
    fullDescription:
      "Our IT consulting services help businesses navigate the complex technology landscape. We provide strategic guidance, technology assessments, and implementation support to help you achieve your digital transformation goals.",
    icon: Headphones,
    features: [
      "Technology Assessment",
      "Digital Strategy",
      "Infrastructure Planning",
      "Security Consulting",
      "Cloud Migration",
      "Project Management",
    ],
  },
  {
    id: "6",
    title: "Live Streaming Apps",
    slug: "live-streaming",
    shortDescription: "Expert IT consulting and strategic guidance",
    fullDescription:
      "Our IT consulting services help businesses navigate the complex technology landscape. We provide strategic guidance, technology assessments, and implementation support to help you achieve your digital transformation goals.",
    icon: Video,
    features: [
      "Technology Assessment",
      "Digital Strategy",
      "Infrastructure Planning",
      "Security Consulting",
      "Cloud Migration",
      "Project Management",
    ],
  },
  {
    id: "7",
    title: "Audio Streaming Apps",
    slug: "audio-streaming",
    shortDescription: "Expert IT consulting and strategic guidance",
    fullDescription:
      "Our IT consulting services help businesses navigate the complex technology landscape. We provide strategic guidance, technology assessments, and implementation support to help you achieve your digital transformation goals.",
    icon: Headphones,
    features: [
      "Technology Assessment",
      "Digital Strategy",
      "Infrastructure Planning",
      "Security Consulting",
      "Cloud Migration",
      "Project Management",
    ],
  },
  {
    id: "8",
    title: "Shopping Apps",
    slug: "sopping-apps",
    shortDescription: "Expert IT consulting and strategic guidance",
    fullDescription:
      "Our IT consulting services help businesses navigate the complex technology landscape. We provide strategic guidance, technology assessments, and implementation support to help you achieve your digital transformation goals.",
    icon: Handbag,
    features: [
      "Technology Assessment",
      "Digital Strategy",
      "Infrastructure Planning",
      "Security Consulting",
      "Cloud Migration",
      "Project Management",
    ],
  },
  {
    id: "9",
    title: "Social Media Apps",
    slug: "social-media",
    shortDescription: "Expert IT consulting and strategic guidance",
    fullDescription:
      "Our IT consulting services help businesses navigate the complex technology landscape. We provide strategic guidance, technology assessments, and implementation support to help you achieve your digital transformation goals.",
    icon: Bot,
    features: [
      "Technology Assessment",
      "Digital Strategy",
      "Infrastructure Planning",
      "Security Consulting",
      "Cloud Migration",
      "Project Management",
    ],
  },
];

// Get first N services for dropdown preview
export const getDropdownServices = (count = 4) => {
  return services.slice(0, count);
};

// Get all services
export const getAllServices = () => {
  return services;
};

// Get service by slug
export const getServiceBySlug = (slug) => {
  return services.find((service) => service.slug === slug);
};

// Check if service exists
export const serviceExists = (slug) => {
  return services.some((service) => service.slug === slug);
};
