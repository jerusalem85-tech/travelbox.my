import './print.css';

const formatCurrency = (n: any) => Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2 });
const fmtDate = (d: string) => d ? new Date(d).toLocaleDateString('ar-SA') : '-';

function ServiceRow({ label, items }: { label: string; items: any[] }) {
  if (!items || items.length === 0) return null;
  return items.map((item: any, i: number) => (
    <tr key={`${label}-${i}`}>
      <td className="text-gray-800">{label}</td>
      <td className="text-gray-600">{item.supplier?.name || item.airline || item.provider || item.name || '-'}</td>
      <td className="text-center text-gray-600">{item.bookingRef || item.flightNo || item.policyNo || '-'}</td>
      <td className="text-center text-gray-600">1</td>
      <td className="text-left font-medium" dir="ltr">{formatCurrency(item.sellPrice)}</td>
      <td className="text-left font-medium" dir="ltr">{formatCurrency(item.sellPrice)}</td>
    </tr>
  ));
}

export default function QuotationTemplate({ doc, trip }: { doc: any; trip: any }) {
  const mainCustomer = trip.customers?.[0]?.customer;
  const allServices = [
    ...(trip.flights || []),
    ...(trip.hotels || []),
    ...(trip.transfers || []),
    ...(trip.visas || []),
    ...(trip.insurances || []),
    ...(trip.activities || []),
  ];
  const subtotal = allServices.reduce((s: number, i: any) => s + Number(i.sellPrice || 0), 0);
  const validityDate = new Date();
  validityDate.setDate(validityDate.getDate() + 7);

  return (
    <div id="print-area" className="document-template bg-white max-w-[210mm] mx-auto p-8">
      {/* Header */}
      <div className="flex justify-between items-start mb-6 border-b-2 border-gray-800 pb-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">TravelBox</h1>
          <p className="text-xs text-gray-500 mt-1">Travel Agency Management System</p>
        </div>
        <div className="text-left">
          <h2 className="text-lg font-bold text-green-700">Quotation</h2>
          <p className="text-xs text-gray-500 mt-1">QUOTATION</p>
        </div>
      </div>

      {/* Document Info */}
      <div className="flex justify-between mb-4">
        <div>
          <p className="text-xs text-gray-500">Quotation No.</p>
          <p className="font-bold text-gray-900">{doc.documentNo}</p>
        </div>
        <div className="text-left">
          <p className="text-xs text-gray-500">Issue Date</p>
          <p className="font-medium">{fmtDate(doc.createdAt)}</p>
        </div>
      </div>

      <div className="p-2 bg-green-50 rounded text-sm text-green-800 mb-4">
        Valid until: {validityDate.toLocaleDateString('ar-SA')}
      </div>

      {/* Customer */}
      {mainCustomer && (
        <div className="mb-6 p-3 bg-gray-50 rounded">
          <p className="text-xs text-gray-500 mb-1">Client</p>
          <p className="font-bold">{mainCustomer.firstName} {mainCustomer.lastName}</p>
          <p className="text-xs text-gray-600">{mainCustomer.email} | {mainCustomer.phone || ''}</p>
        </div>
      )}

      {/* Trip ref */}
      <div className="mb-4">
        <p className="text-xs text-gray-500">Trip</p>
        <p className="font-medium">{trip.referenceNo} - {trip.name || ''}</p>
        {trip.startDate && (
          <p className="text-xs text-gray-500 mt-1">
            Date: {fmtDate(trip.startDate)} {trip.endDate ? `to ${fmtDate(trip.endDate)}` : ''}
          </p>
        )}
      </div>

      {/* Services Table */}
      <table className="mb-6">
        <thead>
          <tr>
            <th className="w-1/6">Service</th>
            <th className="w-2/6">Description</th>
            <th className="w-1/6">Ref</th>
            <th className="w-1/12">Qty</th>
            <th className="w-1/6 text-left">Unit Price</th>
            <th className="w-1/6 text-left">Total</th>
          </tr>
        </thead>
        <tbody>
          <ServiceRow label="Flight" items={trip.flights} />
          <ServiceRow label="Hotel" items={trip.hotels} />
          <ServiceRow label="Transfer" items={trip.transfers} />
          <ServiceRow label="Visa" items={trip.visas} />
          <ServiceRow label="Insurance" items={trip.insurances} />
          <ServiceRow label="Activity" items={trip.activities} />
        </tbody>
      </table>

      {/* Summary */}
      <div className="mr-auto w-64 space-y-2">
        <div className="flex justify-between text-sm">
          <span className="text-gray-600">Subtotal</span>
          <span className="font-medium" dir="ltr">{formatCurrency(subtotal)} USD</span>
        </div>
        <div className="flex justify-between font-bold text-base border-t-2 border-gray-800 pt-2">
          <span>Estimated Total</span>
          <span dir="ltr">{formatCurrency(subtotal)} USD</span>
        </div>
      </div>

      {/* Terms */}
      <div className="mt-8 pt-4 border-t border-gray-300">
        <p className="font-semibold text-sm mb-2">Terms & Conditions</p>
        <ul className="text-xs text-gray-600 space-y-1 pr-4">
          <li>Quotation valid for 7 days from issue date</li>
          <li>Prices subject to change based on availability at time of actual booking</li>
          <li>All prices in USD</li>
          <li>Including VAT</li>
        </ul>
      </div>
    </div>
  );
}
