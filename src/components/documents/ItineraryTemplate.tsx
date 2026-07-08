import './print.css';

const fmtDate = (d: string) => d ? new Date(d).toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '-';
const fmtTime = (d: string) => d ? new Date(d).toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }) : '-';
const fmtDateTime = (d: string) => d ? `${fmtDate(d)}` : '-';

function FlightCard({ f }: { f: any }) {
  return (
    <div className="border border-blue-200 rounded-lg p-3 mb-2 bg-blue-50">
      <div className="flex justify-between items-start">
        <div>
          <p className="font-bold text-gray-900">{f.airline} - {f.flightNo}</p>
          <p className="text-xs text-gray-600">
            {f.departureAirport} → {f.arrivalAirport}
            {f.departureTerminal ? ` (Departure ${f.departureTerminal})` : ''}
            {f.arrivalTerminal ? ` (Arrival ${f.arrivalTerminal})` : ''}
          </p>
        </div>
        <div className="text-left">
          <p className="text-sm font-medium">{fmtDateTime(f.departureDate)}</p>
          <p className="text-xs text-gray-500">{f.bookingRef ? `Booking: ${f.bookingRef}` : ''}</p>
        </div>
      </div>
      {f.supplier && <p className="text-xs text-gray-400 mt-1">Supplier: {f.supplier.name}</p>}
    </div>
  );
}

function HotelCard({ h }: { h: any }) {
  return (
    <div className="border border-emerald-200 rounded-lg p-3 mb-2 bg-emerald-50">
      <div className="flex justify-between">
        <div>
          <p className="font-bold text-gray-900">{h.hotelName || h.name}</p>
          <p className="text-xs text-gray-600">{h.roomType || ''} {h.boardBasis ? `- ${h.boardBasis}` : ''}</p>
        </div>
        <div className="text-left text-xs">
          <p>Check-in: {fmtDate(h.checkIn)}</p>
          <p>Check-out: {fmtDate(h.checkOut)}</p>
        </div>
      </div>
      {h.bookingRef && <p className="text-xs text-gray-500 mt-1">Booking: {h.bookingRef}</p>}
    </div>
  );
}

function TransferCard({ t }: { t: any }) {
  return (
    <div className="border border-purple-200 rounded-lg p-3 mb-2 bg-purple-50">
      <div className="flex justify-between">
        <div>
          <p className="font-bold text-gray-900">{t.type === 'ARRIVAL' ? 'Pickup' : 'Drop-off'} - {t.transferType || t.type}</p>
          <p className="text-xs text-gray-600">{t.pickupLocation || ''} {t.dropoffLocation ? `→ ${t.dropoffLocation}` : ''}</p>
        </div>
        <div className="text-left text-xs">
          <p>{fmtDateTime(t.date)}</p>
          {t.bookingRef && <p>Booking: {t.bookingRef}</p>}
        </div>
      </div>
    </div>
  );
}

function ActivityCard({ a }: { a: any }) {
  return (
    <div className="border border-amber-200 rounded-lg p-3 mb-2 bg-amber-50">
      <div className="flex justify-between">
        <div>
          <p className="font-bold text-gray-900">{a.name}</p>
          <p className="text-xs text-gray-600">{a.location || ''} {a.duration ? `- Duration: ${a.duration}` : ''}</p>
        </div>
        <div className="text-left text-xs">
          <p>{fmtDate(a.date)}</p>
          {a.startTime && <p>{a.startTime}</p>}
        </div>
      </div>
      {a.description && <p className="text-xs text-gray-500 mt-1">{a.description}</p>}
    </div>
  );
}

function VisaCard({ v }: { v: any }) {
  return (
    <div className="border border-red-200 rounded-lg p-3 mb-2 bg-red-50">
      <div className="flex justify-between">
        <div>
          <p className="font-bold text-gray-900">Visa - {v.country || v.type}</p>
          <p className="text-xs text-gray-600">{v.visaType || v.type}</p>
        </div>
        <div className="text-left text-xs">
          {v.entryDate && <p>Entry Date: {fmtDate(v.entryDate)}</p>}
          {v.expiryDate && <p>Expiry Date: {fmtDate(v.expiryDate)}</p>}
        </div>
      </div>
      {v.bookingRef && <p className="text-xs text-gray-500 mt-1">Reference: {v.bookingRef}</p>}
    </div>
  );
}

