"use client";

import { useEffect, useState } from "react";
import { Wallet, Plus, Search, Trash2, X } from "lucide-react";

interface Expense {
  id: string;
  description: string;
  category: string;
  amount: number;
  date: string;
}

const categories = [
  "إيجار",
  "رواتب",
  "كهرباء وماء",
  "إنترنت",
  "نقل",
  "تسويق",
  "مكتب",
  "سفر",
  "أخرى",
];

const emptyExpense = {
  description: "",
  category: "أخرى",
  amount: 0,
  date: new Date().toISOString().split("T")[0],
};

export default function ExpensesPage() {
  const [expenses, setExpenses] = useState<Expense[]>([]);
  const [search, setSearch] = useState("");
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState(emptyExpense);

  useEffect(() => {
    const data = JSON.parse(localStorage.getItem("travelbox_expenses") || "[]");
    setExpenses(data);
  }, []);

  const save = (updated: Expense[]) => {
    setExpenses(updated);
    localStorage.setItem("travelbox_expenses", JSON.stringify(updated));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.description.trim() || !form.amount) return;

    const newExpense: Expense = {
      ...form,
      id: Date.now().toString(),
      date: new Date(form.date).toLocaleDateString("ar-EG"),
    };
    save([...expenses, newExpense]);
    setForm(emptyExpense);
    setShowForm(false);
  };

  const handleDelete = (id: string) => {
    if (confirm("هل أنت متأكد من حذف هذا المصروف؟")) {
      save(expenses.filter((exp) => exp.id !== id));
    }
  };

  const filtered = expenses.filter(
    (exp) =>
      exp.description.includes(search) || exp.category.includes(search)
  );

  const total = filtered.reduce((s, e) => s + e.amount, 0);

  const categoryTotals = categories
    .map((cat) => ({
      category: cat,
      total: expenses
        .filter((e) => e.category === cat)
        .reduce((s, e) => s + e.amount, 0),
    }))
    .filter((c) => c.total > 0)
    .sort((a, b) => b.total - a.total);

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">المصروفات</h1>
          <p className="text-gray-500 mt-1">تتبع وإدارة المصروفات</p>
        </div>
        <button
          onClick={() => setShowForm(true)}
          className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors"
        >
          <Plus className="w-4 h-4" />
          إضافة مصروف
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div className="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100">
          <h3 className="text-sm font-medium text-gray-500 mb-2">
            إجمالي المصروفات
          </h3>
          <p className="text-3xl font-bold text-gray-900">
            {total.toLocaleString()} ر.م
          </p>
        </div>
        <div className="bg-white rounded-2xl p-6 border border-gray-100">
          <h3 className="text-sm font-medium text-gray-500 mb-2">
            عدد المصروفات
          </h3>
          <p className="text-3xl font-bold text-gray-900">{filtered.length}</p>
        </div>
      </div>

      {categoryTotals.length > 0 && (
        <div className="bg-white rounded-2xl p-6 border border-gray-100">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">
            المصروفات حسب الفئة
          </h3>
          <div className="space-y-3">
            {categoryTotals.map((cat) => (
              <div key={cat.category} className="flex items-center gap-4">
                <span className="text-sm text-gray-600 w-24">{cat.category}</span>
                <div className="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                  <div
                    className="bg-blue-500 h-full rounded-full transition-all"
                    style={{
                      width: `${(cat.total / categoryTotals[0].total) * 100}%`,
                    }}
                  />
                </div>
                <span className="text-sm font-semibold text-gray-900 w-28 text-left">
                  {cat.total.toLocaleString()} ر.م
                </span>
              </div>
            ))}
          </div>
        </div>
      )}

      <div className="relative">
        <Search className="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input
          type="text"
          placeholder="بحث في المصروفات..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-full pr-11 pl-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl w-full max-w-lg">
            <div className="flex items-center justify-between p-6 border-b">
              <h2 className="text-lg font-semibold">إضافة مصروف</h2>
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
                    الفئة
                  </label>
                  <select
                    value={form.category}
                    onChange={(e) =>
                      setForm({ ...form, category: e.target.value })
                    }
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    {categories.map((cat) => (
                      <option key={cat} value={cat}>
                        {cat}
                      </option>
                    ))}
                  </select>
                </div>
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
              <div className="flex gap-3 pt-2">
                <button
                  type="submit"
                  className="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors"
                >
                  إضافة
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
          <Wallet className="w-12 h-12 mx-auto text-gray-300 mb-3" />
          <p className="text-gray-500">
            {search ? "لا توجد نتائج للبحث" : "لا توجد مصروفات بعد"}
          </p>
        </div>
      ) : (
        <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-gray-100">
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    الوصف
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    الفئة
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    المبلغ
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    التاريخ
                  </th>
                  <th className="text-right px-6 py-4 text-xs font-medium text-gray-500 uppercase">
                    إجراءات
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {filtered.map((exp) => (
                  <tr key={exp.id} className="hover:bg-gray-50/50">
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">
                      {exp.description}
                    </td>
                    <td className="px-6 py-4">
                      <span className="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                        {exp.category}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm font-semibold text-red-600">
                      {exp.amount.toLocaleString()} ر.م
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {exp.date}
                    </td>
                    <td className="px-6 py-4">
                      <button
                        onClick={() => handleDelete(exp.id)}
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
