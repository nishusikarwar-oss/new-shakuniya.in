 // lib/api.js (या जहाँ आपका apiClient है)
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL;

console.log("🔥 API_BASE_URL from env:", API_BASE_URL);

class ApiClient {
  constructor() {
    this.baseURL = API_BASE_URL;
    console.log("🔥 ApiClient baseURL:", this.baseURL);
    this.blogsCache = null;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    
    console.log("🔥 Request URL:", url);

    const defaultHeaders = {
      "Content-Type": "application/json",
      Accept: "application/json",
    };

    const token =
      typeof window !== "undefined" ? localStorage.getItem("token") : null;

    if (token) {
      defaultHeaders["Authorization"] = `Bearer ${token}`;
    }

    const config = {
      ...options,
      headers: {
        ...defaultHeaders,
        ...options.headers,
      },
      credentials: "include",
    };

    try {
      const response = await fetch(url, config);

      // First check if response is ok
      if (!response.ok) {
        // Try to get error text
        const errorText = await response.text();
        let errorMessage;
        try {
          const errorData = errorText ? JSON.parse(errorText) : {};
          errorMessage = errorData.message || `HTTP error! status: ${response.status}`;
        } catch {
          errorMessage = errorText || `HTTP error! status: ${response.status}`;
        }
        throw new Error(errorMessage);
      }

      // Check content-type to determine how to handle response
      const contentType = response.headers.get('content-type');
      
      // If response is JSON
      if (contentType && contentType.includes('application/json')) {
        const text = await response.text();
        
        // If response is empty, return empty object/array based on endpoint
        if (!text || text.trim() === '') {
          console.log("⚠️ Empty JSON response from server for:", endpoint);
          // Return appropriate empty structure based on endpoint
          if (endpoint.includes('/blogs/') && !endpoint.endsWith('/blogs')) {
            return null; // Single blog should return null
          } else if (endpoint.includes('/blogs')) {
            return []; // Blogs list should return empty array
          }
          return {};
        }

        // Parse JSON
        try {
          return JSON.parse(text);
        } catch (parseError) {
          console.error("❌ JSON parse error:", parseError, "Response text:", text);
          throw new Error("Invalid JSON response from server");
        }
      } else {
        // For non-JSON responses, return text
        const text = await response.text();
        console.log("📝 Non-JSON response:", text.substring(0, 100) + "...");
        return text;
      }
      
    } catch (error) {
      console.error(`Request failed for ${url}:`, error);
      throw error;
    }
  }

  get(endpoint) {
    return this.request(endpoint, {
      method: "GET",
    });
  }

  post(endpoint, data) {
    return this.request(endpoint, {
      method: "POST",
      body: JSON.stringify(data),
    });
  }

  put(endpoint, data) {
    return this.request(endpoint, {
      method: "PUT",
      body: JSON.stringify(data),
    });
  }

  delete(endpoint) {
    return this.request(endpoint, {
      method: "DELETE",
    });
  }

  // 📌 Blog specific methods
  async getBlogs(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const endpoint = queryString ? `/blogs?${queryString}` : '/blogs';
    
    try {
      const response = await this.get(endpoint);
      console.log("📚 getBlogs response:", response);
      
      // Handle different response structures
      let blogsData = [];
      
      if (response && response.data && Array.isArray(response.data)) {
        // Laravel API structure: { data: [...] }
        blogsData = response.data;
      } else if (Array.isArray(response)) {
        // Direct array response
        blogsData = response;
      } else if (response && typeof response === 'object') {
        // Try to find array in response
        const possibleArrays = Object.values(response).find(Array.isArray);
        if (possibleArrays) {
          blogsData = possibleArrays;
        }
      }
      
      // Cache the blogs data
      if (blogsData.length > 0) {
        this.blogsCache = blogsData;
      }
      
      // Return in consistent format
      return { 
        success: true, 
        data: blogsData 
      };
      
    } catch (error) {
      console.error("Error fetching blogs:", error);
      return { success: false, data: [], error: error.message };
    }
  }

  async getBlogBySlug(slug) {
    console.log("🔍 Fetching blog with slug:", slug);
    
    try {
      // Direct API call to single blog endpoint
      const response = await this.get(`/blogs/${slug}`);
      console.log("📡 Blog response:", response);
      
      // Check if we got valid data
      if (response && Object.keys(response).length > 0) {
        console.log("✅ Blog fetched successfully");
        
        // Handle different response structures
        if (response.data) {
          return { success: true, data: response.data };
        } else if (response.id || response.slug) {
          // Direct blog object
          return { success: true, data: response };
        }
      }
      
      // If response is empty or invalid
      console.log("⚠️ Empty or invalid blog response");
      return { 
        success: false, 
        error: "Blog not found",
        data: null 
      };
      
    } catch (error) {
      console.error("❌ Error in getBlogBySlug:", error);
      
      // Return a properly formatted error
      return { 
        success: false, 
        error: error.message,
        data: null 
      };
    }
  }

  async getLatestBlogs(limit = 4) {
    try {
      const response = await this.getBlogs({ latest: true, limit });
      return response;
    } catch (error) {
      console.error("Error fetching latest blogs:", error);
      return { success: false, data: [] };
    }
  }

  // Clear cache if needed
  clearCache() {
    this.blogsCache = null;
  }
}

const apiClient = new ApiClient();
export default apiClient;