
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css"

import { AuthProvider } from "@/context/AuthContext";
import { ToastProvider } from "@/hooks/use-toast";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

export const metadata = {
  title: "Shakuniya Solutions | Home",
  description: "We craft your business website as a powerful marketing tool",
  icons: {
    icon: "/logo.png",
  },
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body
        className={`${geistSans.variable} ${geistMono.variable} antialiased`}
      >
        <AuthProvider>
          <ToastProvider>
            {/* <Navbar /> */}
            {children}
          </ToastProvider>
        </AuthProvider>
      </body>
    </html>
  );
}
