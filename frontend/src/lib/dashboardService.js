// // src/lib/dashboardService.js
// // =====================================================================
// // All dashboard-related API calls, matching the Laravel routes in
// // routes/admin.php + routes/api.php.
// // =====================================================================

// import apiClient from "./apiClient";

// const dashboardService = {
//   /**
//    * GET /api/admin/dashboard
//    * Full dashboard payload: stats_cards, recent_activities,
//    * chart_data, quick_stats, module_counts, system_info
//    */
//   async getDashboard() {
//     const response = await apiClient.get("/admin/dashboard");
//     return response.data?.data || {};
//   },

//   /**
//    * GET /api/admin/dashboard/stats
//    * Just the stats cards array (lighter call for periodic refresh).
//    */
//   async getStats() {
//     const response = await apiClient.get("/admin/dashboard/stats");
//     return response.data?.data || [];
//   },

//   /**
//    * GET /api/admin/dashboard/stats/users
//    */
//   async getUserStats() {
//     const response = await apiClient.get("/admin/dashboard/stats/users");
//     return response.data || {};
//   },

//   /**
//    * GET /api/admin/dashboard/stats/products
//    */
//   async getProductStats() {
//     const response = await apiClient.get("/admin/dashboard/stats/products");
//     return response.data || {};
//   },

//   /**
//    * GET /api/admin/dashboard/stats/blogs
//    */
//   async getBlogStats() {
//     const response = await apiClient.get("/admin/dashboard/stats/blogs");
//     return response.data || {};
//   },

//   /**
//    * GET /api/admin/dashboard/stats/career
//    */
//   async getCareerStats() {
//     const response = await apiClient.get("/admin/dashboard/stats/career");
//     return response.data || {};
//   },

//   /**
//    * GET /api/admin/dashboard/module/{module}
//    * e.g. module = 'users' | 'products' | 'services' | 'blogs' | 'career' | 'gallery'
//    */
//   async getModuleStats(module) {
//     const response = await apiClient.get(`/admin/dashboard/module/${module}`);
//     return response.data || {};
//   },
// };

// export default dashboardService;
