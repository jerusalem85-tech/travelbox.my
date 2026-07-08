"use client";

import { useEffect, useState } from "react";
import { BarChart3, TrendingUp, TrendingDown, DollarSign, Users, FileText } from "lucide-react";
import { api } from "@/lib/api";

interface Invoice {
  id: string; client?: { id: string; name: string }; description: string; amount: number; status: string; date: string;
}
interface Expense {
  id: string; description: string; category: string; amount: number; date: string;
}

export default function ReportsPage() {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [expenses, setExpenses] = useState<Expense[]>([]);

  useEffect(() => {
    api.get<Invoice[]>("/invoices").then(setInvoices).catch(console.error);
    api.get<Expense[]>("/expenses").then(setExpenses).catch(console.error);
  }, []);

  const toNum = (v: string | number) => Number(v);

  const totalRevenue = invoices.filter((i) => i.status === "PAID").reduce((s, i) => s + toNum(i.amount), 0);
  const totalPending = invoices.filter((i) => i.status === "PENDING").reduce((s, i) => s + toNum(i.amount), 0);
  const totalOverdue = invoices.filter((i) => i.status === "OVERDUE").reduce((s, i) => s + toNum(i.amount), 0);
  const totalExpenses = expenses.reduce((s, e) => s + toNum(e.amount), 0);
  const netProfit = totalRevenue - totalExpenses;

  const categoryBreakdown = expenses.reduce((acc, exp) => {
    acc[exp.category] = (acc[exp.category] || 0) + toNum(exp.amount);
    return acc;
  }, {} as Record<string, number>);
  const maxCategory = Math.max(...Object.values(categoryBreakdown), 1);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Reports</h1>
        <p className="text-gray-500 mt-1">Comprehensive financial and statistical reports</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <div className="flex items-center gap-3 mb-3">
            <div className="p-2 bg-green-100 rounded-xl"><TrendingUp className="w-5 h-5 text-green-600" /></div>
            <span className="text-sm text-gray-500">Collected Revenue</span>
          </div>
          <p className="text-2xl font-bold text-green-600">{totalRevenue.toLocaleString()} $</p>
        </div>
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <div className="flex items-center gap-3 mb-3">
            <div className="p-2 bg-yellow-100 rounded-xl"><DollarSign className="w-5 h-5 text-yellow-600" /></div>
            <span className="text-sm text-gray-500">Pending Amounts</span>
          </div>
          <p className="text-2xl font-bold text-yellow-600">{(totalPending + totalOverdue).toLocaleString()} $</p>
        </div>
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <div className="flex items-center gap-3 mb-3">
            <div className="p-2 bg-red-100 rounded-xl"><TrendingDown className="w-5 h-5 text-red-600" /></div>
            <span className="text-sm text-gray-500">Expenses</span>
          </div>
          <p className="text-2xl font-bold text-red-600">{totalExpenses.toLocaleString()} $</p>
        </div>
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <div className="flex items-center gap-3 mb-3">
            <div className="p-2 bg-blue-100 rounded-xl"><BarChart3 className="w-5 h-5 text-blue-600" /></div>
            <span className="text-sm text-gray-500">Net Profit</span>
          </div>
          <p className={`text-2xl font-bold ${netProfit >= 0 ? "text-blue-600" : "text-red-600"}`}>{netProfit.toLocaleString()} $</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Expenses by Category</h2>
          {Object.keys(categoryBreakdown).length === 0 ? (
            <div className="text-center py-8 text-gray-400"><BarChart3 className="w-10 h-10 mx-auto mb-2 opacity-50" /><p className="text-sm">No data available</p></div>
          ) : (
            <div className="space-y-3">
              {Object.entries(categoryBreakdown).sort(([, a], [, b]) => b - a).map(([category, amount]) => (
                <div key={category} className="space-y-1">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">{category}</span>
                    <span className="font-medium text-gray-900">{amount.toLocaleString()} $</span>
                  </div>
                  <div className="w-full bg-gray-100 rounded-full h-2">
                    <div className="bg-red-400 h-2 rounded-full transition-all" style={{ width: `${(amount / maxCategory) * 100}%` }} />
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>

      <div className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Invoice Summary</h2>
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div className="p-4 bg-green-50 rounded-xl text-center">
            <FileText className="w-6 h-6 text-green-600 mx-auto mb-2" />
            <p className="text-2xl font-bold text-green-700">{invoices.filter((i) => i.status === "PAID").length}</p>
            <p className="text-sm text-green-600">Paid Invoices</p>
          </div>
          <div className="p-4 bg-yellow-50 rounded-xl text-center">
            <FileText className="w-6 h-6 text-yellow-600 mx-auto mb-2" />
            <p className="text-2xl font-bold text-yellow-700">{invoices.filter((i) => i.status === "PENDING").length}</p>
            <p className="text-sm text-yellow-600">Pending</p>
          </div>
          <div className="p-4 bg-red-50 rounded-xl text-center">
            <FileText className="w-6 h-6 text-red-600 mx-auto mb-2" />
            <p className="text-2xl font-bold text-red-700">{invoices.filter((i) => i.status === "OVERDUE").length}</p>
            <p className="text-sm text-red-600">Overdue</p>
          </div>
        </div>
      </div>
    </div>
  );
}
