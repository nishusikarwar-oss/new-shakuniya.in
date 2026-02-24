import {
  Smartphone,
  Apple,
  Globe,
  Code,
} from "lucide-react";

/**
 * MASTER PRODUCT DATA
 * Single source of truth
 */
export const products = [
  {
    id: 1,
    title: "Vyaparbot",
    slug: "vyaparbot",
    shortDescription:
      "Native Vyaparbot Product with cutting-edge technology",
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
    id: 2,
    title: "Niya Meet",
    slug: "niya-meet",
    shortDescription:
      "Premium iOS applications for Apple devices",
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
    id: 3,
    title: "EWS",
    slug: "ews",
    shortDescription:
      "Modern, responsive web solutions",
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
    id: 4,
    title: "Bio Linker",
    slug: "bio-linker",
    shortDescription:
      "Custom software systems tailored to your needs",
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
];


/* ===============================
   DYNAMIC HELPERS
================================ */

// Get all products
export const getAllProducts = () => products;


// Get product by slug
export const getProductBySlug = (slug) =>
  products.find((product) => product.slug === slug);


// Get dropdown products (limit)
export const getDropdownProducts = (limit = 4) =>
  products.slice(0, limit);


// Check if product exists
export const productExists = (slug) =>
  products.some((product) => product.slug === slug);


// Generate static params (for Next.js SSG)
export const generateProductParams = () =>
  products.map((product) => ({
    slug: product.slug,
  }));
