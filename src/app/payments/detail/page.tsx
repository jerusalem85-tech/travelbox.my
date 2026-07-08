"use client";

import { Suspense, useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { ArrowLeft } from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

function PaymentDetailInner() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const id = searchParams.get("id");
  const [payment, setPayment] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    if (!id) { router.push("/payments"); return; }
    fetch(`${API_BASE}/payments/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setPayment)
      .catch(() => router.push("/payments"))
      .finally(() => setLoading(false));
  }, [router, id]);

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  if (!payment) return null;

  const directionLabel = payment.direction === "INFLOW" ? "Inflow" : "Outflow";
  const directionColor = payment.direction === "INFLOW" ? "text-green-600 bg-green-50" : "text-red-600 bg-red-50";
  const statusLabel: Record<string, string> = {
    PENDING: "Pending", PARTIAL: "Partial", PAID: "Paid", REFUNDED: "Refunded", CANCELLED: "Cancelled",
  };

  return (
    <div className="max-w-3xl space-y-6">
      <button onClick={() => router.back()} className="flex items-center gap-2 text-gray-500 hover:text-gray-900">
        <ArrowLeft className="w-4 h-4" />
        <span className="text-sm">Back</span>
      </button>

      <div className="bg-white rounded-2xl border border-gray-100 p-8">
        <div className="flex items-start justify-between mb-6">
          <div>
            <div className="flex items-center gap-3 mb-2">
              <span className={`px-3 py-1 rounded-full text-sm font-medium ${directionColor}`}>
                {directionLabel}
              </span>
              {payment.referenceNo && (
                <span className="text-sm text-gray-400">{payment.referenceNo}</span>
              )}
            </div>
            <h1 className="text-2xl font-bold text-gray-900">
              {Number(payment.amount).toLocaleString()} {payment.currency}
            </h1>
          </div>
          <span className="text-sm text-gray-500">
            {new Date(payment.paymentDate).toLocaleDateString("en-US")}
          </span>
        </div>

        <div className="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl">
          <div>
            <p className="text-xs text-gray-500">Status</p>
            <p className="font-medium">{statusLabel[payment.status] || payment.status}</p>
          </div>
          <div>
            <p className="text-xs text-gray-500">Payment Method</p>
            <p className="font-medium">{payment.method}</p>
          </div>
          {payment.currency !== "USD" && (
            <div>
              <p className="text-xs text-gray-500">Exchange Rate</p>
              <p className="font-medium">{payment.exchangeRate}</p>
            </div>
          )}
          {payment.trip && (
            <div>
              <p className="text-xs text-gray-500">Trip</p>
              <p className="font-medium">{payment.trip.referenceNo}</p>
            </div>
          )}
          {payment.customer && (
            <div>
              <p className="text-xs text-gray-500">Customer</p>
              <p className="font-medium">{payment.customer.firstName} {payment.customer.lastName}</p>
            </div>
          )}
          {payment.supplier && (
            <div>
              <p className="text-xs text-gray-500">Supplier</p>
              <p className="font-medium">{payment.supplier.name}</p>
            </div>
          )}
        </div>

        {payment.description && (
          <div className="mt-6">
            <p className="text-sm text-gray-500 mb-1">Description</p>
            <p className="text-gray-700">{payment.description}</p>
          </div>
        )}

        {payment.notes && (
          <div className="mt-4">
            <p className="text-sm text-gray-500 mb-1">Notes</p>
            <p className="text-gray-700">{payment.notes}</p>
          </div>
        )}

        {payment.journalEntries?.length > 0 && (
          <div className="mt-8">
            <h3 className="font-semibold text-gray-900 mb-3">Journal Entries</h3>
            <table className="w-full text-sm">
              <thead>
                <tr className="bg-gray-50 text-right">
                  <th className="px-3 py-2 text-gray-600 text-xs font-medium">Account</th>
                  <th className="px-3 py-2 text-gray-600 text-xs font-medium">Type</th>
                  <th className="px-3 py-2 text-gray-600 text-xs font-medium text-left">Amount</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {payment.journalEntries.map((je: any) => (
                  <tr key={je.id}>
                    <td className="px-3 py-2">
                      <span className="text-xs text-gray-400 ml-1">{je.account?.code}</span>
                      {je.account?.name}
                    </td>
                    <td className="px-3 py-2">
                      <span className={`text-xs font-medium ${je.entryType === "DEBIT" ? "text-red-600" : "text-green-600"}`}>
                        {je.entryType === "DEBIT" ? "Debit" : "Credit"}
                      </span>
                    </td>
                    <td className="px-3 py-2 text-left font-medium">{Number(je.amount).toLocaleString()} $</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}

export default function PaymentDetailPage() {
  return <Suspense><PaymentDetailInner /></Suspense>;
}
