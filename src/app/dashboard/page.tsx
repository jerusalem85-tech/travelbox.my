"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import {
  Users, FileText, TrendingUp, Wallet, ArrowUpLeft, ArrowDownLeft, Plane,
} from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

async function apiGet<T>(endpoint: string): Promise<T> {
  const token = localStorage.getItem("travelbox_token");
  const res = await fetch(`${API_BASE}${endpoint}`, {
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" },
  });
  if (!res.ok) throw new Error("Request failed");
  return res.json();
}

export default function DashboardPage() {
  const router = useRouter();
  const [data, setData] = useState<any>(null);

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    apiGet("/dashboard").then(setData).catch(() => router.push("/login"));
  }, [router]);

  if (!data) return (
    <div className="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center">
      <div className="animate-spin w-10 h-10 border-4 border-white border-t-transparent rounded-full" />
    </div>
  );

  const { stats, recentTrips, upcomingTrips } = data;

  const cards = [
    { title: "Active Trips", value: stats.activeTrips, icon: Plane, color: "bg-blue-50 text-blue-600", iconBg: "bg-blue-100" },
    { title: "Total Trips", value: stats.totalTrips, icon: TrendingUp, color: "bg-purple-50 text-purple-600", iconBg: "bg-purple-100" },
    { title: "Customers", value: stats.totalCustomers, icon: Users, color: "bg-emerald-50 text-emerald-600", iconBg: "bg-emerald-100" },
    { title: "Pending Payments", value: `${Number(stats.pendingPayments).toLocaleString()} $`, icon: Wallet, color: "bg-amber-50 text-amber-600", iconBg: "bg-amber-100" },
  ];

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
          <p className="text-gray-500 mt-1">Overview of your business activity</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {cards.map((card) => (
          <div key={card.title} className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div className="flex items-center justify-between mb-4">
              <div className={`p-2.5 rounded-xl ${card.iconBg}`}>
                <card.icon className={`w-5 h-5 ${card.color.split(" ")[1]}`} />
              </div>
            </div>
            <p className="text-sm text-gray-500">{card.title}</p>
            <p className="text-2xl font-bold text-gray-900 mt-1">{card.value}</p>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h2>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 bg-green-50 rounded-xl">
              <div className="flex items-center gap-3">
                <ArrowUpLeft className="w-5 h-5 text-green-600" />
                <span className="text-sm font-medium text-green-800">Total Revenue</span>
              </div>
              <span className="font-bold text-green-700">{Number(stats.totalRevenue).toLocaleString()} $</span>
            </div>
            <div className="flex items-center justify-between p-4 bg-red-50 rounded-xl">
              <div className="flex items-center gap-3">
                <ArrowDownLeft className="w-5 h-5 text-red-600" />
                <span className="text-sm font-medium text-red-800">Total Cost</span>
              </div>
              <span className="font-bold text-red-700">{Number(stats.totalCost).toLocaleString()} $</span>
            </div>
            <div className="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
              <div className="flex items-center gap-3">
                <TrendingUp className="w-5 h-5 text-blue-600" />
                <span className="text-sm font-medium text-blue-800">Net Profit</span>
              </div>
              <span className={`font-bold ${Number(stats.totalProfit) >= 0 ? "text-blue-700" : "text-red-700"}`}>
                {Number(stats.totalProfit).toLocaleString()} $
              </span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Upcoming Trips</h2>
          {upcomingTrips?.length > 0 ? (
            <div className="space-y-3">
              {upcomingTrips.map((trip: any) => (
                <div key={trip.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                  <div>
                    <p className="text-sm font-medium text-gray-900">{trip.referenceNo}</p>
                    <p className="text-xs text-gray-500">
                      {trip.customers?.[0]?.customer?.firstName} {trip.customers?.[0]?.customer?.lastName}
                    </p>
                  </div>
                  <span className="text-xs text-gray-500">
                    {trip.startDate ? new Date(trip.startDate).toLocaleDateString("en-US") : ""}
                  </span>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-gray-400 text-sm">No upcoming trips</p>
          )}
        </div>
      </div>
    </div>
  );
}
