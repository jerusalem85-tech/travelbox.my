'use client';

import { useParams, useRouter } from 'next/navigation';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { tripService } from '@/services/trips';
import { tripEngineService } from '@/services/trip-engine';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Plane, Hotel, Car, MapPin, Ship, Train, Shield, FileText, AlertTriangle, DollarSign, Users, Clock, ArrowRight, Calendar, CheckCircle2, XCircle, AlertCircle, HelpCircle, Plus, ChevronRight, ChevronLeft, ArrowUp, ArrowDown, Upload } from 'lucide-react';
import { format } from 'date-fns';
import { useState } from 'react';
import AddServiceModal from '@/components/trip/add-service-modal';
import ServiceDetailModal from '@/components/trip/service-detail-modal';
import AddPaymentModal from '@/components/trip/add-payment-modal';
import AddTaskModal from '@/components/trip/add-task-modal';
import AddDocumentModal from '@/components/trip/add-document-modal';
import PassengerModal from '@/components/trip/passenger-modal';
import api from '@/lib/api';
import toast from 'react-hot-toast';

const statusVariant: Record<string, 'success' | 'warning' | 'default' | 'destructive'> = {
  CONFIRMED: 'success', BOOKED: 'success', COMPLETED: 'default',
  DEPOSIT_PAID: 'success', TICKETED: 'success', TRAVELING: 'success',
  LEAD: 'warning', QUOTATION: 'warning', NEGOTIATION: 'warning',
  DOCUMENTS_SENT: 'warning',
  CANCELLED: 'destructive', ARCHIVED: 'destructive',
};

const priorityVariant: Record<string, 'destructive' | 'warning' | 'default'> = {
  URGENT: 'destructive', HIGH: 'destructive', MEDIUM: 'warning', LOW: 'default',
};

const typeIcon: Record<string, any> = {
  FLIGHT_DEPARTURE: Plane, FLIGHT_ARRIVAL: Plane,
  HOTEL_CHECKIN: Hotel, HOTEL_CHECKOUT: Hotel,
  TRANSFER: Car, TOUR: MapPin, CRUISE: Ship, TRAIN: Train,
  VISA: FileText, INSURANCE: Shield, CAR_RENTAL: Car,
};

const typeColors: Record<string, string> = {
  FLIGHT_DEPARTURE: 'text-blue-500 bg-blue-50 border-blue-200',
  FLIGHT_ARRIVAL: 'text-green-500 bg-green-50 border-green-200',
  HOTEL_CHECKIN: 'text-purple-500 bg-purple-50 border-purple-200',
  HOTEL_CHECKOUT: 'text-purple-500 bg-purple-50 border-purple-200',
  TRANSFER: 'text-orange-500 bg-orange-50 border-orange-200',
  TOUR: 'text-cyan-500 bg-cyan-50 border-cyan-200',
  CRUISE: 'text-teal-500 bg-teal-50 border-teal-200',
  TRAIN: 'text-amber-500 bg-amber-50 border-amber-200',
  VISA: 'text-red-500 bg-red-50 border-red-200',
  INSURANCE: 'text-emerald-500 bg-emerald-50 border-emerald-200',
  CAR_RENTAL: 'text-orange-500 bg-orange-50 border-orange-200',
};

const tabs = ['Timeline', 'Services', 'Financial', 'Tasks', 'Documents', 'Passengers'];

function formatDateTime(d: string | null | undefined): { date: string; time: string } {
  if (!d) return { date: '', time: '' };
  try { return { date: format(new Date(d), 'MMM dd, yyyy'), time: format(new Date(d), 'HH:mm') }; } catch { return { date: d, time: '' }; }
}

