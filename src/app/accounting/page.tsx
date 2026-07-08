"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Search } from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

export default function JournalPage() {
  const router = useRouter();
  const [entries, setEntries] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/accounting/journal`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setEntries)
      .catch(() => router.push("/login"))
      .finally(() => setLoading(false));
  }, [router]);

  const filtered = entries.filter((e) =>
    !search || e.description?.includes(search) || e.account?.name?.includes(search)
  );

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Journal</h1>
        <p className="text-gray-500 mt-1">All accounting journal entries</p>
      </div>

      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
        <input
          type="text"
          placeholder="Search entries..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
        />
      </div>

      <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-gray-50 text-left">
                <th className="px-4 py-3 text-gray-600 font-medium">Date</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Account</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Description</th>
                <th className="px-4 py-3 text-gray-600 font-medium">Reference</th>
                <th className="px-4 py-3 text-gray-600 font-medium text-right">Debit</th>
                <th className="px-4 py-3 text-gray-600 font-medium text-right">Credit</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {filtered.map((e: any) => (
                <tr key={e.id} className="hover:bg-gray-50">
                  <td className="px-4 py-3 text-gray-900">
                    {new Date(e.entryDate).toLocaleDateString("en-US")}
                  </td>
                  <td className="px-4 py-3">
                    <span className="text-xs text-gray-400">{e.account?.code}</span>
                    <span className="ml-1">{e.account?.name}</span>
                  </td>
                  <td className="px-4 py-3 text-gray-600 max-w-xs truncate">{e.description}</td>
                  <td className="px-4 py-3 text-gray-500 text-xs">{e.referenceNo || "-"}</td>
                  <td className="px-4 py-3 text-right font-medium">
                    {e.entryType === "DEBIT" ? `${Number(e.amount).toLocaleString()} $` : ""}
                  </td>
                  <td className="px-4 py-3 text-right font-medium">
                    {e.entryType === "CREDIT" ? `${Number(e.amount).toLocaleString()} $` : ""}
                  </td>
                </tr>
              ))}
              {filtered.length === 0 && (
                <tr>
                  <td colSpan={6} className="px-4 py-12 text-center text-gray-400">No entries yet</td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
