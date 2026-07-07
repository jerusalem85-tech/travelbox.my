"use client";

import { useEffect, useState, useCallback } from "react";
import { Plane, Plus, Search, Edit2, Trash2, X } from "lucide-react";
import { api } from "@/lib/api";

interface Trip {
  id: string;
  title: string;
  destination: string;
  description: string;
  price: number;
  duration: number;
  startDate: string;
  endDate: string;
  status: string;
  maxCapacity: number;
  createdAt: string;
}

const emptyTrip = {
  title: "", destination: "", description: "", price: 0, duration: 0,
  startDate: "", endDate: "", status: "active", maxCapacity: 0,
};

export default function TripsPage() {
  const [trips, setTrips] = useState<Trip[]>([]);
  const [search, setSearch] = useState("");
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [form, setForm] = useState(emptyTrip);
  const [loading, setLoading] = useState(true);

  const fetchTrips = useCallback(async () => {
    try {
      const data = await api.get<Trip[]>("/trips");
      setTrips(data);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { fetchTrips(); }, [fetchTrips]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.title.trim() || !form.destination.trim()) return;
    try {
      if (editingId) {
        await api.patch(`/trips/${editingId}`, form);
      } else {
        await api.post("/trips", form);
      }
      setForm(emptyTrip);
      setEditingId(null);
      setShowForm(false);
      await fetchTrips();
    } catch (err) {
      console.error(err);
    }
  };

  const handleEdit = (trip: Trip) => {
    setForm({
      title: trip.title, destination: trip.destination, description: trip.description || "",
      price: Number(trip.price), duration: trip.duration || 0,
      startDate: trip.startDate ? trip.startDate.split("T")[0] : "",
      endDate: trip.endDate ? trip.endDate.split("T")[0] : "",
      status: trip.status, maxCapacity: trip.maxCapacity || 0,
    });
    setEditingId(trip.id);
    setShowForm(true);
  };

  const handleDelete = async (id: string) => {
    if (!confirm("هل أنت متأكد من حذف هذه الرحلة؟")) return;
    try {
      await api.delete(`/trips/${id}`);
      await fetchTrips();
    } catch (err) {
      console.error(err);
    }
  };

  const filtered = trips.filter((t) => t.title.includes(search) || t.destination.includes(search));

  if (loading) return <div className="flex items-center justify-center h-64"><div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" /></div>;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">الرحلات</h1>
          <p className="text-gray-500 mt-1">إدارة باقات وعروض الرحلات</p>
        </div>
        <button onClick={() => { setForm(emptyTrip); setEditingId(null); setShowForm(true); }}
          className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
          <Plus className="w-4 h-4" /> إضافة رحلة
        </button>
      </div>

      <div className="relative">
        <Search className="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input type="text" placeholder="بحث في الرحلات..." value={search} onChange={(e) => setSearch(e.target.value)}
          className="w-full pr-11 pl-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-auto">
            <div className="flex items-center justify-between p-6 border-b">
              <h2 className="text-lg font-semibold">{editingId ? "تعديل رحلة" : "إضافة رحلة جديدة"}</h2>
              <button onClick={() => setShowForm(false)} className="text-gray-400 hover:text-gray-600"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">عنوان الرحلة *</label>
                  <input type="text" value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">الوجهة *</label>
                  <input type="text" value={form.destination} onChange={(e) => setForm({ ...form, destination: e.target.value })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                <textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} rows={3}
                  className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" />
              </div>
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">السعر</label>
                  <input type="number" value={form.price || ""} onChange={(e) => setForm({ ...form, price: Number(e.target.value) })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">المدة (أيام)</label>
                  <input type="number" value={form.duration || ""} onChange={(e) => setForm({ ...form, duration: Number(e.target.value) })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">العدد الأقصى</label>
                  <input type="number" value={form.maxCapacity || ""} onChange={(e) => setForm({ ...form, maxCapacity: Number(e.target.value) })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">تاريخ البداية</label>
                  <input type="date" value={form.startDate} onChange={(e) => setForm({ ...form, startDate: e.target.value })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">تاريخ النهاية</label>
                  <input type="date" value={form.endDate} onChange={(e) => setForm({ ...form, endDate: e.target.value })}
                    className="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
              </div>
              <div className="flex gap-3 pt-2">
                <button type="submit" className="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                  {editingId ? "تحديث" : "إضافة"}
                </button>
                <button type="button" onClick={() => setShowForm(false)}
                  className="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">إلغاء</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {filtered.length === 0 ? (
        <div className="bg-white rounded-2xl p-12 text-center border border-gray-100">
          <Plane className="w-12 h-12 mx-auto text-gray-300 mb-3" />
          <p className="text-gray-500">{search ? "لا توجد نتائج للبحث" : "لا توجد رحلات بعد"}</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          {filtered.map((trip) => (
            <div key={trip.id} className="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
              <div className="flex items-start justify-between mb-3">
                <div className="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                  <Plane className="w-5 h-5 text-blue-600" />
                </div>
                <div className="flex gap-1">
                  <button onClick={() => handleEdit(trip)}
                    className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><Edit2 className="w-4 h-4" /></button>
                  <button onClick={() => handleDelete(trip.id)}
                    className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"><Trash2 className="w-4 h-4" /></button>
                </div>
              </div>
              <h3 className="font-semibold text-gray-900">{trip.title}</h3>
              <p className="text-sm text-gray-500 mt-1">{trip.destination}</p>
              <div className="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <span className="text-lg font-bold text-blue-600">{Number(trip.price).toLocaleString()} ر.م</span>
                {trip.duration && <span className="text-xs text-gray-400">{trip.duration} أيام</span>}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
