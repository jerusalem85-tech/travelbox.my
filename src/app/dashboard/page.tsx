"use client";

import { useEffect, useState } from "react";
import {
  Users,
  FileText,
  Wallet,
  TrendingUp,
  ArrowUpLeft,
  ArrowDownLeft,
} from "lucide-react";
import { api } from "@/lib/api";

interface DashboardStats {
  totalUsers: number;
  totalClients: number;
  totalInvoices: number;
  totalExpenses: number;
  totalTrips: number;
  totalBookings: number;
  revenueCollected: number;
  pendingAmount: number;
}

export default function DashboardPage() {
  const [stats, setStats] = useState<DashboardStats>({
    totalUsers: 0,
    totalClients: 0,
    totalInvoices: 0,
    totalExpenses: 0,
    totalTrips: 0,
    totalBookings: 0,
    revenueCollected: 0,
    pendingAmount: 0,
  });

  useEffect(() => {
    api.get<DashboardStats>("/dashboard").then(setStats).catch(console.error);
  }, []);

  const cards = [
    { title: "العملاء", value: stats.totalClients, icon: Users, color: "bg-blue-50 text-blue-600", iconBg: "bg-blue-100" },
    { title: "الفواتير", value: stats.totalInvoices, icon: FileText, color: "bg-emerald-50 text-emerald-600", iconBg: "bg-emerald-100" },
    { title: "الرحلات", value: stats.totalTrips, icon: TrendingUp, color: "bg-purple-50 text-purple-600", iconBg: "bg-purple-100" },
    { title: "المصروفات", value: `${stats.totalExpenses.toLocaleString()} ر.م`, icon: Wallet, color: "bg-red-50 text-red-600", iconBg: "bg-red-100" },
  ];

  const profit = stats.revenueCollected - (stats.totalExpenses > 0 ? stats.totalExpenses : 0);

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">لوحة التحكم</h1>
        <p className="text-gray-500 mt-1">نظرة عامة على نشاطك التجاري</p>
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
          <h2 className="text-lg font-semibold text-gray-900 mb-4">ملخص مالي</h2>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 bg-green-50 rounded-xl">
              <div className="flex items-center gap-3">
                <ArrowUpLeft className="w-5 h-5 text-green-600" />
                <span className="text-sm font-medium text-green-800">الإيرادات المحصلة</span>
              </div>
              <span className="font-bold text-green-700">{stats.revenueCollected.toLocaleString()} ر.م</span>
            </div>
            <div className="flex items-center justify-between p-4 bg-yellow-50 rounded-xl">
              <div className="flex items-center gap-3">
                <ArrowDownLeft className="w-5 h-5 text-yellow-600" />
                <span className="text-sm font-medium text-yellow-800">المبالغ المعلقة</span>
              </div>
              <span className="font-bold text-yellow-700">{stats.pendingAmount.toLocaleString()} ر.م</span>
            </div>
            <div className="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
              <div className="flex items-center gap-3">
                <TrendingUp className="w-5 h-5 text-blue-600" />
                <span className="text-sm font-medium text-blue-800">صافي الربح</span>
              </div>
              <span className={`font-bold ${profit >= 0 ? "text-blue-700" : "text-red-700"}`}>
                {profit.toLocaleString()} ر.م
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
