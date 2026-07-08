"use client";

import { useEffect, useState, useCallback } from "react";
import { FileText, Plus, Search, Trash2, X } from "lucide-react";
import { api } from "@/lib/api";

interface Invoice {
  id: string;
  client?: { id: string; name: string };
  clientId?: string;
  description: string;
  amount: number;
  status: "PAID" | "PENDING" | "OVERDUE";
  date: string;
}

const emptyInvoice = {
  clientId: "",
  description: "",
  amount: 0,
  status: "PENDING" as const,
  date: new Date().toISOString().split("T")[0],
};

const statusColors: Record<string, string> = {
  PAID: "bg-green-100 text-green-700",
  PENDING: "bg-yellow-100 text-yellow-700",
  OVERDUE: "bg-red-100 text-red-700",
};

const statusLabels: Record<string, string> = {
  PAID: "Paid",
  PENDING: "Pending",
  OVERDUE: "Overdue",
};

export default function InvoicesPage() {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [search, setSearch] = useState("");
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState(emptyInvoice);
  const [filterStatus, setFilterStatus] = useState("all");
  const [loading, setLoading] = useState(true);

  const fetchInvoices = useCallback(async () => {
    try {
      const data = await api.get<Invoice[]>("/invoices");
      setInvoices(data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { fetchInvoices(); }, [fetchInvoices]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.clientId || !form.amount) return;
    try {
      await api.post("/invoices", form);
      setForm(emptyInvoice);
      setShowForm(false);
      await fetchInvoices();
    } catch (err) {
      console.error(err);
    }
  };

  const toggleStatus = async (id: string, current: string) => {
    const next: Record<string, "PAID" | "PENDING" | "OVERDUE"> = {
      PENDING: "PAID", PAID: "OVERDUE", OVERDUE: "PENDING",
    };
    try {
      await api.patch(`/invoices/${id}`, { status: next[current] });
      await fetchInvoices();
    } catch (err) {
      console.error(err);
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm("Are you sure you want to delete this invoice?")) return;
    try {
      await api.delete(`/invoices/${id}`);
      await fetchInvoices();
    } catch (err) {
      console.error(err);
    }
  };

  const filtered = invoices.filter((inv) => {
    const clientName = inv.client?.name || "";
    const matchSearch = clientName.includes(search) || inv.description?.includes(search);
    const matchStatus = filterStatus === "all" || inv.status === filterStatus;
    return matchSearch && matchStatus;
  });

  const totals = {
    all: invoices.reduce((s, i) => s + Number(i.amount), 0),
    PAID: invoices.filter((i) => i.status === "PAID").reduce((s, i) => s + Number(i.amount), 0),
    PENDING: invoices.filter((i) => i.status === "PENDING").reduce((s, i) => s + Number(i.amount), 0),
    OVERDUE: invoices.filter((i) => i.status === "OVERDUE").reduce((s, i) => s + Number(i.amount), 0),
  };

  if (loading) return <div className="flex items-center justify-center h-64"><div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" /></div>;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Invoices</h1>
          <p className="text-gray-500 mt-1">Manage invoices and payments</p>
        </div>
        <button onClick={() => setShowForm(true)}
          className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
          <Plus className="w-4 h-4" /> New Invoice
        </button>
      </div>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {[
          { key: "all", label: "All", amount: totals.all, color: "border-blue-500" },
          { key: "PAID", label: "Paid", amount: totals.PAID, color: "border-green-500" },
          { key: "PENDING", label: "Pending", amount: totals.PENDING, color: "border-yellow-500" },
          { key: "OVERDUE", label: "Overdue", amount: totals.OVERDUE, color: "border-red-500" },
        ].map((tab) => (
          <button key={tab.key} onClick={() => setFilterStatus(tab.key)}
            className={`p-4 rounded-xl border-r-4 text-right transition-colors ${filterStatus === tab.key ? `bg-white shadow-sm ${tab.color}` : "bg-white/50 hover:bg-white"}`}>
            <p className="text-xs text-gray-500">{tab.label}</p>
            <p className="text-lg font-bold text-gray-900 mt-1">{tab.amount.toLocaleString()} $</p>
          </button>
        ))}
      </div>

      <div className="relative">
        <Search className="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input type="text" placeholder="Search invoices..." value={search} onChange={(e) => setSearch(e.target.value)}
          className="w-full pr-11 pl-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl w-full max-w-lg">
            <div className="flex items-center justify-between p-6 border-b">
              <h2 className="text-lg font-semibold">New Invoice</h2>
              <button onClick={() => setShowForm(false)} className="text-gray-400 hover:text-gray-600"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Client *</label>
                <input type="text" value={form.clientId} onChange={(e) => setForm({ ...form, clientId: e.target.value })}
                  className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" required />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })}
                  className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                  <input type="number" value={form.amount || ""} onChange={(e) => setForm({ ...form, amount: Number(e.target.value) })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Date</label>
                  <input type="date" value={form.date} onChange={(e) => setForm({ ...form, date: e.target.value })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
              </div>
              <div className="flex gap-3 pt-2">
                <button type="submit" className="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">Create Invoice</button>
                <button type="button" onClick={() => setShowForm(false)}
                  className="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {filtered.length === 0 ? (
        <div className="bg-white rounded-2xl p-12 text-center border border-gray-100">
          <FileText className="w-12 h-12 mx-auto text-gray-300 mb-3" />
          <p className="text-gray-500">{search ? "No search results" : "No invoices yet"}</p>
        </div>
      ) : (
        <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-gray-100">
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">Client</th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">Description</th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">Amount</th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">Date</th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {filtered.map((inv) => (
                  <tr key={inv.id} className="hover:bg-gray-50/50">
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">{inv.client?.name || "—"}</td>
                    <td className="px-6 py-4 text-sm text-gray-600">{inv.description}</td>
                    <td className="px-6 py-4 text-sm font-semibold text-gray-900">{Number(inv.amount).toLocaleString()} $</td>
                    <td className="px-6 py-4 text-sm text-gray-500">{new Date(inv.date).toLocaleDateString("en-US")}</td>
                    <td className="px-6 py-4">
                      <button onClick={() => toggleStatus(inv.id, inv.status)}
                        className={`px-3 py-1 rounded-full text-xs font-medium ${statusColors[inv.status]}`}>
                        {statusLabels[inv.status]}
                      </button>
                    </td>
                    <td className="px-6 py-4">
                      <button onClick={() => handleDelete(inv.id)}
                        className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"><Trash2 className="w-4 h-4" /></button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
}
