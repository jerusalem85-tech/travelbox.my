"use client";

import { Suspense, useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { ArrowLeft, FileText, Search, Loader2 } from "lucide-react";
import InvoiceTemplate from "@/components/documents/InvoiceTemplate";
import QuotationTemplate from "@/components/documents/QuotationTemplate";
import ItineraryTemplate from "@/components/documents/ItineraryTemplate";
import { downloadPdf } from "@/lib/pdf";

const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:3001/api";

const docTypes = [
  { value: 'INVOICE', label: 'Invoice', desc: 'Official invoice for customer' },
  { value: 'QUOTATION', label: 'Quotation', desc: 'Detailed price quotation' },
  { value: 'ITINERARY', label: 'Itinerary', desc: 'Complete trip itinerary' },
];

function GenerateInner() {
  const router = useRouter();
  const [trips, setTrips] = useState<any[]>([]);
  const [tripsLoading, setTripsLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [selectedTrip, setSelectedTrip] = useState<any>(null);
  const [selectedType, setSelectedType] = useState<string>('INVOICE');
  const [generating, setGenerating] = useState(false);
  const [doc, setDoc] = useState<any>(null);
  const [step, setStep] = useState<'select' | 'preview'>('select');

  useEffect(() => {
    const token = localStorage.getItem("travelbox_token");
    if (!token) { router.push("/login"); return; }
    fetch(`${API_BASE}/trips`, { headers: { Authorization: `Bearer ${token}` } })
      .then((r) => r.json())
      .then(setTrips)
      .catch(() => router.push("/login"))
      .finally(() => setTripsLoading(false));
  }, [router]);

  const handleGenerate = async () => {
    if (!selectedTrip) return;
    setGenerating(true);
    const token = localStorage.getItem("travelbox_token");
    const res = await fetch(`${API_BASE}/documents/generate/${selectedTrip.id}`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ documentType: selectedType }),
    });
    const data = await res.json();
    setDoc(data);
    setStep('preview');
    setGenerating(false);
  };

  const filteredTrips = trips.filter((t) =>
    !search || t.referenceNo?.includes(search) || t.name?.includes(search)
  );

  if (tripsLoading) return (
    <div className="flex items-center justify-center h-64">
      <div className="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full" />
    </div>
  );

  return (
    <div className="max-w-4xl space-y-6">
      <button onClick={() => router.back()} className="flex items-center gap-2 text-gray-500 hover:text-gray-900 no-print">
        <ArrowLeft className="w-4 h-4" />
        <span className="text-sm">Back</span>
      </button>

      <h1 className="text-2xl font-bold text-gray-900">Create New Document</h1>

      {step === 'select' && (
        <>
          {/* Step 1: Select Trip */}
          <div className="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 className="font-semibold text-gray-900 mb-4">1. Select Trip</h2>
            <div className="relative mb-4">
              <Search className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input
                type="text"
                placeholder="Search by trip number or name..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              />
            </div>
            <div className="max-h-48 overflow-y-auto space-y-2">
              {filteredTrips.map((t) => (
                <button
                  key={t.id}
                  onClick={() => setSelectedTrip(t)}
                  className={`w-full text-right px-4 py-3 rounded-xl border text-sm transition-colors ${
                    selectedTrip?.id === t.id
                      ? 'border-blue-500 bg-blue-50 text-blue-800'
                      : 'border-gray-100 hover:border-gray-200 text-gray-700'
                  }`}
                >
                  <span className="font-medium">{t.referenceNo}</span>
                  {t.name && <span className="mr-2 text-gray-500">- {t.name}</span>}
                </button>
              ))}
              {filteredTrips.length === 0 && (
                <p className="text-gray-400 text-sm text-center py-4">No trips found</p>
              )}
            </div>
          </div>

          {/* Step 2: Select Type */}
          <div className="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 className="font-semibold text-gray-900 mb-4">2. Select Document Type</h2>
            <div className="grid grid-cols-3 gap-4">
              {docTypes.map((dt) => (
                <button
                  key={dt.value}
                  onClick={() => setSelectedType(dt.value)}
                  className={`p-4 rounded-xl border text-right transition-colors ${
                    selectedType === dt.value
                      ? 'border-blue-500 bg-blue-50'
                      : 'border-gray-100 hover:border-gray-200'
                  }`}
                >
                  <FileText className={`w-6 h-6 mb-2 ${selectedType === dt.value ? 'text-blue-600' : 'text-gray-400'}`} />
                  <p className="font-medium text-sm text-gray-900">{dt.label}</p>
                  <p className="text-xs text-gray-500 mt-1">{dt.desc}</p>
                </button>
              ))}
            </div>
          </div>

          {/* Generate button */}
          <button
            onClick={handleGenerate}
            disabled={!selectedTrip || generating}
            className="w-full py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
          >
            {generating ? (
              <span className="flex items-center justify-center gap-2">
                <Loader2 className="w-4 h-4 animate-spin" /> Creating...
              </span>
            ) : 'Create Document'}
          </button>
        </>
      )}

      {step === 'preview' && doc && (
        <>
          {/* Toolbar */}
          <div className="flex items-center justify-between bg-white rounded-2xl border border-gray-100 p-4 no-print">
            <div className="flex items-center gap-3">
              <span className="text-sm text-gray-500">{doc.documentNo}</span>
              <span className={`px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600`}>
                {docTypes.find(dt => dt.value === doc.documentType)?.label}
              </span>
            </div>
            <div className="flex items-center gap-3">
              <button
                onClick={() => window.print()}
                className="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-xl hover:bg-gray-50 text-sm"
              >
                <span>Print</span>
              </button>
              <button
                onClick={() => downloadPdf('print-area', `${doc.documentNo}.pdf`)}
                className="flex items-center gap-2 px-3 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-sm"
              >
                <span>Download PDF</span>
              </button>
              <button
                onClick={() => router.push(`/documents/detail?id=${doc.id}`)}
                className="flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 text-sm"
              >
                <span>Open Document</span>
              </button>
              <button
                onClick={() => { setStep('select'); setDoc(null); }}
                className="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm"
              >
                New
              </button>
            </div>
          </div>

          {/* Preview */}
          <div className="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-lg">
            {doc.documentType === 'INVOICE' && <InvoiceTemplate doc={doc} trip={doc.trip} />}
            {doc.documentType === 'QUOTATION' && <QuotationTemplate doc={doc} trip={doc.trip} />}
            {doc.documentType === 'ITINERARY' && <ItineraryTemplate trip={doc.trip} />}
          </div>
        </>
      )}
    </div>
  );
}

export default function GeneratePage() {
  return <Suspense><GenerateInner /></Suspense>;
}
