"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Search, Wallet } from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

export default function PaymentsPage() {
  const router = useRouter();
  const [payments, setPayments] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/payments`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setPayments)
      .catch(() => router.push("/login"))
      .finally(() => setLoading(false));
  }, [router]);

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  const directionStyle: Record<string, string> = {
    INFLOW: "text-green-600 bg-green-50",
    OUTFLOW: "text-red-600 bg-red-50",
  };
  const directionLabel: Record<string, string> = {
    INFLOW: "Inflow", OUTFLOW: "Outflow",
  };
  const statusStyle: Record<string, string> = {
    PENDING: "bg-amber-100 text-amber-700",
    PARTIAL: "bg-blue-100 text-blue-700",
    PAID: "bg-emerald-100 text-emerald-700",
    REFUNDED: "bg-purple-100 text-purple-700",
    CANCELLED: "bg-red-100 text-red-700",
  };
  const statusLabel: Record<string, string> = {
    PENDING: "Pending", PARTIAL: "Partial", PAID: "Paid", REFUNDED: "Refunded", CANCELLED: "Cancelled",
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Payments</h1>
        <p className="text-gray-500 mt-1">Manage payments and receivables</p>
      </div>

      <div className="relative">
        <Search className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
        <input
          type="text"
          placeholder="Search..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
        />
      </div>

      <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-gray-50 text-right">
                <th className="px-4 py-3 text-gray-600 font-medium">Date</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Type</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Trip</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Party</th>
                <th className="px-4 py-3 text-gray-600 font-medium text-left">Amount</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Payment Method</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {payments.filter((p) =>
                !search || p.referenceNo?.includes(search) || p.description?.includes(search)
              ).map((p: any) => (
                <tr key={p.id} className="hover:bg-gray-50 cursor-pointer"
                  onClick={() => router.push(`/payments/detail?id=${p.id}`)}>
                  <td className="px-4 py-3 text-gray-600">
                    {new Date(p.paymentDate).toLocaleDateString("en-US")}
                  </td>
                  <td className="px-4 py-3">
                    <span className={`px-2.5 py-1 rounded-full text-xs font-medium ${directionStyle[p.direction]}`}>
                      {directionLabel[p.direction]}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-gray-500 text-xs">{p.trip?.referenceNo || "-"}</td>
                  <td className="px-4 py-3 text-gray-600">
                    {p.customer ? `${p.customer.firstName} ${p.customer.lastName}` :
                     p.supplier ? p.supplier.name : "-"}
                  </td>
                  <td className={`px-4 py-3 text-left font-bold ${
                    p.direction === "INFLOW" ? "text-green-700" : "text-red-700"
                  }`}>
                    {Number(p.amount).toLocaleString()} $
                  </td>
                  <td className="px-4 py-3 text-gray-500">{p.method}</td>
                  <td className="px-4 py-3">
                    <span className={`px-2.5 py-1 rounded-full text-xs font-medium ${statusStyle[p.status] || ""}`}>
                      {statusLabel[p.status] || p.status}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          {payments.length === 0 && (
            <div className="text-center py-16">
              <Wallet className="w-12 h-12 text-gray-300 mx-auto mb-4" />
              <p className="text-gray-500">No payments yet</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
