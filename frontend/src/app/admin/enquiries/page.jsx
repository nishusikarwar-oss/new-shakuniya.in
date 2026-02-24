// "use client";

// import { useEffect, useState } from "react";
// import { supabase } from "@/integrations/supabase/client";
// import {
//   Card,
//   CardContent,
//   CardHeader,
//   CardTitle,
// } from "@/components/ui/card";
// import { Button } from "@/components/ui/button";
// import { Input } from "@/components/ui/input";
// import { Search, Mail, Phone, Building } from "lucide-react";
// import {
//   Table,
//   TableBody,
//   TableCell,
//   TableHead,
//   TableHeader,
//   TableRow,
// } from "@/components/ui/table";
// import { Badge } from "@/components/ui/badge";
// import { useToast } from "@/hooks/use-toast";

// const Enquiries = () => {
//   const [enquiries, setEnquiries] = useState([]);
//   const [isLoading, setIsLoading] = useState(true);
//   const [searchQuery, setSearchQuery] = useState("");
//   const { toast } = useToast();

//   useEffect(() => {
//     fetchEnquiries();
//   }, []);

//   const fetchEnquiries = async () => {
//     try {
//       const { data, error } = await supabase
//         .from("enquiries")
//         .select("*")
//         .order("created_at", { ascending: false });

//       if (error) throw error;
//       setEnquiries(data || []);
//     } catch (error) {
//       console.error("Fetch enquiries error:", error);
//       toast({
//         title: "Error",
//         description: "Failed to fetch enquiries",
//         variant: "destructive",
//       });
//     } finally {
//       setIsLoading(false);
//     }
//   };

//   const updateStatus = async (id, status) => {
//     try {
//       const { error } = await supabase
//         .from("enquiries")
//         .update({ status })
//         .eq("id", id);

//       if (error) throw error;

//       setEnquiries((prev) =>
//         prev.map((e) => (e.id === id ? { ...e, status } : e))
//       );

//       toast({
//         title: "Status Updated",
//         description: `Enquiry marked as ${status}`,
//       });
//     } catch (error) {
//       console.error("Update status error:", error);
//       toast({
//         title: "Error",
//         description: "Failed to update status",
//         variant: "destructive",
//       });
//     }
//   };

//   const filteredEnquiries = enquiries.filter(
//     (e) =>
//       e.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
//       e.email.toLowerCase().includes(searchQuery.toLowerCase()) ||
//       e.company?.toLowerCase().includes(searchQuery.toLowerCase())
//   );

//   const getStatusColor = (status) => {
//     switch (status) {
//       case "pending":
//         return "bg-yellow-500/20 text-yellow-400 border-yellow-500/30";
//       case "contacted":
//         return "bg-blue-500/20 text-blue-400 border-blue-500/30";
//       case "resolved":
//         return "bg-green-500/20 text-green-400 border-green-500/30";
//       default:
//         return "bg-white/5 text-gray-400 border-white/10";
//     }
//   };

//   return (
//     <div className="space-y-6 text-gray-200">

{
  /* Header */
}
// <div>
//   <h1 className="text-2xl md:text-3xl font-bold">
//     Enquiries
//   </h1>
//   <p className="text-gray-400 mt-1">
//     Manage customer enquiries and messages
//   </p>
// </div>

{
  /* Table Card */
}
//       <Card className="bg-slate-950 border border-white/10">
//         <CardHeader className="flex flex-col sm:flex-row sm:items-center gap-4">
//           <CardTitle className="text-lg">
//             All Enquiries
//           </CardTitle>

//           <div className="relative flex-1 max-w-sm ml-auto">
//             <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
//             <Input
//               placeholder="Search enquiries..."
//               value={searchQuery}
//               onChange={(e) => setSearchQuery(e.target.value)}
//               className="pl-10 bg-slate-900 border-white/10 text-gray-200"
//             />
//           </div>
//         </CardHeader>

//         <CardContent>
//           {isLoading ? (
//             <div className="text-center py-8 text-gray-400">
//               Loading enquiries...
//             </div>
//           ) : filteredEnquiries.length === 0 ? (
//             <div className="text-center py-8 text-gray-400">
//               No enquiries found.
//             </div>
//           ) : (
//             <div className="overflow-x-auto">
//               <Table>
//                 <TableHeader>
//                   <TableRow className="border-white/10">
//                     <TableHead>Contact</TableHead>
//                     <TableHead>Company</TableHead>
//                     <TableHead>Service</TableHead>
//                     <TableHead>Status</TableHead>
//                     <TableHead>Date</TableHead>
//                     <TableHead />
//                   </TableRow>
//                 </TableHeader>

//                 <TableBody>
//                   {filteredEnquiries.map((e) => (
//                     <TableRow
//                       key={e.id}
//                       className="border-white/10 hover:bg-white/5"
//                     >
//                       <TableCell>
//                         <div className="space-y-1">
//                           <p className="font-medium">
//                             {e.name}
//                           </p>
//                           <div className="flex items-center gap-2 text-xs text-gray-400">
//                             <Mail size={12} />
//                             {e.email}
//                           </div>
//                           {e.mobile && (
//                             <div className="flex items-center gap-2 text-xs text-gray-400">
//                               <Phone size={12} />
//                               {e.mobile}
//                             </div>
//                           )}
//                         </div>
//                       </TableCell>

//                       <TableCell className="text-gray-400">
//                         {e.company && (
//                           <div className="flex items-center gap-2">
//                             <Building size={14} />
//                             {e.company}
//                           </div>
//                         )}
//                       </TableCell>

//                       <TableCell className="text-gray-400">
//                         {e.service || "-"}
//                       </TableCell>

//                       <TableCell>
//                         <Badge
//                           variant="outline"
//                           className={getStatusColor(e.status)}
//                         >
//                           {e.status || "pending"}
//                         </Badge>
//                       </TableCell>

//                       <TableCell className="text-gray-400">
//                         {new Date(e.created_at).toLocaleDateString()}
//                       </TableCell>

//                       <TableCell>
//                         <div className="flex gap-1">
//                           <Button
//                             variant="ghost"
//                             size="sm"
//                             onClick={() => updateStatus(e.id, "contacted")}
//                             className="text-xs hover:bg-white/5"
//                           >
//                             Contact
//                           </Button>
//                           <Button
//                             variant="ghost"
//                             size="sm"
//                             onClick={() => updateStatus(e.id, "resolved")}
//                             className="text-xs text-blue-400 hover:bg-white/5"
//                           >
//                             Resolve
//                           </Button>
//                         </div>
//                       </TableCell>
//                     </TableRow>
//                   ))}
//                 </TableBody>
//               </Table>
//             </div>
//           )}
//         </CardContent>
//       </Card>
//     </div>
//   );
// };

// export default Enquiries;