export default function TripDetailPage() {
  const params = useParams();
  const router = useRouter();
  const tripId = params.id as string;
  const [activeTab, setActiveTab] = useState('Timeline');
  const [showServiceModal, setShowServiceModal] = useState(false);
  const [selectedService, setSelectedService] = useState<any>(null);
  const [showServiceDetail, setShowServiceDetail] = useState(false);
  const [showPaymentModal, setShowPaymentModal] = useState(false);
  const [showTaskModal, setShowTaskModal] = useState(false);
  const [showDocumentModal, setShowDocumentModal] = useState(false);
  const [showPassengerModal, setShowPassengerModal] = useState(false);
  const queryClient = useQueryClient();

  const workflowMutation = useMutation({
    mutationFn: (toStage: string) => api.post(`/api/smart-engine/workflow/${tripId}/transition`, { toStage }).then(r => r.data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['trip', tripId] });
      toast.success('Workflow stage updated');
    },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Transition failed'),
  });

  const reorderMutation = useMutation({
    mutationFn: ({ serviceIds }: { serviceIds: string[] }) => api.put(`/api/trips/${tripId}/services/reorder`, { serviceIds }).then(r => r.data),
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['trip', tripId] }); toast.success('Services reordered'); },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Reorder failed'),
  });

  const moveService = (index: number, direction: -1 | 1) => {
    const services = trip?.services ? [...trip.services] : [];
    if (index + direction < 0 || index + direction >= services.length) return;
    [services[index], services[index + direction]] = [services[index + direction], services[index]];
    reorderMutation.mutate({ serviceIds: services.map((s: any) => s.id) });
  };

  const { data: availableStages } = useQuery({
    queryKey: ['workflow-available', tripId],
    queryFn: () => api.get(`/api/smart-engine/workflow/${tripId}/available`).then(r => r.data),
    enabled: !!tripId,
  });

  const { data: trip, isLoading: tripLoading } = useQuery({
    queryKey: ['trip', tripId],
    queryFn: () => tripService.getById(tripId),
    enabled: !!tripId,
  });

  const { data: timeline } = useQuery({
    queryKey: ['trip-timeline', tripId],
    queryFn: () => tripEngineService.timeline(tripId),
    enabled: !!tripId,
  });

  const { data: location } = useQuery({
    queryKey: ['trip-location', tripId],
    queryFn: () => tripEngineService.location(tripId),
    enabled: !!tripId,
  });

  const { data: validations } = useQuery({
    queryKey: ['trip-validations', tripId],
    queryFn: () => tripEngineService.validations(tripId),
    enabled: !!tripId,
  });

  const { data: cities } = useQuery({
    queryKey: ['trip-cities', tripId],
    queryFn: () => tripEngineService.cities(tripId),
    enabled: !!tripId,
  });

  const { data: financial } = useQuery({
    queryKey: ['trip-financial', tripId],
    queryFn: () => tripEngineService.financial(tripId),
    enabled: !!tripId,
  });

  if (tripLoading) {
    return (
      <div className="space-y-6">
        <div className="h-16 bg-muted rounded animate-pulse" />
        <div className="h-96 bg-muted rounded animate-pulse" />
      </div>
    );
  }

  if (!trip) return <div className="text-center py-12 text-muted-foreground">Trip not found</div>;

  const errors = validations?.filter((v: any) => v.type === 'error') || [];
  const warnings = validations?.filter((v: any) => v.type === 'warning') || [];
  const suggestions = validations?.filter((v: any) => v.type === 'suggestion') || [];

  const renderTimeline = () => (
    <div className="flex flex-col lg:flex-row gap-6">
      <div className="flex-1 min-w-0">
        <Card className="p-4">
          <h2 className="text-lg font-semibold mb-4 flex items-center gap-2"><Clock className="h-4 w-4" /> Trip Timeline</h2>
          {!timeline || timeline.length === 0 ? (
            <p className="text-sm text-muted-foreground py-8 text-center">No timeline entries yet. Add services to build the timeline.</p>
          ) : (
            <div className="relative">
              {timeline.map((entry: any, i: number) => {
                const prevDate = i > 0 ? timeline[i - 1]?.date : null;
                const showDateHeader = !prevDate || prevDate !== entry.date;
                const Icon = typeIcon[entry.type] || HelpCircle;
                const colorClass = typeColors[entry.type] || 'text-gray-500 bg-gray-50 border-gray-200';

                return (
                    <div key={`${entry.id}-${entry.type}-${i}`}>
                      {showDateHeader && (
                        <div className="flex items-center gap-2 py-2 mt-2 mb-1">
                          <Calendar className="h-4 w-4 text-muted-foreground" />
                          <span className="text-sm font-semibold text-muted-foreground">{formatDateTime(entry.date).date}</span>
                          <div className="flex-1 border-t border-dashed border-muted" />
                        </div>
                      )}
                      <div className="flex gap-3 pb-4 relative group cursor-pointer" onClick={() => {
                        const svc = trip.services?.find((s: any) => s.id === entry.id);
                        if (svc) { setSelectedService(svc); setShowServiceDetail(true); }
                      }}>
                        <div className="flex flex-col items-center">
                          <div className={`h-8 w-8 rounded-full flex items-center justify-center border ${colorClass}`}>
                            <Icon className="h-4 w-4" />
                          </div>
                          {i < timeline.length - 1 && <div className="w-px flex-1 bg-muted mt-1" />}
                        </div>
                        <div className="flex-1 pb-2">
                          <div className="flex items-start justify-between">
                            <div>
                              <p className="text-sm font-medium">{entry.title}</p>
                              {entry.subtitle && <p className="text-xs text-muted-foreground">{entry.subtitle}</p>}
                            </div>
                            {entry.time && (
                              <span className="text-xs font-mono text-muted-foreground shrink-0 ml-2">{entry.time}</span>
                            )}
                          </div>
                        </div>
                      </div>
                    </div>
                );
              })}
            </div>
          )}
        </Card>
      </div>

      <div className="w-full lg:w-80 space-y-4 shrink-0">
        <Card className="p-4">
          <h3 className="text-sm font-semibold mb-3 flex items-center gap-2"><MapPin className="h-4 w-4 text-blue-500" /> Current Location</h3>
          {location ? (
            <div>
              <p className="text-lg font-bold">{location.currentCity}</p>
              {location.nextMove && (
                <div className="flex items-center gap-1 mt-2 text-sm text-muted-foreground">
                  <ArrowRight className="h-3 w-3" /> {location.nextMove}
                </div>
              )}
              {location.warnings?.length > 0 && (
                <div className="mt-2 text-xs text-red-500 space-y-1">
                  {location.warnings.map((w: string, i: number) => <p key={i}>⚠ {w}</p>)}
                </div>
              )}
            </div>
          ) : <p className="text-sm text-muted-foreground">Loading...</p>}
        </Card>

        {(errors.length > 0 || warnings.length > 0 || suggestions.length > 0) && (
          <Card className="p-4">
            <h3 className="text-sm font-semibold mb-3 flex items-center gap-2"><AlertTriangle className="h-4 w-4 text-amber-500" /> Alerts ({errors.length + warnings.length})</h3>
            <div className="space-y-2">
              {errors.map((v: any, i: number) => (
                <div key={i} className="flex gap-2 text-xs">
                  <XCircle className="h-3.5 w-3.5 text-red-500 shrink-0 mt-0.5" />
                  <span className="text-red-600">{v.message}</span>
                </div>
              ))}
              {warnings.map((v: any, i: number) => (
                <div key={i} className="flex gap-2 text-xs">
                  <AlertCircle className="h-3.5 w-3.5 text-amber-500 shrink-0 mt-0.5" />
                  <span className="text-amber-700">{v.message}</span>
                </div>
              ))}
              {suggestions.map((v: any, i: number) => (
                <div key={i} className="flex gap-2 text-xs">
                  <HelpCircle className="h-3.5 w-3.5 text-blue-500 shrink-0 mt-0.5" />
                  <span className="text-muted-foreground">{v.message}</span>
                </div>
              ))}
            </div>
          </Card>
        )}

        {cities && (
          <Card className="p-4">
            <h3 className="text-sm font-semibold mb-3 flex items-center gap-2"><MapPin className="h-4 w-4 text-purple-500" /> Trip Overview</h3>
            <div className="grid grid-cols-2 gap-2 text-xs">
              <div className="bg-muted/50 rounded p-2"><span className="text-muted-foreground">Flights</span><p className="font-semibold">{cities.flightCount}</p></div>
              <div className="bg-muted/50 rounded p-2"><span className="text-muted-foreground">Hotels</span><p className="font-semibold">{cities.hotelCount}</p></div>
              <div className="bg-muted/50 rounded p-2"><span className="text-muted-foreground">Transfers</span><p className="font-semibold">{cities.transferCount}</p></div>
              <div className="bg-muted/50 rounded p-2"><span className="text-muted-foreground">Nights</span><p className="font-semibold">{cities.nightCount}</p></div>
            </div>
            {cities.cities?.length > 0 && (
              <div className="mt-3"><p className="text-xs text-muted-foreground mb-1">Cities:</p><div className="flex flex-wrap gap-1">{cities.cities.map((c: string) => <Badge key={c} variant="outline" className="text-xs">{c}</Badge>)}</div></div>
            )}
          </Card>
        )}

        {financial && (
          <Card className="p-4">
            <h3 className="text-sm font-semibold mb-3 flex items-center gap-2"><DollarSign className="h-4 w-4 text-emerald-500" /> Financial</h3>
            <div className="space-y-2 text-sm">
              <div className="flex justify-between"><span className="text-muted-foreground">Selling</span><span className="font-semibold">${(financial.totalSelling || 0).toLocaleString()}</span></div>
              <div className="flex justify-between"><span className="text-muted-foreground">Cost</span><span>${(financial.totalCost || 0).toLocaleString()}</span></div>
              <div className="flex justify-between border-t pt-2"><span className="font-semibold">Profit</span><span className="font-semibold text-emerald-600">${(financial.totalProfit || 0).toLocaleString()}</span></div>
              <div className="flex justify-between text-xs"><span className="text-muted-foreground">Margin</span><span className={financial.margin < 10 ? 'text-red-500' : 'text-emerald-600'}>{financial.margin?.toFixed(1)}%</span></div>
              <div className="border-t pt-2 mt-2 space-y-1 text-xs">
                <div className="flex justify-between"><span className="text-muted-foreground">Paid</span><span className="text-green-600">${(financial.totalPaid || 0).toLocaleString()}</span></div>
                <div className="flex justify-between"><span className="text-muted-foreground">Remaining</span><span className={financial.totalRemaining > 0 ? 'text-amber-600' : 'text-green-600'}>${(financial.totalRemaining || 0).toLocaleString()}</span></div>
              </div>
            </div>
          </Card>
        )}
      </div>
    </div>
  );

  const renderServices = () => (
    <Card className="p-4">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-lg font-semibold">Services</h2>
        <Button size="sm" onClick={() => setShowServiceModal(true)}><Plus className="h-4 w-4 mr-1" /> Add Service</Button>
      </div>
      {!trip.services || trip.services.length === 0 ? (
        <p className="text-sm text-muted-foreground py-8 text-center">No services added yet.</p>
      ) : (
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead><tr className="border-b bg-muted/50 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
              <th className="px-4 py-3">#</th>
              <th className="px-4 py-3">Type</th>
              <th className="px-4 py-3">Description</th>
              <th className="px-4 py-3">Supplier</th>
              <th className="text-right px-4 py-3">Cost</th>
              <th className="text-right px-4 py-3">Selling</th>
              <th className="text-right px-4 py-3">Profit</th>
              <th className="px-4 py-3">Status</th>
              <th className="px-2 py-3"></th>
            </tr></thead>
            <tbody className="divide-y">
              {trip.services.map((s: any, i: number) => (
                <tr key={s.id} className="hover:bg-muted/50 text-sm cursor-pointer" onClick={() => { setSelectedService(s); setShowServiceDetail(true); }}>
                  <td className="px-4 py-3 text-muted-foreground">{i + 1}</td>
                  <td className="px-4 py-3"><Badge variant="outline">{s.type}</Badge></td>
                  <td className="px-4 py-3">{s.description || '-'}</td>
                  <td className="px-4 py-3 text-muted-foreground">{s.supplier?.companyName || '-'}</td>
                  <td className="px-4 py-3 text-right">${(s.costPrice || 0).toLocaleString()}</td>
                  <td className="px-4 py-3 text-right">${(s.sellingPrice || 0).toLocaleString()}</td>
                  <td className={`px-4 py-3 text-right font-medium ${(s.profit || 0) < 0 ? 'text-red-500' : 'text-green-600'}`}>${(s.profit || 0).toLocaleString()}</td>
                  <td className="px-4 py-3"><Badge variant={s.status === 'CONFIRMED' ? 'success' : s.status === 'CANCELLED' ? 'destructive' : 'default'} className="text-xs">{s.status}</Badge></td>
                  <td className="px-2 py-3">
                    <div className="flex flex-col gap-0.5">
                      <button className="h-5 w-5 flex items-center justify-center rounded hover:bg-muted text-muted-foreground" onClick={(e) => { e.stopPropagation(); moveService(i, -1); }} disabled={i === 0 || reorderMutation.isPending}><ArrowUp className="h-3 w-3" /></button>
                      <button className="h-5 w-5 flex items-center justify-center rounded hover:bg-muted text-muted-foreground" onClick={(e) => { e.stopPropagation(); moveService(i, 1); }} disabled={i === (trip.services?.length ?? 0) - 1 || reorderMutation.isPending}><ArrowDown className="h-3 w-3" /></button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </Card>
  );

  const renderFinancial = () => (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
      <Card className="p-4">
        <h3 className="text-sm font-semibold mb-4">Profit & Loss</h3>
        <div className="space-y-3">
          <div className="flex justify-between text-sm"><span>Total Selling</span><span className="font-semibold text-lg">${(financial?.totalSelling || 0).toLocaleString()}</span></div>
          <div className="flex justify-between text-sm"><span>Total Cost</span><span>${(financial?.totalCost || 0).toLocaleString()}</span></div>
          <div className="border-t pt-3 flex justify-between"><span className="font-semibold">Profit</span><span className={`font-bold text-lg ${(financial?.totalProfit || 0) < 0 ? 'text-red-500' : 'text-emerald-600'}`}>${(financial?.totalProfit || 0).toLocaleString()}</span></div>
          <div className="flex justify-between text-sm"><span>Margin</span><span className={financial?.margin < 10 ? 'text-red-500 font-medium' : ''}>{financial?.margin?.toFixed(1)}%</span></div>
          <div className="flex justify-between text-sm"><span>Commission</span><span>${(financial?.totalCommission || 0).toLocaleString()}</span></div>
        </div>
      </Card>
      <Card className="p-4">
        <div className="flex items-center justify-between mb-4">
          <h3 className="text-sm font-semibold">Payments</h3>
          <Button size="sm" variant="outline" onClick={() => setShowPaymentModal(true)}><DollarSign className="h-3.5 w-3.5 mr-1" /> Record Payment</Button>
        </div>
        <div className="space-y-3">
          <div className="flex justify-between text-sm"><span>Customer Paid</span><span className="text-green-600 font-medium">${(financial?.totalPaid || 0).toLocaleString()}</span></div>
          <div className="flex justify-between text-sm"><span>Customer Remaining</span><span className={financial?.totalRemaining > 0 ? 'text-amber-600 font-medium' : 'text-green-600'}>${(financial?.totalRemaining || 0).toLocaleString()}</span></div>
          <div className="border-t pt-3 flex justify-between text-sm"><span>Supplier Paid</span><span className="text-green-600 font-medium">${(financial?.supplierTotalPaid || 0).toLocaleString()}</span></div>
          <div className="flex justify-between text-sm"><span>Supplier Balance</span><span className={financial?.supplierTotalRemaining > 0 ? 'text-amber-600 font-medium' : 'text-green-600'}>${(financial?.supplierTotalRemaining || 0).toLocaleString()}</span></div>
        </div>
      </Card>
    </div>
  );

  const renderTasks = () => (
    <Card className="p-4">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-lg font-semibold">Tasks</h2>
        <Button size="sm" variant="outline" onClick={() => setShowTaskModal(true)}><Plus className="h-3.5 w-3.5 mr-1" /> Add Task</Button>
      </div>
      {!trip.tasks || trip.tasks.length === 0 ? (
        <p className="text-sm text-muted-foreground py-8 text-center">No tasks for this trip.</p>
      ) : (
        <div className="space-y-2">
          {trip.tasks.map((t: any) => (
            <div key={t.id} className="flex items-center justify-between p-3 rounded-lg border hover:bg-muted/50">
              <div className="flex items-center gap-3">
                <CheckCircle2 className={`h-4 w-4 ${t.status === 'COMPLETED' ? 'text-green-500' : 'text-muted-foreground'}`} />
                <div><p className={`text-sm ${t.status === 'COMPLETED' ? 'line-through text-muted-foreground' : ''}`}>{t.title}</p><p className="text-xs text-muted-foreground">{t.priority} · {t.dueDate ? format(new Date(t.dueDate), 'MMM dd') : 'No due date'}</p></div>
              </div>
              <Badge variant={t.status === 'COMPLETED' ? 'success' : t.status === 'IN_PROGRESS' ? 'warning' : 'default'} className="text-xs">{t.status}</Badge>
            </div>
          ))}
        </div>
      )}
    </Card>
  );

  const renderDocuments = () => (
    <Card className="p-4">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-lg font-semibold">Documents</h2>
        <Button size="sm" variant="outline" onClick={() => setShowDocumentModal(true)}><Upload className="h-3.5 w-3.5 mr-1" /> Add Document</Button>
      </div>
      {!trip.documents || trip.documents.length === 0 ? (
        <p className="text-sm text-muted-foreground py-8 text-center">No documents attached.</p>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
          {trip.documents.map((d: any) => (
            <div key={d.id} className="flex items-center gap-3 p-3 rounded-lg border hover:bg-muted/50">
              <FileText className="h-5 w-5 text-muted-foreground" />
              <div className="flex-1 min-w-0"><p className="text-sm font-medium truncate">{d.name}</p><p className="text-xs text-muted-foreground">{d.category}</p></div>
            </div>
          ))}
        </div>
      )}
    </Card>
  );

  const renderPassengers = () => (
    <Card className="p-4">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-lg font-semibold">Passengers</h2>
        <Button size="sm" variant="outline" onClick={() => setShowPassengerModal(true)}><Plus className="h-3.5 w-3.5 mr-1" /> Add Passenger</Button>
      </div>
      {!trip.passengers || trip.passengers.length === 0 ? (
        <p className="text-sm text-muted-foreground py-8 text-center">No passengers added yet.</p>
      ) : (
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead><tr className="border-b bg-muted/50 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
              <th className="px-4 py-3">Name</th>
              <th className="px-4 py-3">DOB</th>
              <th className="px-4 py-3">Gender</th>
              <th className="px-4 py-3">Nationality</th>
              <th className="px-4 py-3">Passport #</th>
              <th className="px-4 py-3">Expiry</th>
            </tr></thead>
            <tbody className="divide-y">
              {trip.passengers.map((p: any) => (
                <tr key={p.id} className="hover:bg-muted/50 text-sm">
                  <td className="px-4 py-3 font-medium">{p.firstName} {p.lastName}</td>
                  <td className="px-4 py-3 text-muted-foreground">{p.dateOfBirth ? format(new Date(p.dateOfBirth), 'MMM dd, yyyy') : '-'}</td>
                  <td className="px-4 py-3">{p.gender || '-'}</td>
                  <td className="px-4 py-3">{p.nationality || '-'}</td>
                  <td className="px-4 py-3 font-mono text-xs">{p.passportNumber || '-'}</td>
                  <td className="px-4 py-3">{p.passportExpiry ? format(new Date(p.passportExpiry), 'MMM dd, yyyy') : '-'}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </Card>
  );

  const tabContent: Record<string, () => any> = {
    Timeline: renderTimeline,
    Services: renderServices,
    Financial: renderFinancial,
    Tasks: renderTasks,
    Documents: renderDocuments,
    Passengers: renderPassengers,
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="flex items-center gap-3 flex-wrap">
          <div>
            <div className="flex items-center gap-2">
              <h1 className="text-2xl font-bold">{trip.name || 'Trip'}</h1>
              <Badge variant={statusVariant[trip.status] || 'default'}>{trip.status}</Badge>
              {trip.priority && <Badge variant={priorityVariant[trip.priority] || 'default'}>{trip.priority}</Badge>}
            </div>
            <p className="text-sm text-muted-foreground flex items-center gap-2 mt-0.5">
              <span className="font-mono">{trip.tripNumber}</span>
              {trip.customer && <><span>&middot;</span><span>{trip.customer.firstName} {trip.customer.lastName}</span></>}
              {trip.startDate && <><span>&middot;</span><span>{format(new Date(trip.startDate), 'MMM dd')} - {format(new Date(trip.endDate || trip.startDate), 'MMM dd, yyyy')}</span></>}
              <span className="ml-2"><Button variant="ghost" size="sm" className="h-6 text-xs gap-1" onClick={() => setShowPassengerModal(true)}><Users className="h-3 w-3" />{trip.passengers?.length || 0} pax</Button></span>
            </p>
          </div>
        </div>
        <div className="flex items-center gap-2 shrink-0">
          {availableStages?.map((stage: string) => (
            <Button key={stage} size="sm" variant="outline" onClick={() => workflowMutation.mutate(stage)} disabled={workflowMutation.isPending}>
              → {stage.replace(/_/g, ' ')}
            </Button>
          ))}
        </div>
      </div>

      <div className="border-b">
        <nav className="flex gap-4 -mb-px">
          {tabs.map((tab) => (
            <button key={tab} onClick={() => setActiveTab(tab)} className={`px-1 py-3 text-sm font-medium border-b-2 transition-colors ${activeTab === tab ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'}`}>
              {tab}
            </button>
          ))}
        </nav>
      </div>

      {tabContent[activeTab]?.()}

      <AddServiceModal tripId={tripId} open={showServiceModal} onClose={() => setShowServiceModal(false)} />
      <ServiceDetailModal tripId={tripId} service={selectedService} open={showServiceDetail} onClose={() => setShowServiceDetail(false)} />
      <AddPaymentModal tripId={tripId} open={showPaymentModal} onClose={() => setShowPaymentModal(false)} />
      <AddTaskModal tripId={tripId} open={showTaskModal} onClose={() => setShowTaskModal(false)} />
      <AddDocumentModal tripId={tripId} open={showDocumentModal} onClose={() => setShowDocumentModal(false)} />
      <PassengerModal tripId={tripId} open={showPassengerModal} onClose={() => setShowPassengerModal(false)} />
    </div>
  );
}
