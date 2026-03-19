const fs = require('fs');
const path = require('path');
const file = path.join(__dirname, '../src/lib/api.js');
let src = fs.readFileSync(file, 'utf8');
if (/export const services\b/.test(src)) {
  console.log('services export already exists');
  process.exit(0);
}

const append = `
export const services = {
  list: (params = {}) => apiClient.get(` + "`" + "/services?${new URLSearchParams(params)}" + "`" + `),
  get: (id) => apiClient.get(` + "`" + "/services/${id}" + "`" + `),
  create: (data) => apiClient.post("/services", data),
  update: (id, data) => apiClient.put(` + "`" + "/services/${id}" + "`" + `, data),
  remove: (id) => apiClient.delete(` + "`" + "/services/${id}" + "`" + `),
};
`;

fs.appendFileSync(file, append);
console.log('Appended services export to', file);
