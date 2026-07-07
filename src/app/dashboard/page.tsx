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

interface Stats {
  totalClients: number;
  totalInvoices: number;
  totalRevenue: number;
  totalExpenses: number;
}

export default function DashboardPage() {
  const [stats, setStats] = useState<Stats>({
    totalClients: 0,
    totalInvoices: 0,
    totalRevenue: 0,
    totalExpenses: 0,
  });

  useEffect(() => {
    const clients = JSON.parse(localStorage.getItem("travelbox_clients") || "[]");
    const invoices = JSON.parse(localStorage.getItem("travelbox_invoices") || "[]");
    const expenses = JSON.parse(localStorage.getItem("travelbox_expenses") || "[]");

    setStats({
      totalClients: clients.length,
      totalInvoices: invoices.length,
      totalRevenue: invoices.reduce(
        (sum: number, inv: { amount?: number }) => sum + (inv.amount || 0),
        0
      ),
      totalExpenses: expenses.reduce(
        (sum: number, exp: { amount?: number }) => sum + (exp.amount || 0),
        0
      ),
    });
  }, []);

  const cards = [
    {
      title: "العملاء",
      value: stats.totalClients,
      icon: Users,
      color: "bg-blue-50 text-blue-600",
      iconBg: "bg-blue-100",
    },
    {
      title: "الفواتير",
      value: stats.totalInvoices,
      icon: FileText,
      color: "bg-emerald-50 text-emerald-600",
      iconBg: "bg-emerald-100",
    },
    {
      title: "الإيرادات",
      value: `${stats.totalRevenue.toLocaleString()} ر.م`,
      icon: TrendingUp,
      color: "bg-purple-50 text-purple-600",
      iconBg: "bg-purple-100",
    },
    {
      title: "المصروفات",
      value: `${stats.totalExpenses.toLocaleString()} ر.م`,
      icon: Wallet,
      color: "bg-red-50 text-red-600",
      iconBg: "bg-red-100",
    },
  ];

  const profit = stats.totalRevenue - stats.totalExpenses;

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">لوحة التحكم</h1>
        <p className="text-gray-500 mt-1">نظرة عامة على نشاطك التجاري</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {cards.map((card) => (
          <div
            key={card.title}
            className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm"
          >
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
                <span className="text-sm font-medium text-green-800">
                  إجمالي الإيرادات
                </span>
              </div>
              <span className="font-bold text-green-700">
                {stats.totalRevenue.toLocaleString()} ر.م
              </span>
            </div>
            <div className="flex items-center justify-between p-4 bg-red-50 rounded-xl">
              <div className="flex items-center gap-3">
                <ArrowDownLeft className="w-5 h-5 text-red-600" />
                <span className="text-sm font-medium text-red-800">
                  إجمالي المصروفات
                </span>
              </div>
              <span className="font-bold text-red-700">
                {stats.totalExpenses.toLocaleString()} ر.م
              </span>
            </div>
            <div className="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
              <div className="flex items-center gap-3">
                <TrendingUp className="w-5 h-5 text-blue-600" />
                <span className="text-sm font-medium text-blue-800">
                  صافي الربح
                </span>
              </div>
              <span
                className={`font-bold ${profit >= 0 ? "text-blue-700" : "text-red-700"}`}
              >
                {profit.toLocaleString()} ر.م
              </span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">
            آخر الفواتير
          </h2>
          <RecentItems storageKey="travelbox_invoices" type="invoice" />
        </div>
      </div>
    </div>
  );
}

function RecentItems({
  storageKey,
  type,
}: {
  storageKey: string;
  type: string;
}) {
  const [items, setItems] = useState<Array<{ id: string; client?: string; description?: string; amount?: number; date?: string }>>([]);

  useEffect(() => {
    const data = JSON.parse(localStorage.getItem(storageKey) || "[]");
    setItems(data.slice(-5).reverse());
  }, [storageKey]);

  if (items.length === 0) {
    return (
      <div className="text-center py-8 text-gray-400">
        <FileText className="w-10 h-10 mx-auto mb-2 opacity-50" />
        <p className="text-sm">لا توجد بيانات بعد</p>
      </div>
    );
  }

  return (
    <div className="space-y-3">
      {items.map((item) => (
        <div
          key={item.id}
          className="flex items-center justify-between p-3 bg-gray-50 rounded-xl"
        >
          <div>
            <p className="text-sm font-medium text-gray-900">
              {type === "invoice" ? item.client : item.description}
            </p>
            <p className="text-xs text-gray-500">{item.date}</p>
          </div>
          <span className="font-semibold text-sm text-gray-900">
            {(item.amount || 0).toLocaleString()} ر.م
          </span>
        </div>
      ))}
    </div>
  );
}