function InsuranceCard({ ins }: { ins: any }) {
  return (
    <div className="border border-gray-200 rounded-lg p-3 mb-2 bg-gray-50">
      <div className="flex justify-between">
        <div>
          <p className="font-bold text-gray-900">Insurance - {ins.provider}</p>
          <p className="text-xs text-gray-600">{ins.type}</p>
        </div>
        <div className="text-left text-xs">
          <p>From: {fmtDate(ins.startDate)}</p>
          <p>To: {fmtDate(ins.endDate)}</p>
        </div>
      </div>
      {ins.policyNo && <p className="text-xs text-gray-500 mt-1">Policy No.: {ins.policyNo}</p>}
    </div>
  );
}

export default function ItineraryTemplate({ trip }: { trip: any }) {
  const mainCustomer = trip.customers?.[0]?.customer;

  return (
    <div id="print-area" className="document-template bg-white max-w-[210mm] mx-auto p-8">
      {/* Header */}
      <div className="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 className="text-2xl font-bold text-gray-900">TravelBox</h1>
        <h2 className="text-lg font-bold text-blue-700 mt-2">Trip Itinerary</h2>
        <p className="text-sm font-medium mt-1">{trip.referenceNo} - {trip.name || ''}</p>
        {trip.startDate && (
          <p className="text-xs text-gray-500">
            From {fmtDate(trip.startDate)} {trip.endDate ? `to ${fmtDate(trip.endDate)}` : ''}
          </p>
        )}
      </div>

      {/* Customer Info */}
      <div className="mb-6 p-3 bg-gray-50 rounded">
        <p className="text-xs text-gray-500 mb-1">Client</p>
        {mainCustomer ? (
          <p className="font-bold">{mainCustomer.firstName} {mainCustomer.lastName} - {mainCustomer.email}</p>
        ) : (
          <p className="text-gray-600">-</p>
        )}
        {trip.passengers?.length > 0 && (
          <div className="mt-2 text-xs text-gray-600">
            <span className="font-medium">Number of travelers: {trip.passengers.length}</span>
            <span className="mr-4">
              {trip.passengers.map((p: any, i: number) => (
                <span key={p.id}>{p.firstName} {p.lastName}{i < trip.passengers.length - 1 ? ', ' : ''}</span>
              ))}
            </span>
          </div>
        )}
      </div>

      {/* Services by Type */}
      <div className="space-y-4">
        {trip.flights?.length > 0 && (
          <div>
            <h3 className="font-bold text-lg text-blue-800 mb-2 border-b border-blue-200 pb-1">Flights</h3>
            {trip.flights.map((f: any) => <FlightCard key={f.id} f={f} />)}
          </div>
        )}

        {trip.hotels?.length > 0 && (
          <div>
            <h3 className="font-bold text-lg text-emerald-800 mb-2 border-b border-emerald-200 pb-1">Hotels</h3>
            {trip.hotels.map((h: any) => <HotelCard key={h.id} h={h} />)}
          </div>
        )}

        {trip.transfers?.length > 0 && (
          <div>
            <h3 className="font-bold text-lg text-purple-800 mb-2 border-b border-purple-200 pb-1">Transfers</h3>
            {trip.transfers.map((t: any) => <TransferCard key={t.id} t={t} />)}
          </div>
        )}

        {trip.activities?.length > 0 && (
          <div>
            <h3 className="font-bold text-lg text-amber-800 mb-2 border-b border-amber-200 pb-1">Activities</h3>
            {trip.activities.map((a: any) => <ActivityCard key={a.id} a={a} />)}
          </div>
        )}

        {trip.visas?.length > 0 && (
          <div>
            <h3 className="font-bold text-lg text-red-800 mb-2 border-b border-red-200 pb-1">Visas</h3>
            {trip.visas.map((v: any) => <VisaCard key={v.id} v={v} />)}
          </div>
        )}

        {trip.insurances?.length > 0 && (
          <div>
            <h3 className="font-bold text-lg text-gray-800 mb-2 border-b border-gray-200 pb-1">Insurance</h3>
            {trip.insurances.map((ins: any) => <InsuranceCard key={ins.id} ins={ins} />)}
          </div>
        )}
      </div>

      {/* Emergency */}
      <div className="mt-8 p-3 border border-red-300 rounded-lg bg-red-50">
        <p className="font-bold text-red-800 mb-1">Emergency Contacts</p>
        <p className="text-xs text-gray-600">Contact travel agency: info@travelbox.my</p>
      </div>
    </div>
  );
}
