// "use client";

// // src/hooks/useDashboard.js
// // =====================================================================
// // Custom hook that fetches the full dashboard payload and exposes
// // loading / error state.  Auto-refreshes every 60 seconds.
// // =====================================================================

// import { useCallback, useEffect, useRef, useState } from "react";
// import dashboardService from "@/lib/dashboardService";

// /**
//  * @param {number} refreshInterval  ms between auto-refreshes (default 60 000)
//  */
// export function useDashboard(refreshInterval = 60_000) {
//   const [data, setData]         = useState(null);
//   const [isLoading, setLoading] = useState(true);
//   const [error, setError]       = useState(null);
//   const timerRef                = useRef(null);

//   const fetch = useCallback(async () => {
//     try {
//       setError(null);
//       const result = await dashboardService.getDashboard();
//       setData(result);
//     } catch (err) {
//       setError(err.message || "Failed to load dashboard data.");
//     } finally {
//       setLoading(false);
//     }
//   }, []);

//   // Initial fetch
//   useEffect(() => {
//     fetch();
//   }, [fetch]);

//   // Auto-refresh
//   useEffect(() => {
//     if (!refreshInterval) return;
//     timerRef.current = setInterval(fetch, refreshInterval);
//     return () => clearInterval(timerRef.current);
//   }, [fetch, refreshInterval]);

//   return { data, isLoading, error, refetch: fetch };
// }

// /**
//  * Lightweight hook — only fetches stats cards.
//  * Use this on the dashboard header to avoid re-fetching everything.
//  */
// export function useDashboardStats() {
//   const [stats, setStats]       = useState([]);
//   const [isLoading, setLoading] = useState(true);
//   const [error, setError]       = useState(null);

//   const fetch = useCallback(async () => {
//     try {
//       setError(null);
//       const result = await dashboardService.getStats();
//       setStats(Array.isArray(result) ? result : []);
//     } catch (err) {
//       setError(err.message || "Failed to load stats.");
//     } finally {
//       setLoading(false);
//     }
//   }, []);

//   useEffect(() => {
//     fetch();
//   }, [fetch]);

//   return { stats, isLoading, error, refetch: fetch };
// }
