"use client";

import { useEffect, useState } from "react";
import { FileText, Plus, Search, Trash2, X, Printer } from "lucide-react";

interface Invoice {
  id: string;
  client: string;
  description: string;
  amount: number;
  status: "paid" | "pending" | "overdue";
  date: string;
}

const emptyInvoice: Omit<Invoice, "id" | "date"> & { date: string } = {
  client: "",
  description: "",
  amount: 0,
  status: "pending",
  date: new Date().toISOString().split("T")[0],
};

export default function InvoicesPage() {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [search, setSearch] = useState("");
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState(emptyInvoice);
  const [filterStatus, setFilterStatus] = useState<string>("all");

  useEffect(() => {
    const data = JSON.parse(localStorage.getItem("travelbox_invoices") || "[]");
    setInvoices(data);
  }, []);

  const save = (updated: Invoice[]) => {
    setInvoices(updated);
    localStorage.setItem("travelbox_invoices", JSON.stringify(updated));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.client.trim() || !form.amount) return;

    const newInvoice: Invoice = {
      ...form,
      id: Date.now().toString(),
      date: new Date(form.date).toLocaleDateString("ar-EG"),
    };
    save([...invoices, newInvoice]);
    setForm(emptyInvoice);
    setShowForm(false);
  };

  const handleDelete = (id: string) => {
    if (confirm("هل أنت متأكد من حذف هذه الفاتورة؟")) {
      save(invoices.filter((inv) => inv.id !== id));
    }
  };

  const toggleStatus = (id: string) => {
    const updated = invoices.map((inv) => {
      if (inv.id !== id) return inv;
      const next: Invoice["status"] =
        inv.status === "pending"
          ? "paid"
          : inv.status === "paid"
            ? "overdue"
            : "pending";
      return { ...inv, status: next };
    });
    save(updated);
  };

  const statusColors = {
    paid: "bg-green-100 text-green-700",
    pending: "bg-yellow-100 text-yellow-700",
    overdue: "bg-red-100 text-red-700",
  };

  const statusLabels = {
    paid: "مدفوعة",
    pending: "قيد الانتظار",
    overdue: "متأخرة",
  };

  const filtered = invoices.filter((inv) => {
    const matchSearch =
      inv.client.includes(search) || inv.description.includes(search);
    const matchStatus = filterStatus === "all" || inv.status === filterStatus;
    return matchSearch && matchStatus;
  });

  const totals = {
    all: invoices.reduce((s, i) => s + i.amount, 0),
    paid: invoices.filter((i) => i.status === "paid").reduce((s, i) => s + i.amount, 0),
    pending: invoices.filter((i) => i.status === "pending").reduce((s, i) => s + i.amount, 0),
    overdue: invoices.filter((i) => i.status === "overdue").reduce((s, i) => s + i.amount, 0),
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">الفواتير</h1>
          <p className="text-gray-500 mt-1">إدارة الفواتير والمدفوعات</p>
        </div>
        <button
          onClick={() => setShowForm(true)}
          className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors"
        >
          <Plus className="w-4 h-4" />
          فاتورة جديدة
        </button>
      </div>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {[
          { key: "all", label: "الكل", amount: totals.all, color: "border-blue-500" },
          { key: "paid", label: "مدفوعة", amount: totals.paid, color: "border-green-500" },
          { key: "pending", label: "قيد الانتظار", amount: totals.pending, color: "border-yellow-500" },
          { key: "overdue", label: "متأخرة", amount: totals.overdue, color: "border-red-500" },
        ].map((tab) => (
          <button
            key={tab.key}
            onClick={() => setFilterStatus(tab.key)}
            className={`p-4 rounded-xl border-r-4 text-right transition-colors ${
              filterStatus === tab.key
                ? `bg-white shadow-sm ${tab.color}`
                : "bg-white/50 hover:bg-white"
            }`}
          >
            <p className="text-xs text-gray-500">{tab.label}</p>
            <p className="text-lg font-bold text-gray-900 mt-1">
              {tab.amount.toLocaleString()} ر.م
            </p>
          </button>
        ))}
      </div>

      <div className="relative">
        <Search className="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input
          type="text"
          placeholder="بحث في الفواتير..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-full pr-11 pl-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl w-full max-w-lg">
            <div className="flex items-center justify-between p-6 border-b">
              <h2 className="text-lg font-semibold">فاتورة جديدة</h2>
              <button
                onClick={() => setShowForm(false)}
                className="text-gray-400 hover:text-gray-600"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  اسم العميل *
                </label>
                <input
                  type="text"
                  value={form.client}
                  onChange={(e) => setForm({ ...form, client: e.target.value })}
                  className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  الوصف *
                </label>
                <input
                  type="text"
                  value={form.description}
                  onChange={(e) =>
                    setForm({ ...form, description: e.target.value })
                  }
                  className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    المبلغ *
                  </label>
                  <input
                    type="number"
                    value={form.amount || ""}
                    onChange={(e) =>
                      setForm({ ...form, amount: Number(e.target.value) })
                    }
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    min="0"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    التاريخ
                  </label>
                  <input
                    type="date"
                    value={form.date}
                    onChange={(e) => setForm({ ...form, date: e.target.value })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  الحالة
                </label>
                <select
                  value={form.status}
                  onChange={(e) =>
                    setForm({
                      ...form,
                      status: e.target.value as Invoice["status"],
                    })
                  }
                  className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="pending">قيد الانتظار</option>
                  <option value="paid">مدفوعة</option>
                  <option value="overdue">متأخرة</option>
                </select>
              </div>
              <div className="flex gap-3 pt-2">
                <button
                  type="submit"
                  className="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors"
                >
                  إنشاء الفاتورة
                </button>
                <button
                  type="button"
                  onClick={() => setShowForm(false)}
                  className="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors"
                >
                  إلغاء
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {filtered.length === 0 ? (
        <div className="bg-white rounded-2xl p-12 text-center border border-gray-100">
          <FileText className="w-12 h-12 mx-auto text-gray-300 mb-3" />
          <p className="text-gray-500">
            {search ? "لا توجد نتائج للبحث" : "لا توجد فواتير بعد"}
          </p>
        </div>
      ) : (
        <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-gray-100">
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    العميل
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    الوصف
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    المبلغ
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    التاريخ
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    الحالة
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    إجراءات
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {filtered.map((inv) => (
                  <tr key={inv.id} className="hover:bg-gray-50/50">
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">
                      {inv.client}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-600">
                      {inv.description}
                    </td>
                    <td className="px-6 py-4 text-sm font-semibold text-gray-900">
                      {inv.amount.toLocaleString()} ر.م
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {inv.date}
                    </td>
                    <td className="px-6 py-4">
                      <button
                        onClick={() => toggleStatus(inv.id)}
                        className={`px-3 py-1 rounded-full text-xs font-medium ${statusColors[inv.status]}`}
                      >
                        {statusLabels[inv.status]}
                      </button>
                    </td>
                    <td className="px-6 py-4">
                      <button
                        onClick={() => handleDelete(inv.id)}
                        className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
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
