import api from '@/lib/api';

export const tripEngineService = {
  timeline: (tripId: string) =>
    api.get<any[]>(`/api/smart-engine/timeline/${tripId}`).then(r => r.data),

  location: (tripId: string) =>
    api.get<any>(`/api/smart-engine/location/${tripId}`).then(r => r.data),

  validations: (tripId: string) =>
    api.get<any[]>(`/api/smart-engine/validations/${tripId}`).then(r => r.data),

  cities: (tripId: string) =>
    api.get<any>(`/api/smart-engine/cities/${tripId}`).then(r => r.data),

  financial: (tripId: string) =>
    api.get<any>(`/api/smart-engine/financial/${tripId}`).then(r => r.data),
};
