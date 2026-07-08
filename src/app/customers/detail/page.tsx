"use client";

import { Suspense, useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { ArrowLeft, Mail, Phone, IdCard } from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

function CustomerDetailInner() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const id = searchParams.get("id");
  const [customer, setCustomer] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    if (!id) { router.push("/customers"); return; }
    fetch(`${API_BASE}/customers/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setCustomer)
      .catch(() => router.push("/customers"))
      .finally(() => setLoading(false));
  }, [router, id]);

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  if (!customer) return null;

  return (
    <div className="max-w-3xl space-y-6">
      <button onClick={() => router.back()} className="flex items-center gap-2 text-gray-500 hover:text-gray-900">
        <ArrowLeft className="w-4 h-4" />
        <span className="text-sm">Back</span>
      </button>

      <div className="bg-white rounded-2xl border border-gray-100 p-8">
        <div className="flex items-center gap-4 mb-6">
          <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
            <span className="text-blue-600 font-bold text-xl">
              {customer.firstName?.[0]}{customer.lastName?.[0]}
            </span>
          </div>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">{customer.firstName} {customer.lastName}</h1>
            <p className="text-gray-500">{customer.type || "Customer"}</p>
          </div>
        </div>

        <div className="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl">
          {customer.email && (
            <div className="flex items-center gap-2">
              <Mail className="w-4 h-4 text-gray-400" />
              <span className="text-gray-700">{customer.email}</span>
            </div>
          )}
          {customer.phone && (
            <div className="flex items-center gap-2">
              <Phone className="w-4 h-4 text-gray-400" />
              <span className="text-gray-700" dir="ltr">{customer.phone}</span>
            </div>
          )}
          {customer.passportNo && (
            <div className="flex items-center gap-2">
              <IdCard className="w-4 h-4 text-gray-400" />
              <span className="text-gray-700">{customer.passportNo}</span>
            </div>
          )}
          {customer.nationality && (
            <div>
              <p className="text-xs text-gray-500">Nationality</p>
              <p className="font-medium">{customer.nationality}</p>
            </div>
          )}
          {customer.dateOfBirth && (
            <div>
              <p className="text-xs text-gray-500">Date of Birth</p>
              <p className="font-medium">{new Date(customer.dateOfBirth).toLocaleDateString("en-US")}</p>
            </div>
          )}
        </div>

        {customer.notes && (
          <div className="mt-6">
            <p className="text-sm text-gray-500 mb-1">Notes</p>
            <p className="text-gray-700">{customer.notes}</p>
          </div>
        )}
      </div>
    </div>
  );
}

export default function CustomerDetailPage() {
  return <Suspense><CustomerDetailInner /></Suspense>;
}
