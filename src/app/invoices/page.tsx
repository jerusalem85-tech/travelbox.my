"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Plus, Search, Receipt } from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

export default function InvoicesPage() {
  const router = useRouter();
  const [invoices, setInvoices] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/invoices`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setInvoices)
      .catch(() => router.push("/login"))
      .finally(() => setLoading(false));
  }, [router]);

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  const statusColor: Record<string, string> = {
    DRAFT: "bg-gray-100 text-gray-600",
    FINAL: "bg-blue-100 text-blue-700",
    SENT: "bg-purple-100 text-purple-700",
    PAID: "bg-emerald-100 text-emerald-700",
    CANCELLED: "bg-red-100 text-red-700",
  };

  const statusLabel: Record<string, string> = {
    DRAFT: "Draft", FINAL: "Final", SENT: "Sent", PAID: "Paid", CANCELLED: "Cancelled",
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Invoices</h1>
          <p className="text-gray-500 mt-1">Manage invoices and documents</p>
        </div>
      </div>

      <div className="relative">
        <Search className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
        <input
          type="text"
          placeholder="Search by invoice number..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
        />
      </div>

      {invoices.length === 0 ? (
        <div className="text-center py-16 bg-white rounded-2xl border border-gray-100">
          <Receipt className="w-12 h-12 text-gray-300 mx-auto mb-4" />
          <p className="text-gray-500">No invoices yet</p>
        </div>
      ) : (
        <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="bg-gray-50 text-right">
                  <th className="px-4 py-3 text-gray-600 font-medium">Invoice No.</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Customer</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Trip</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Date</th>
                  <th className="px-4 py-3 text-gray-600 font-medium text-left">Amount</th>
                  <th className="px-4 py-3 text-gray-600 font-medium text-left">Balance Due</th>
                  <th className="px-4 py-3 text-gray-600 font-medium">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {invoices.filter((inv) => !search || inv.invoiceNo?.includes(search)).map((inv: any) => (
                  <tr key={inv.id} className="hover:bg-gray-50 cursor-pointer"
                    onClick={() => router.push(`/invoices/detail?id=${inv.id}`)}>
                    <td className="px-4 py-3 font-medium text-gray-900">{inv.invoiceNo}</td>
                    <td className="px-4 py-3 text-gray-600">
                      {inv.customer ? `${inv.customer.firstName} ${inv.customer.lastName}` : "-"}
                    </td>
                    <td className="px-4 py-3 text-gray-500 text-xs">{inv.trip?.referenceNo || "-"}</td>
                    <td className="px-4 py-3 text-gray-600">
                      {new Date(inv.issueDate).toLocaleDateString("en-US")}
                    </td>
                    <td className="px-4 py-3 text-left font-medium">{Number(inv.totalAmount).toLocaleString()} $</td>
                    <td className="px-4 py-3 text-left">{Number(inv.balanceDue).toLocaleString()} $</td>
                    <td className="px-4 py-3">
                      <span className={`px-2.5 py-1 rounded-full text-xs font-medium ${statusColor[inv.status] || ""}`}>
                        {statusLabel[inv.status] || inv.status}
                      </span>
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
