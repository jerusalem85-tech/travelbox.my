"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { TrendingUp, ArrowUpLeft, ArrowDownLeft } from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

export default function ProfitLossPage() {
  const router = useRouter();
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/accounting/profit-loss`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setData)
      .catch(() => router.push("/login"))
      .finally(() => setLoading(false));
  }, [router]);

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  if (!data) return null;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Profit & Loss</h1>
        <p className="text-gray-500 mt-1">Revenue and expenses summary</p>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <div className="flex items-center gap-3 mb-3">
            <div className="p-2 bg-green-100 rounded-xl">
              <ArrowUpLeft className="w-5 h-5 text-green-600" />
            </div>
            <span className="text-sm text-gray-500">Total Revenue</span>
          </div>
          <p className="text-2xl font-bold text-green-700">{Number(data.totalRevenue).toLocaleString()} $</p>
        </div>
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <div className="flex items-center gap-3 mb-3">
            <div className="p-2 bg-red-100 rounded-xl">
              <ArrowDownLeft className="w-5 h-5 text-red-600" />
            </div>
            <span className="text-sm text-gray-500">Total Expenses</span>
          </div>
          <p className="text-2xl font-bold text-red-700">{Number(data.totalExpenses).toLocaleString()} $</p>
        </div>
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <div className="flex items-center gap-3 mb-3">
            <div className="p-2 bg-blue-100 rounded-xl">
              <TrendingUp className="w-5 h-5 text-blue-600" />
            </div>
            <span className="text-sm text-gray-500">Net Profit</span>
          </div>
          <p className={`text-2xl font-bold ${Number(data.netProfit) >= 0 ? "text-blue-700" : "text-red-700"}`}>
            {Number(data.netProfit).toLocaleString()} $
          </p>
          <p className="text-xs text-gray-400 mt-1">Margin: {Number(data.grossMargin).toFixed(1)}%</p>
        </div>
      </div>

      {/* Revenue Table */}
      {data.revenues?.length > 0 && (
        <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-gray-900">Revenue</h2>
          </div>
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-gray-50 text-left">
                <th className="px-6 py-3 text-gray-600 font-medium">Account</th>
                <th className="px-6 py-3 text-gray-600 font-medium text-right">Amount</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {data.revenues.map((r: any, i: number) => (
                <tr key={i}>
                  <td className="px-6 py-3 text-gray-900">
                    <span className="text-xs text-gray-400 mr-1">{r.code}</span>
                    {r.name}
                  </td>
                  <td className="px-6 py-3 text-right font-medium text-green-700">
                    {Number(r.amount).toLocaleString()} $
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* Expenses Table */}
      {data.expenses?.length > 0 && (
        <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100">
            <h2 className="font-semibold text-gray-900">Expenses</h2>
          </div>
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-gray-50 text-left">
                <th className="px-6 py-3 text-gray-600 font-medium">Account</th>
                <th className="px-6 py-3 text-gray-600 font-medium text-right">Amount</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {data.expenses.map((e: any, i: number) => (
                <tr key={i}>
                  <td className="px-6 py-3 text-gray-900">
                    <span className="text-xs text-gray-400 mr-1">{e.code}</span>
                    {e.name}
                  </td>
                  <td className="px-6 py-3 text-right font-medium text-red-700">
                    {Number(e.amount).toLocaleString()} $
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
