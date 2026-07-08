"use client";

import { Suspense, useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { ArrowLeft, Download, Printer, Check } from "lucide-react";
import { downloadPdf } from "@/lib/pdf";
import InvoiceTemplate from "@/components/documents/InvoiceTemplate";
import QuotationTemplate from "@/components/documents/QuotationTemplate";
import ItineraryTemplate from "@/components/documents/ItineraryTemplate";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

const statusColor: Record<string, string> = {
  DRAFT: "bg-gray-100 text-gray-600", FINAL: "bg-blue-100 text-blue-700",
  SENT: "bg-purple-100 text-purple-700", PAID: "bg-emerald-100 text-emerald-700",
  CANCELLED: "bg-red-100 text-red-700",
};
const statusLabel: Record<string, string> = {
  DRAFT: "Draft", FINAL: "Final", SENT: "Sent", PAID: "Paid", CANCELLED: "Cancelled",
};

const templateNames: Record<string, string> = {
  INVOICE: "Invoice", QUOTATION: "Quotation", ITINERARY: "Itinerary",
};

function DocumentDetailInner() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const id = searchParams.get("id");
  const [doc, setDoc] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [statusUpdating, setStatusUpdating] = useState(false);

  const fetchDoc = () => {
    const token = localStorage.getItem("travelbox_token");
    if (!token || !id) { router.push("/login"); return; }
    fetch(`${API_BASE}/documents/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((r) => r.json())
      .then(setDoc)
      .catch(() => router.push("/documents"))
      .finally(() => setLoading(false));
  };

  useEffect(() => { fetchDoc(); }, [router, id]);

  const handleStatusChange = async (status: string) => {
    setStatusUpdating(true);
    const token = localStorage.getItem("travelbox_token");
    await fetch(`${API_BASE}/documents/${id}/status`, {
      method: 'PATCH',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ status }),
    });
    await fetchDoc();
    setStatusUpdating(false);
  };

  if (loading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  if (!doc) return null;

  const canFinalize = doc.status === 'DRAFT';
  const canSend = doc.status === 'FINAL';
  const filename = `${doc.documentNo}.pdf`;

  const renderTemplate = () => {
    switch (doc.documentType) {
      case 'INVOICE': return <InvoiceTemplate doc={doc} trip={doc.trip} />;
      case 'QUOTATION': return <QuotationTemplate doc={doc} trip={doc.trip} />;
      case 'ITINERARY': return <ItineraryTemplate trip={doc.trip} />;
      default: return <p className="text-gray-400 text-center py-8">Unsupported document type</p>;
    }
  };

  return (
    <div className="space-y-6">
      {/* Toolbar */}
      <div className="flex items-center justify-between no-print">
        <button onClick={() => router.back()} className="flex items-center gap-2 text-gray-500 hover:text-gray-900">
          <ArrowLeft className="w-4 h-4" />
          <span className="text-sm">Back</span>
        </button>

        <div className="flex items-center gap-3">
          <span className={`px-3 py-1 rounded-full text-xs font-medium ${statusColor[doc.status] || ""}`}>
            {statusLabel[doc.status] || doc.status}
          </span>

          {canFinalize && (
            <button
              onClick={() => handleStatusChange('FINAL')}
              disabled={statusUpdating}
              className="flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 text-sm disabled:opacity-50"
            >
              <Check className="w-4 h-4" />
              <span>Final</span>
            </button>
          )}
          {canSend && (
            <button
              onClick={() => handleStatusChange('SENT')}
              disabled={statusUpdating}
              className="flex items-center gap-2 px-3 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 text-sm disabled:opacity-50"
            >
              <Check className="w-4 h-4" />
              <span>Send</span>
            </button>
          )}

          <button
            onClick={() => window.print()}
            className="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-xl hover:bg-gray-50 text-sm"
          >
            <Printer className="w-4 h-4" />
            <span>Print</span>
          </button>
          <button
            onClick={() => downloadPdf('print-area', filename)}
            className="flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-sm"
          >
            <Download className="w-4 h-4" />
            <span>Download PDF</span>
          </button>
        </div>
      </div>

      {/* Document Info */}
      <div className="bg-white rounded-2xl border border-gray-100 p-4 no-print">
        <div className="grid grid-cols-3 gap-4 text-sm">
          <div>
            <p className="text-gray-500 text-xs">Document No.</p>
            <p className="font-medium">{doc.documentNo}</p>
          </div>
          <div>
            <p className="text-gray-500 text-xs">Type</p>
            <p className="font-medium">{templateNames[doc.documentType] || doc.documentType}</p>
          </div>
          <div>
            <p className="text-gray-500 text-xs">Trip</p>
            <p className="font-medium">{doc.trip?.referenceNo || '-'}</p>
          </div>
        </div>
      </div>

      {/* Template Render */}
      <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-lg">
        {renderTemplate()}
      </div>
    </div>
  );
}

export default function DocumentDetailPage() {
  return <Suspense><DocumentDetailInner /></Suspense>;
}
