// next.config.js
// =====================================================================
// WHAT CHANGED:
//   - Added rewrites() so that /api/* in dev proxies to Laravel :8000
//     This avoids CORS issues entirely during local development.
//     In production, point NEXT_PUBLIC_API_URL at your actual domain.
// =====================================================================

/** @type {import('next').NextConfig} */
const nextConfig = {
  // ── Dev API proxy ────────────────────────────────────────────────────
  // When the browser calls /api/... in development, Next.js forwards the
  // request to http://localhost:8000/api/... (your Laravel server).
  // This proxy is only active in `next dev`; in production use your
  // actual API URL via NEXT_PUBLIC_API_URL env variable.
  async rewrites() {
    const apiUrl = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";
    return [
      {
        source:      "/api/:path*",
        destination: `${apiUrl}/api/:path*`,
      },
    ];
  },

  // ── Image domains ────────────────────────────────────────────────────
    images: {
    remotePatterns: [
      {
        protocol: "http",
        hostname: "localhost",
        port: "8000",
        pathname: "/storage/**",
      },

      // ✅ Unsplash images
      {
        protocol: "https",
        hostname: "images.unsplash.com",
      },
    ],
  },

  // ── Misc ─────────────────────────────────────────────────────────────
  reactStrictMode: true,
};

module.exports = nextConfig;
