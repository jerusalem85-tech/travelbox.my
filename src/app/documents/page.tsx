"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Search, FileText, Plus } from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

const typeLabel: Record<string, string> = {
  INVOICE: "Invoice", QUOTATION: "Quotation", PROPOSAL: "Proposal",
  RECEIPT: "Receipt", VOUCHER: "Voucher", ITINERARY: "Itinerary",
  TICKET: "Ticket", CERTIFICATE: "Certificate", CONTRACT: "Contract",
};

const statusColor: Record<string, string> = {
  DRAFT: "bg-gray-100 text-gray-600", FINAL: "bg-blue-100 text-blue-700",
  SENT: "bg-purple-100 text-purple-700", PAID: "bg-emerald-100 text-emerald-700",
  CANCELLED: "bg-red-100 text-red-700",
};

const statusLabel: Record<string, string> = {
  DRAFT: "Draft", FINAL: "Final", SENT: "Sent", PAID: "Paid", CANCELLED: "Cancelled",
};

export default function DocumentsPage() {
  const router = useRouter();
  const [docs, setDocs] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [typeFilter, setTypeFilter] = useState("");

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/documents`, { headers: { Authorization: `Bearer ${token}` } })
      .then((r) => r.json())
      .then(setDocs)
      .catch(() => router.push("/login"))
      .finally(() => setLoading(false));
  }, [router]);

  const filtered = docs.filter((d) => {
    if (search && !d.documentNo?.includes(search) && !d.title?.includes(search)) return false;
    if (typeFilter && d.documentType !== typeFilter) return false;
    return true;
  });

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Documents</h1>
          <p className="text-gray-500 mt-1">Create and view documents</p>
        </div>
        <button
          onClick={() => router.push("/documents/generate")}
          className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors text-sm"
        >
          <Plus className="w-4 h-4" />
          <span>New Document</span>
        </button>
      </div>

      <div className="flex gap-3">
        <div className="relative flex-1">
          <Search className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            type="text"
            placeholder="Search by document number..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          />
        </div>
        <select
          value={typeFilter}
          onChange={(e) => setTypeFilter(e.target.value)}
          className="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
        >
          <option value="">All Types</option>
          {Object.entries(typeLabel).map(([k, v]) => (
            <option key={k} value={k}>{v}</option>
          ))}
        </select>
      </div>

      {filtered.length === 0 ? (
        <div className="text-center py-16 bg-white rounded-2xl border border-gray-100">
          <FileText className="w-12 h-12 text-gray-300 mx-auto mb-4" />
          <p className="text-gray-500">No documents yet</p>
          <button
            onClick={() => router.push("/documents/generate")}
            className="mt-4 text-blue-600 text-sm hover:underline"
          >
            Create your first document
          </button>
        </div>
      ) : (
        <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="bg-gray-50 text-right">
                  <th className="px-4 py-3 text-gray-600 font-medium">Document No.</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Type</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Title</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Trip</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Date</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Status</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {filtered.map((d: any) => (
                  <tr key={d.id} className="hover:bg-gray-50">
                    <td className="px-4 py-3 font-medium text-gray-900">{d.documentNo}</td>
                    <td className="px-4 py-3">
                      <span className="px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                        {typeLabel[d.documentType] || d.documentType}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-gray-600 max-w-xs truncate">{d.title}</td>
                    <td className="px-4 py-3 text-xs text-gray-500">{d.trip?.referenceNo || "-"}</td>
                    <td className="px-4 py-3 text-gray-600">
                      {new Date(d.createdAt).toLocaleDateString("ar-SA")}
                    </td>
                    <td className="px-4 py-3">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-medium ${statusColor[d.status] || ""}`}>
                        {statusLabel[d.status] || d.status}
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <button
                        onClick={() => router.push(`/documents/detail?id=${d.id}`)}
                        className="text-blue-600 hover:underline text-xs"
                      >
                        View
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
}
