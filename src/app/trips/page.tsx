"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Plus, Search, Plane } from "lucide-react";
import Link from "next/link";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";
const STATUS_COLORS: Record<string, string> = {
  INQUIRY: "bg-gray-100 text-gray-700",
  QUOTATION: "bg-blue-100 text-blue-700",
  PROVISIONAL: "bg-yellow-100 text-yellow-700",
  CONFIRMED: "bg-green-100 text-green-700",
  IN_PROGRESS: "bg-purple-100 text-purple-700",
  COMPLETED: "bg-emerald-100 text-emerald-700",
  CANCELLED: "bg-red-100 text-red-700",
};

export default function TripsPage() {
  const router = useRouter();
  const [trips, setTrips] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/trips`, { headers: { Authorization: `Bearer ${token}` } })
      .then((r) => r.json())
      .then(setTrips)
      .catch(() => router.push("/login"))
      .finally(() => setLoading(false));
  }, [router]);

  const filtered = trips.filter((t) =>
    !search || t.referenceNo?.includes(search) || t.name?.includes(search)
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
          <h1 className="text-2xl font-bold text-gray-900">Trips</h1>
          <p className="text-gray-500 mt-1">Manage all travel trips</p>
        </div>
        <Link
          href="/trips/new"
          className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors text-sm font-medium"
        >
          <Plus className="w-4 h-4" />
          New Trip
        </Link>
      </div>

      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
        <input
          type="text"
          placeholder="Search by reference or trip name..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
        />
      </div>

      {filtered.length === 0 ? (
        <div className="text-center py-16 bg-white rounded-2xl border border-gray-100">
          <Plane className="w-12 h-12 text-gray-300 mx-auto mb-4" />
          <p className="text-gray-500">No trips yet</p>
          <Link href="/trips/new" className="text-blue-600 text-sm hover:underline mt-2 inline-block">
            Create your first trip
          </Link>
        </div>
      ) : (
        <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b bg-gray-50 text-left">
                <th className="px-4 py-3 font-medium text-gray-600">Reference</th>
                <th className="px-4 py-3 font-medium text-gray-600">Name</th>
                <th className="px-4 py-3 font-medium text-gray-600">Customer</th>
                <th className="px-4 py-3 font-medium text-gray-600">Date</th>
                <th className="px-4 py-3 font-medium text-gray-600">Status</th>
                <th className="px-4 py-3 font-medium text-gray-600">Revenue</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((trip) => (
                <tr
                  key={trip.id}
                  onClick={() => router.push(`/trips/${trip.id}`)}
                  className="border-b last:border-0 hover:bg-gray-50 cursor-pointer"
                >
                  <td className="px-4 py-3 font-medium text-blue-600">{trip.referenceNo}</td>
                  <td className="px-4 py-3">{trip.name || "-"}</td>
                  <td className="px-4 py-3 text-gray-600">
                    {trip.customers?.[0]?.customer?.firstName} {trip.customers?.[0]?.customer?.lastName}
                  </td>
                  <td className="px-4 py-3 text-gray-500">
                    {trip.startDate ? new Date(trip.startDate).toLocaleDateString("en-US") : "-"}
                  </td>
                  <td className="px-4 py-3">
                    <span className={`px-2.5 py-1 rounded-lg text-xs font-medium ${STATUS_COLORS[trip.status] || "bg-gray-100 text-gray-700"}`}>
                      {trip.status}
                    </span>
                  </td>
                  <td className="px-4 py-3">{Number(trip.totalRevenue).toLocaleString()} $</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
