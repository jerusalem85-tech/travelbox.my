"use client";

import { Suspense, useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { ArrowLeft } from "lucide-react";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

function InvoiceDetailInner() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const id = searchParams.get("id");
  const [invoice, setInvoice] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    if (!id) { router.push("/invoices"); return; }
    fetch(`${API_BASE}/invoices/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setInvoice)
      .catch(() => router.push("/invoices"))
      .finally(() => setLoading(false));
  }, [router, id]);

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  if (!invoice) return null;

  const statusColor: Record<string, string> = {
    DRAFT: "bg-gray-100 text-gray-600", FINAL: "bg-blue-100 text-blue-700",
    SENT: "bg-purple-100 text-purple-700", PAID: "bg-emerald-100 text-emerald-700",
    CANCELLED: "bg-red-100 text-red-700",
  };
  const statusLabel: Record<string, string> = {
    DRAFT: "Draft", FINAL: "Final", SENT: "Sent", PAID: "Paid", CANCELLED: "Cancelled",
  };

  return (
    <div className="max-w-4xl space-y-6">
      <button onClick={() => router.back()} className="flex items-center gap-2 text-gray-500 hover:text-gray-900">
        <ArrowLeft className="w-4 h-4" />
        <span className="text-sm">Back</span>
      </button>

      <div className="bg-white rounded-2xl border border-gray-100 p-8">
        <div className="flex items-start justify-between mb-8">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">{invoice.invoiceNo}</h1>
            <span className={`inline-block mt-2 px-3 py-1 rounded-full text-sm font-medium ${statusColor[invoice.status] || ""}`}>
              {statusLabel[invoice.status] || invoice.status}
            </span>
          </div>
          <div className="text-left">
            <p className="text-sm text-gray-500">Issue Date</p>
            <p className="font-medium">{new Date(invoice.issueDate).toLocaleDateString("en-US")}</p>
            {invoice.dueDate && (
              <>
                <p className="text-sm text-gray-500 mt-2">Due Date</p>
                <p className="font-medium">{new Date(invoice.dueDate).toLocaleDateString("en-US")}</p>
              </>
            )}
          </div>
        </div>

        {invoice.customer && (
          <div className="mb-8 p-4 bg-gray-50 rounded-xl">
            <p className="text-sm text-gray-500 mb-1">Customer</p>
            <p className="font-medium">{invoice.customer.firstName} {invoice.customer.lastName}</p>
            <p className="text-sm text-gray-600">{invoice.customer.email}</p>
            {invoice.customer.phone && <p className="text-sm text-gray-600">{invoice.customer.phone}</p>}
          </div>
        )}

        <div className="border-t border-gray-100 pt-6">
          <div className="space-y-3">
            <div className="flex justify-between text-sm">
              <span className="text-gray-500">Subtotal</span>
              <span className="font-medium">{Number(invoice.subtotal).toLocaleString()} $</span>
            </div>
            {Number(invoice.discountAmount) > 0 && (
              <div className="flex justify-between text-sm">
                <span className="text-gray-500">Discount ({Number(invoice.discountPct)}%)</span>
                <span className="font-medium text-red-600">-{Number(invoice.discountAmount).toLocaleString()} $</span>
              </div>
            )}
            {Number(invoice.taxAmount) > 0 && (
              <div className="flex justify-between text-sm">
                <span className="text-gray-500">Tax ({Number(invoice.taxRate)}%)</span>
                <span className="font-medium">{Number(invoice.taxAmount).toLocaleString()} $</span>
              </div>
            )}
            <div className="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
              <span>Total</span>
              <span>{Number(invoice.totalAmount).toLocaleString()} $</span>
            </div>
            {Number(invoice.balanceDue) > 0 && (
              <div className="flex justify-between text-sm text-red-600 font-medium">
                <span>Balance Due</span>
                <span>{Number(invoice.balanceDue).toLocaleString()} $</span>
              </div>
            )}
          </div>
        </div>

        {invoice.trip && (
          <div className="mt-8 p-4 bg-blue-50 rounded-xl">
            <p className="text-sm text-blue-600 mb-2">Related Trip</p>
            <p className="font-medium">{invoice.trip.referenceNo} - {invoice.trip.name}</p>
          </div>
        )}

        {invoice.notes && (
          <div className="mt-6">
            <p className="text-sm text-gray-500 mb-1">Notes</p>
            <p className="text-gray-700">{invoice.notes}</p>
          </div>
        )}
      </div>
    </div>
  );
}

export default function InvoiceDetailPage() {
  return <Suspense><InvoiceDetailInner /></Suspense>;
}
