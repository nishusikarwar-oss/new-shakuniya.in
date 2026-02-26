"use client";

import { useState } from "react";
import Link from "next/link";
import { Menu, X, ChevronDown } from "lucide-react";
import logo from "@/images/logo.png";

import ServicesDropdown from "./ServicesDropdown";
import ProductsDropdown from "./ProductsDropdown";

const navLinks = [
  { name: "Home", href: "/" },
  { name: "About", href: "/about" },
   { name: "Gallery", href: "/gallery" },
  { name: "Services" },
  { name: "Products" },
  { name: "Career", href: "/career" },
  { name: "Contact", href: "/contact" },
];

export default function Navbar() {
  const [isOpen, setIsOpen] = useState(false);
  const [openDropdown, setOpenDropdown] = useState(null);

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 bg-[#0B1221] border-b border-white/5">
      <div className="container mx-auto px-4 lg:px-8">
        <div className="flex items-center justify-between h-20">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2">
            <div className="w-14 h-14">
              <img
                src={logo.src}
                alt="Logo"
                className="w-full h-full object-cover"
              />
            </div>
          </Link>

          {/* ================= Desktop Navigation ================= */}
          <div className="hidden lg:flex items-center gap-10">
            {navLinks.map((link) =>
              link.name === "Services" ? (
                <ServicesDropdown key="services" />
              ) : link.name === "Products" ? (
                <ProductsDropdown key="products" />
              ) : (
                <Link
                  key={link.name}
                  href={link.href}
                  className="text-white/60 hover:text-white transition-colors duration-300 text-base font-medium"
                >
                  {link.name}
                </Link>
              )
            )}
          </div>

          {/* ================= Mobile Menu Button ================= */}
          <button
            onClick={() => setIsOpen(!isOpen)}
            className="lg:hidden text-white p-2"
          >
            {isOpen ? <X size={24} /> : <Menu size={24} />}
          </button>
        </div>

        {/* ================= Mobile Menu (UNCHANGED) ================= */}
        {isOpen && (
          <div className="lg:hidden bg-[#0B1221] border border-white/5 mt-2 p-4 mb-4 rounded-xl">
            <div className="flex flex-col gap-4">
              {navLinks.map((link) =>
                link.name === "Services" || link.name === "Products" ? (
                  <div key={link.name}>
                    <button
                      onClick={() =>
                        setOpenDropdown(
                          openDropdown === link.name ? null : link.name
                        )
                      }
                      className="flex justify-between items-center w-full text-white/80 py-2"
                    >
                      {link.name}
                      <ChevronDown size={16} />
                    </button>

                    {openDropdown === link.name && (
                      <div className="ml-4 mt-2">
                        {link.name === "Services" ? (
                          <ServicesDropdown isMobile />
                        ) : (
                          <ProductsDropdown isMobile />
                        )}
                      </div>
                    )}
                  </div>
                ) : (
                  <Link
                    key={link.name}
                    href={link.href}
                    className="text-white/60 hover:text-white transition py-2"
                    onClick={() => setIsOpen(false)}
                  >
                    {link.name}
                  </Link>
                )
              )}
            </div>
          </div>
        )}
      </div>
    </nav>
  );
}
