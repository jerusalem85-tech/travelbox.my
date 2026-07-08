"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Plus, Search, Users as UsersIcon } from "lucide-react";
import Link from "next/link";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

export default function CustomersPage() {
  const router = useRouter();
  const [customers, setCustomers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/customers`, { headers: { Authorization: `Bearer ${token}` } })
      .then((r) => r.json())
      .then(setCustomers)
      .catch(() => router.push("/login"))
      .finally(() => setLoading(false));
  }, [router]);

  const filtered = customers.filter((c) =>
    !search || c.firstName?.includes(search) || c.lastName?.includes(search) || c.email?.includes(search) || c.phone?.includes(search)
  );

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Customers</h1>
          <p className="text-gray-500 mt-1">Manage travel agency customers</p>
        </div>
      </div>

      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
        <input
          type="text"
          placeholder="Search by name, email or phone..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
        />
      </div>

      {filtered.length === 0 ? (
        <div className="text-center py-16 bg-white rounded-2xl border border-gray-100">
          <UsersIcon className="w-12 h-12 text-gray-300 mx-auto mb-4" />
          <p className="text-gray-500">No customers yet</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {filtered.map((c) => (
            <div key={c.id} className="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow cursor-pointer"
              onClick={() => router.push(`/customers/detail?id=${c.id}`)}>
              <div className="flex items-center gap-3 mb-3">
                <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <span className="text-blue-600 font-bold text-sm">
                    {c.firstName?.[0]}{c.lastName?.[0]}
                  </span>
                </div>
                <div>
                  <p className="font-medium text-gray-900">{c.firstName} {c.lastName}</p>
                  <p className="text-xs text-gray-500">{c.email || "No email"}</p>
                </div>
              </div>
              <div className="text-xs text-gray-500 space-y-1">
                <p>Phone: {c.phone || "-"}</p>
                <p>Passport: {c.passportNo || "-"}</p>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
