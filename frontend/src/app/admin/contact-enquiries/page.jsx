"use client";

// pages/contact-enquiries.js or app/contact-enquiries/page.js
import { useState, useEffect } from 'react';
import { 
  FiMail, 
  FiPhone, 
  FiUser, 
  FiBriefcase, 
  FiMessageCircle,
  FiCheckCircle,
  FiXCircle,
  FiClock,
  FiSearch,
  FiFilter,
  FiDownload,
  FiEye,
  FiStar,
  FiMoreVertical,
  FiRefreshCw
} from 'react-icons/fi';

export default function ContactEnquiries() {
  const [enquiries, setEnquiries] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [selectedEnquiry, setSelectedEnquiry] = useState(null);
  const [stats, setStats] = useState({
    total: 0,
    pending: 0,
    contacted: 0,
    resolved: 0
  });

  // Fetch enquiries from API
  const fetchEnquiries = async () => {
    setLoading(true);
    try {
      const response = await fetch('http://127.0.0.1:8000/api/contact-inquiries');
      const data = await response.json();
      
      if (data.success) {
        // Handle different possible response structures
        let enquiriesData = [];
        
        // Check if data.data is an array (direct array)
        if (Array.isArray(data.data)) {
          enquiriesData = data.data;
        } 
        // Check if data.data has a data property that is an array (paginated response)
        else if (data.data && Array.isArray(data.data.data)) {
          enquiriesData = data.data.data;
        }
        // Check if data.data is an object with enquiries
        else if (data.data && Array.isArray(data.data.enquiries)) {
          enquiriesData = data.data.enquiries;
        }
        
        setEnquiries(enquiriesData);
        calculateStats(enquiriesData);
      }
    } catch (error) {
      console.error('Error fetching enquiries:', error);
      // Set empty array on error to prevent filter errors
      setEnquiries([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchEnquiries();
  }, []);

  const calculateStats = (data) => {
    // Ensure data is an array before calculating stats
    if (!Array.isArray(data)) {
      setStats({ total: 0, pending: 0, contacted: 0, resolved: 0 });
      return;
    }
    
    const stats = {
      total: data.length,
      pending: data.filter(item => item && item.status === 'pending').length,
      contacted: data.filter(item => item && item.status === 'contacted').length,
      resolved: data.filter(item => item && item.status === 'resolved').length
    };
    setStats(stats);
  };

  const getStatusColor = (status) => {
    switch(status) {
      case 'pending':
        return 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30';
      case 'contacted':
        return 'bg-blue-500/20 text-blue-400 border-blue-500/30';
      case 'resolved':
        return 'bg-green-500/20 text-green-400 border-green-500/30';
      default:
        return 'bg-gray-500/20 text-gray-400 border-gray-500/30';
    }
  };

  const getStatusIcon = (status) => {
    switch(status) {
      case 'pending':
        return <FiClock className="w-3 h-3" />;
      case 'contacted':
        return <FiCheckCircle className="w-3 h-3" />;
      case 'resolved':
        return <FiCheckCircle className="w-3 h-3" />;
      default:
        return <FiClock className="w-3 h-3" />;
    }
  };

  // Safely filter enquiries with null checks
  const filteredEnquiries = Array.isArray(enquiries) 
    ? enquiries.filter(enquiry => {
        if (!enquiry) return false;
        
        const matchesSearch = 
          (enquiry.name?.toLowerCase() || '').includes(searchTerm.toLowerCase()) ||
          (enquiry.email?.toLowerCase() || '').includes(searchTerm.toLowerCase()) ||
          (enquiry.company_name?.toLowerCase() || '').includes(searchTerm.toLowerCase()) ||
          (enquiry.service_interest?.toLowerCase() || '').includes(searchTerm.toLowerCase());
        
        const matchesStatus = statusFilter === 'all' || enquiry.status === statusFilter;
        
        return matchesSearch && matchesStatus;
      })
    : [];

  const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  return (
    <div className="min-h-screen overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-gray-100">
      {/* Sidebar */}
      <div className="fixed left-0 top-0 h-full w-64 bg-gray-900/50 backdrop-blur-xl border-r border-gray-700/50 p-6 overflow-y-auto">
        <div className="flex items-center gap-3 mb-8">
          <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg"></div>
          <h1 className="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
            Admin
          </h1>
        </div>
        
        <nav className="space-y-2">
          {[
            'Dashboard',
            'Users',
            'Products',
            'Services',
            'FAQ',
            'Gallery',
            'Blogs',
            'Carrer',
            'Contact Messages',
            'Contact Enquiries'
          ].map((item, index) => (
            <a
              key={index}
              href="#"
              className={`flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 ${
                item === 'Contact Enquiries'
                  ? 'bg-gradient-to-r from-blue-600/20 to-purple-600/20 text-blue-400 border-l-4 border-blue-500'
                  : 'text-gray-400 hover:bg-gray-800/50 hover:text-gray-200'
              }`}
            >
              <span className="text-sm">{item}</span>
            </a>
          ))}
        </nav>
        
        <div className="absolute bottom-6 left-6 right-6">
          <div className="flex items-center gap-3 px-4 py-3 bg-gray-800/50 rounded-lg border border-gray-700/50">
            <FiMail className="text-gray-400" />
            <span className="text-sm text-gray-300">admin@example.com</span>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="overflow-x-hidden">
        {/* Header */}
        <div className="flex justify-between items-center mb-8">
          <div>
            <h2 className="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
              Contact Enquiries
            </h2>
            <p className="text-gray-400 mt-1">Manage and respond to customer inquiries</p>
          </div>
          
          <div className="flex gap-3">
            <button 
              onClick={fetchEnquiries}
              className="p-3 bg-gray-800/50 hover:bg-gray-700/50 rounded-lg border border-gray-700/50 text-gray-300 transition-all duration-200"
            >
              <FiRefreshCw className={`w-5 h-5 ${loading ? 'animate-spin' : ''}`} />
            </button>
            <button className="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg text-white font-medium hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
              <FiDownload className="w-4 h-4" />
              Export
            </button>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          {[
            { label: 'Total Enquiries', value: stats.total, icon: FiMail },
            { label: 'Pending', value: stats.pending, icon: FiClock },
            { label: 'Contacted', value: stats.contacted, icon: FiCheckCircle },
            { label: 'Resolved', value: stats.resolved, icon: FiStar }
          ].map((stat, index) => (
            <div key={index} className="bg-gray-800/30 backdrop-blur-xl rounded-xl border border-gray-700/50 p-6 hover:border-gray-600/50 transition-all duration-200">
              <div className="flex justify-between items-start">
                <div>
                  <p className="text-gray-400 text-sm">{stat.label}</p>
                  <p className="text-3xl font-bold mt-2 text-white">
                    {stat.value}
                  </p>
                </div>
                <div className="p-3 bg-gray-700/30 rounded-lg">
                  <stat.icon className="w-5 h-5 text-gray-400" />
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Filters */}
        <div className="bg-gray-800/30 backdrop-blur-xl rounded-xl border border-gray-700/50 p-6 mb-6">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="flex-1 relative">
              <FiSearch className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" />
              <input
                type="text"
                placeholder="Search by name, email, company..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pl-12 pr-4 py-3 bg-gray-900/50 border border-gray-700/50 rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50"
              />
            </div>
            
            <div className="flex gap-2">
              <div className="relative">
                <FiFilter className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <select
                  value={statusFilter}
                  onChange={(e) => setStatusFilter(e.target.value)}
                  className="pl-12 pr-8 py-3 bg-gray-900/50 border border-gray-700/50 rounded-lg text-gray-100 appearance-none cursor-pointer focus:outline-none focus:border-blue-500/50"
                >
                  <option value="all">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="contacted">Contacted</option>
                  <option value="resolved">Resolved</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        {/* Enquiries Table */}
        <div className="bg-gray-800/30 backdrop-blur-xl rounded-xl border border-gray-700/50 overflow-hidden">
          {loading ? (
            <div className="flex justify-center items-center h-64">
              <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>
          ) : (
            <div className="overflow-x-auto">
              {filteredEnquiries.length === 0 ? (
                <div className="text-center py-16">
                  <p className="text-gray-400 text-lg">No enquiries found</p>
                </div>
              ) : (
                <table className="w-full table-fixed text-center">
                  <thead>
                    <tr className="border-b border-gray-700/50">
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">ID</th>
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">Name</th>
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">Company</th>
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">Email</th>
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">Phone</th>
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">Service</th>
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">Status</th>
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">Date</th>
                      <th className="text-left py-4 px-6 text-gray-400 font-medium text-sm">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="text-left">
                    {filteredEnquiries.map((enquiry) => (
                      <tr 
                        key={enquiry?.inquiry_id || Math.random()} 
                        className="border-b border-gray-700/50 hover:bg-gray-700/20 transition-all duration-200 cursor-pointer"
                        onClick={() => setSelectedEnquiry(enquiry)}
                      >
                        <td className="py-4 px-6 text-sm">#{enquiry?.inquiry_id || 'N/A'}</td>
                        <td className="py-4 px-6">
                          <div className="flex items-center gap-3">
                            <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                              <FiUser className="w-4 h-4 text-white" />
                            </div>
                            <span className="font-medium">{enquiry?.name || 'N/A'}</span>
                          </div>
                        </td>
                        <td className="py-4 px-6 text-sm text-gray-300">{enquiry?.company_name || 'N/A'}</td>
                        <td className="py-4 px-6 text-sm text-gray-300">{enquiry?.email || 'N/A'}</td>
                        <td className="py-4 px-6 text-sm text-gray-300">{enquiry?.phone || 'N/A'}</td>
                        <td className="py-4 px-6">
                          <span className="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-full text-xs border border-blue-500/20">
                            {enquiry?.service_interest || 'N/A'}
                          </span>
                        </td>
                        <td className="py-4 px-6">
                          <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs border ${getStatusColor(enquiry?.status)}`}>
                            {getStatusIcon(enquiry?.status)}
                            {enquiry?.status ? enquiry.status.charAt(0).toUpperCase() + enquiry.status.slice(1) : 'Unknown'}
                          </span>
                        </td>
                        <td className="py-4 px-6 text-sm text-gray-400">{formatDate(enquiry?.created_at)}</td>
                        <td className="py-4 px-6">
                          <div className="flex items-center gap-2">
                            <button className="p-2 hover:bg-gray-700/50 rounded-lg transition-all duration-200">
                              <FiEye className="w-4 h-4 text-gray-400" />
                            </button>
                            <button className="p-2 hover:bg-gray-700/50 rounded-lg transition-all duration-200">
                              <FiMoreVertical className="w-4 h-4 text-gray-400" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </div>
          )}
        </div>

        {/* Pagination */}
        <div className="flex justify-between items-center mt-6">
          <p className="text-sm text-gray-400">
            Showing {filteredEnquiries.length} of {Array.isArray(enquiries) ? enquiries.length : 0} enquiries
          </p>
          <div className="flex gap-2">
            <button className="px-4 py-2 bg-gray-800/50 border border-gray-700/50 rounded-lg text-gray-300 hover:bg-gray-700/50 transition-all duration-200">
              Previous
            </button>
            <button className="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg text-white font-medium hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
              1
            </button>
            <button className="px-4 py-2 bg-gray-800/50 border border-gray-700/50 rounded-lg text-gray-300 hover:bg-gray-700/50 transition-all duration-200">
              Next
            </button>
          </div>
        </div>

        {/* Enquiry Details Modal */}
        {selectedEnquiry && (
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div className="bg-gray-800 rounded-xl border border-gray-700/50 w-full max-w-2xl max-h-[80vh] overflow-y-auto">
              <div className="p-6 border-b border-gray-700/50">
                <div className="flex justify-between items-center">
                  <h3 className="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Enquiry Details #{selectedEnquiry?.inquiry_id || 'N/A'}
                  </h3>
                  <button 
                    onClick={() => setSelectedEnquiry(null)}
                    className="p-2 hover:bg-gray-700/50 rounded-lg transition-all duration-200"
                  >
                    <FiXCircle className="w-5 h-5 text-gray-400" />
                  </button>
                </div>
              </div>
              
              <div className="p-6 space-y-4">
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <p className="text-sm text-gray-400 mb-1">Name</p>
                    <p className="font-medium">{selectedEnquiry?.name || 'N/A'}</p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-400 mb-1">Company</p>
                    <p className="font-medium">{selectedEnquiry?.company_name || 'N/A'}</p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-400 mb-1">Email</p>
                    <p className="font-medium">{selectedEnquiry?.email || 'N/A'}</p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-400 mb-1">Phone</p>
                    <p className="font-medium">{selectedEnquiry?.phone || 'N/A'}</p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-400 mb-1">Service Interest</p>
                    <span className="inline-block px-3 py-1 bg-blue-500/10 text-blue-400 rounded-full text-xs border border-blue-500/20">
                      {selectedEnquiry?.service_interest || 'N/A'}
                    </span>
                  </div>
                  <div>
                    <p className="text-sm text-gray-400 mb-1">Status</p>
                    <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs border ${getStatusColor(selectedEnquiry?.status)}`}>
                      {getStatusIcon(selectedEnquiry?.status)}
                      {selectedEnquiry?.status ? selectedEnquiry.status.charAt(0).toUpperCase() + selectedEnquiry.status.slice(1) : 'Unknown'}
                    </span>
                  </div>
                </div>
                
                <div>
                  <p className="text-sm text-gray-400 mb-1">Message</p>
                  <div className="p-4 bg-gray-900/50 rounded-lg border border-gray-700/50">
                    <p className="text-gray-300">{selectedEnquiry?.message || 'No message provided'}</p>
                  </div>
                </div>
                
                <div>
                  <p className="text-sm text-gray-400 mb-1">Additional Information</p>
                  <div className="grid grid-cols-2 gap-4 p-4 bg-gray-900/50 rounded-lg border border-gray-700/50">
                    <div>
                      <p className="text-xs text-gray-500">IP Address</p>
                      <p className="text-sm text-gray-300">{selectedEnquiry?.ip_address || 'N/A'}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">User Agent</p>
                      <p className="text-sm text-gray-300 truncate" title={selectedEnquiry?.user_agent}>
                        {selectedEnquiry?.user_agent || 'N/A'}
                      </p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Created At</p>
                      <p className="text-sm text-gray-300">{formatDate(selectedEnquiry?.created_at)}</p>
                    </div>
                    <div>
                      <p className="text-xs text-gray-500">Last Updated</p>
                      <p className="text-sm text-gray-300">{formatDate(selectedEnquiry?.updated_at)}</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div className="p-6 border-t border-gray-700/50 flex justify-end gap-3">
                <button className="px-4 py-2 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg text-gray-300 transition-all duration-200">
                  Mark as Contacted
                </button>
                <button className="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg text-white font-medium hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                  Reply via Email
                </button>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}