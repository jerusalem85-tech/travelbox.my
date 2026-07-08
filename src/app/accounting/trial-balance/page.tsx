"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

export default function TrialBalancePage() {
  const router = useRouter();
  const [data, setData] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/accounting/trial-balance`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setData)
      .catch(() => router.push("/login"))
      .finally(() => setLoading(false));
  }, [router]);

  const totalDebit = data.reduce((s, r) => s + Number(r.debit), 0);
  const totalCredit = data.reduce((s, r) => s + Number(r.credit), 0);

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Trial Balance</h1>
        <p className="text-gray-500 mt-1">Account balances summary</p>
      </div>

      <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-gray-50 text-left">
                <th className="px-4 py-3 text-gray-600 font-medium">Code</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Account</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Category</th>
                <th className="px-4 py-3 text-gray-600 font-medium text-right">Debit</th>
                <th className="px-4 py-3 text-gray-600 font-medium text-right">Credit</th>
                <th className="px-4 py-3 text-gray-600 font-medium text-right">Balance</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {data.map((row: any, i: number) => (
                <tr key={i} className="hover:bg-gray-50">
                  <td className="px-4 py-3 text-gray-500 text-xs">{row.code}</td>
                  <td className="px-4 py-3 text-gray-900 font-medium">{row.name}</td>
                  <td className="px-4 py-3">
                    <span className="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                      {row.category}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-right font-medium">
                    {Number(row.debit) > 0 ? `${Number(row.debit).toLocaleString()} $` : ""}
                  </td>
                  <td className="px-4 py-3 text-right font-medium">
                    {Number(row.credit) > 0 ? `${Number(row.credit).toLocaleString()} $` : ""}
                  </td>
                  <td className={`px-4 py-3 text-right font-bold ${Number(row.balance) >= 0 ? "text-emerald-600" : "text-red-600"}`}>
                    {Number(row.balance).toLocaleString()} $
                  </td>
                </tr>
              ))}
            </tbody>
            <tfoot className="bg-gray-50 border-t-2 border-gray-200">
              <tr>
                <td colSpan={3} className="px-4 py-3 font-bold text-gray-900">Total</td>
                <td className="px-4 py-3 text-right font-bold">{totalDebit.toLocaleString()} $</td>
                <td className="px-4 py-3 text-right font-bold">{totalCredit.toLocaleString()} $</td>
                <td className="px-4 py-3 text-right font-bold text-emerald-600">
                  {(totalDebit - totalCredit).toLocaleString()} $
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  );
}
