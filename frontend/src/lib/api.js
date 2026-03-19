// lib/api.js
// ✅ Centralised API client — connects every admin page to Laravel backend

const BASE = process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

// ── helpers ───────────────────────────────────────────────────────────────────

function getToken() {
  if (typeof window === "undefined") return null;
  return localStorage.getItem("admin_token") || localStorage.getItem("auth_token");
}

async function request(endpoint, options = {}) {
  const url = `${BASE}${endpoint}`;
  const token = getToken();

  const headers = {
    Accept: "application/json",
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
    ...options.headers,
  };

  // Don't set Content-Type for FormData (let browser set it with boundary)
  if (!(options.body instanceof FormData)) {
    headers["Content-Type"] = "application/json";
  }

  const res = await fetch(url, { ...options, headers, credentials: 'include' });

  // Try to parse JSON even on error responses
  let data;
  try {
    data = await res.json();
  } catch {
    data = { success: false, message: `HTTP ${res.status}` };
  }

  if (!res.ok) {
    const msg = data?.message || data?.error || `HTTP ${res.status}`;
    throw new Error(msg);
  }

  return data;
}

const get    = (ep)       => request(ep, { method: "GET" });
const post   = (ep, body) => request(ep, { method: "POST",   body: body instanceof FormData ? body : JSON.stringify(body) });
const put    = (ep, body) => request(ep, { method: "PUT",    body: body instanceof FormData ? body : JSON.stringify(body) });
const patch  = (ep, body) => request(ep, { method: "PATCH",  body: JSON.stringify(body) });
const del    = (ep)       => request(ep, { method: "DELETE" });

// ── auth ──────────────────────────────────────────────────────────────────────
export const auth = {
  login:   (email, password) => post("/login",    { email, password }),
  logout:  ()                => post("/logout",   {}),
  getUser: ()                => get("/user"),
};

// ── dashboard ─────────────────────────────────────────────────────────────────
export const dashboard = {
  getData:  () => get("/admin/dashboard"),
  getStats: () => get("/admin/dashboard/stats"),
};

// ── blogs ─────────────────────────────────────────────────────────────────────
export const blogs = {
  list:   (params = {}) => get(`/blogs?${new URLSearchParams(params)}`),
  get:    (id)          => get(`/blogs/${id}`),
  create: (data)        => post("/blogs", data),

  // update using POST + _method in FormData
  update: (id, data) => post(`/blogs/${id}`, data),

  remove: (id)       => del(`/blogs/${id}`),
};

// ── faqs ──────────────────────────────────────────────────────────────────────
export const faqs = {
  list:         (params = {}) => get(`/faqs?${new URLSearchParams(params)}`),
  listAll:      ()            => get("/faqs?active=false&per_page=100"),
  create:       (data)        => post("/faqs", data),
  update:       (id, data)    => put(`/faqs/${id}`, data),
  remove:       (id)          => del(`/faqs/${id}`),
  toggleStatus: (id)          => patch(`/faqs/${id}/toggle-status`),
};

// ── gallery ───────────────────────────────────────────────────────────────────
export const gallery = {
  list:   (params = {}) => get(`/gallery-images?${new URLSearchParams(params)}`),
  create: (formData)    => post("/gallery-images", formData),
  update: (id, data)    => post(`/gallery-images/${id}?_method=PUT`, data),
  remove: (id)          => del(`/gallery-images/${id}`),
};

// ── products ──────────────────────────────────────────────────────────────────
export const products = {
  list:   (params = {}) => get(`/products?${new URLSearchParams(params)}`),
  get:    (id)          => get(`/products/${id}`),
  create: (data)        => post("/products", data),
  update: (id, data)    => post(`/products/${id}?_method=PUT`, data), // FormData workaround
  remove: (id)          => del(`/products/${id}`),
  toggleActive: (id)    => patch(`/products/${id}/toggle-active`),
  reorder: (orders)     => post("/products/reorder", { orders }),
  bulkDelete: (ids)     => post("/products/bulk-delete", { ids }),
};

// ── users ─────────────────────────────────────────────────────────────────────
export const users = {
  list:   (params = {}) => get(`/users?${new URLSearchParams(params)}`),
  get:    (id)          => get(`/users/${id}`),
  create: (data)        => post("/users", data),
  update: (id, data)    => put(`/users/${id}`, data),
  remove: (id)          => del(`/users/${id}`),
  toggleStatus: (id)    => patch(`/users/${id}/toggle-status`),
};

// ── services ──────────────────────────────────────────────────────────────────
export const services = {
  list:   (params = {}) => get(`/services?${new URLSearchParams(params)}`),
  get:    (id)          => get(`/services/${id}`),
  create: (data)        => post("/services", data),
  update: (id, data)    => put(`/services/${id}`, data),
  remove: (id)          => del(`/services/${id}`),
  toggleActive: (id)    => patch(`/services/${id}/toggle-active`),
  toggleFeatured: (id)  => patch(`/services/${id}/toggle-featured`),
};

// ── categories ────────────────────────────────────────────────────────────────
export const categories = {
  list:   (params = {}) => get(`/categories?${new URLSearchParams(params)}`),
  get:    (id)          => get(`/categories/${id}`),
  create: (data)        => post("/categories", data),
  update: (id, data)    => put(`/categories/${id}`, data),
  remove: (id)          => del(`/categories/${id}`),
  toggleStatus: (id)    => patch(`/categories/${id}/toggle-status`),
};

// ── career / jobs ─────────────────────────────────────────────────────────────
export const perks = {
  list:   ()            => get("/perks"),
  get:    (id)          => get(`/perks/${id}`),
  create: (data)        => post("/perks", data),
  update: (id, data)    => put(`/perks/${id}`, data),
  remove: (id)          => del(`/perks/${id}`),
};

export const jobs = {
  list:   (params = {}) => get(`/jobs?${new URLSearchParams(params)}`),
  get:    (id)          => get(`/jobs/${id}`),
  create: (data)        => post("/jobs", data),
  update: (id, data)    => put(`/jobs/${id}`, data),
  remove: (id)          => del(`/jobs/${id}`),
  stats:  ()            => get("/jobs/stats"),
};

// ── enquiries / contact messages ──────────────────────────────────────────────
export const contactMessages = {
  list:         (params = {}) => get(`/contact-messages?${new URLSearchParams(params)}`),
  get:          (id)          => get(`/contact-messages/${id}`),
  create:       (data)        => post("/contact-messages", data),
  updateStatus: (id, status)  => patch(`/contact-messages/${id}/status`, { status }),
  remove:       (id)          => del(`/contact-messages/${id}`),
  stats:        ()            => get("/contact-messages/stats"),
};

// ── default export ────────────────────────────────────────────────────────────
const apiClient = {
  auth,
  dashboard,
  blogs,
  faqs,
  gallery,
  products,
  users,
  services,
  categories,
  perks,
  jobs,
  contactMessages,
  // Backward compatibility with frontend components
  getBlogs: (params) => blogs.list(params),
  getBlogBySlug: (slug) => blogs.get(slug),
  getProducts: (params) => products.list(params),
  getProductBySlug: (slug) => get(`/products/slug/${slug}`),
  getCategories: () => get("/categories"),
  getServices: () => get("/services"),
};

export default apiClient;
