import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

export interface ValidationWarning {
  type: 'error' | 'warning' | 'suggestion';
  message: string;
  serviceId?: string;
}

@Injectable()
export class DateValidationEngine {
  constructor(private prisma: PrismaService) {}

  async validate(tripId: string): Promise<ValidationWarning[]> {
    const trip = await this.prisma.trip.findUnique({
      where: { id: tripId },
      include: {
        passengers: true,
        services: {
          include: {
            flight: true,
            hotel: true,
            transfer: true,
            tour: true,
            visa: true,
            insurance: true,
            carRental: true,
            cruise: true,
            train: true,
          },
          orderBy: [{ startDate: 'asc' }, { sortOrder: 'asc' }],
        },
      },
    });

    if (!trip) return [];
    const warnings: ValidationWarning[] = [];

    this.checkMissingNights(trip, warnings);
    this.checkDateBoundaries(trip, warnings);
    this.checkFlightOrder(trip, warnings);
    this.checkTransferBeforeFlight(trip, warnings);
    this.checkOverlappingHotels(trip, warnings);
    this.checkPassportValidity(trip, warnings);
    this.checkMissingReturnFlight(trip, warnings);
    this.checkMissingAccommodation(trip, warnings);
    this.checkMissingVisa(trip, warnings);

    return warnings;
  }

  private checkMissingNights(trip: any, warnings: ValidationWarning[]) {
    if (!trip.startDate || !trip.endDate) return;
    const start = new Date(trip.startDate);
    const end = new Date(trip.endDate);
    const totalNights = Math.ceil((end.getTime() - start.getTime()) / 86400000);

    const hotels = trip.services.filter((s: any) => s.type === 'HOTEL' && s.hotel);
    let bookedNights = 0;
    for (const h of hotels) {
      if (h.hotel.checkIn && h.hotel.checkOut) {
        bookedNights += Math.ceil(
          (new Date(h.hotel.checkOut).getTime() - new Date(h.hotel.checkIn).getTime()) / 86400000,
        );
      }
    }

    const missing = totalNights - bookedNights;
    if (missing > 0) {
      warnings.push({
        type: 'warning',
        message: `Missing ${missing} hotel night(s). Trip has ${totalNights} nights but only ${bookedNights} booked.`,
      });
    }
  }

  private checkDateBoundaries(trip: any, warnings: ValidationWarning[]) {
    if (!trip.startDate || !trip.endDate) return;
    const start = new Date(trip.startDate);
    const end = new Date(trip.endDate);

    for (const s of trip.services) {
      if (s.startDate && new Date(s.startDate) < start) {
        warnings.push({
          type: 'error',
          message: `${s.type} "${s.description || s.type}" starts before trip begins.`,
          serviceId: s.id,
        });
      }
      if (s.endDate && new Date(s.endDate) > end) {
        warnings.push({
          type: 'error',
          message: `${s.type} "${s.description || s.type}" ends after trip ends.`,
          serviceId: s.id,
        });
      }
    }
  }

  private checkFlightOrder(trip: any, warnings: ValidationWarning[]) {
    const flights = trip.services
      .filter((s: any) => s.type === 'FLIGHT' && s.flight?.departureTime)
      .sort((a: any, b: any) => new Date(a.flight.departureTime).getTime() - new Date(b.flight.departureTime).getTime());

    for (let i = 0; i < flights.length - 1; i++) {
      const curr = flights[i];
      const next = flights[i + 1];
      const arrival = new Date(curr.flight.arrivalTime || curr.flight.departureTime);
      const nextDeparture = new Date(next.flight.departureTime);
      const gapMinutes = (nextDeparture.getTime() - arrival.getTime()) / 60000;

      if (gapMinutes < 0) {
        warnings.push({
          type: 'error',
          message: `Flight ${next.flight.flightNumber || '#' + next.flight.id} departs before previous flight arrives. Impossible connection.`,
          serviceId: next.id,
        });
      } else if (gapMinutes < 60) {
        warnings.push({
          type: 'warning',
          message: `Only ${Math.round(gapMinutes)}min between flights at ${curr.flight.arrivalAirport || 'connection'}. May be insufficient for connection.`,
          serviceId: next.id,
        });
      }
    }
  }

  private checkTransferBeforeFlight(trip: any, warnings: ValidationWarning[]) {
    const flights = trip.services.filter((s: any) => s.type === 'FLIGHT' && s.flight);
    const transfers = trip.services.filter((s: any) => s.type === 'TRANSFER' && s.transfer);

    for (const t of transfers) {
      if (!t.transfer.pickupTime) continue;
      const pickup = new Date(t.transfer.pickupTime);

      for (const f of flights) {
        if (!f.flight.departureTime) continue;
        const departure = new Date(f.flight.departureTime);
        const gapMinutes = (departure.getTime() - pickup.getTime()) / 60000;

        if (gapMinutes < 0) {
          warnings.push({
            type: 'error',
            message: `Transfer to ${t.transfer.dropoffLocation || 'airport'} is scheduled after flight ${f.flight.flightNumber || ''} departs.`,
            serviceId: t.id,
          });
        } else if (gapMinutes < 60) {
          warnings.push({
            type: 'warning',
            message: `Only ${Math.round(gapMinutes)}min between transfer arrival and flight departure.`,
            serviceId: t.id,
          });
        }
      }
    }
  }

  private checkOverlappingHotels(trip: any, warnings: ValidationWarning[]) {
    const hotels = trip.services
      .filter((s: any) => s.type === 'HOTEL' && s.hotel?.checkIn && s.hotel?.checkOut);

    for (let i = 0; i < hotels.length; i++) {
      for (let j = i + 1; j < hotels.length; j++) {
        const a = hotels[i].hotel;
        const b = hotels[j].hotel;
        if (new Date(a.checkIn) < new Date(b.checkOut) && new Date(b.checkIn) < new Date(a.checkOut)) {
          warnings.push({
            type: 'warning',
            message: `Overlapping hotels: "${a.hotelName || 'Unknown'}" and "${b.hotelName || 'Unknown'}" dates conflict.`,
          });
        }
      }
    }
  }

  private checkPassportValidity(trip: any, warnings: ValidationWarning[]) {
    if (!trip.endDate) return;
    const tripEnd = new Date(trip.endDate);

    for (const p of trip.passengers) {
      if (!p.passportExpiry) {
        warnings.push({
          type: 'warning',
          message: `${p.firstName} ${p.lastName} has no passport expiry on file.`,
        });
        continue;
      }
      const expiry = new Date(p.passportExpiry);
      if (expiry < tripEnd) {
        warnings.push({
          type: 'error',
          message: `${p.firstName} ${p.lastName}'s passport expires before trip ends!`,
        });
      } else {
        const monthsValid = (expiry.getTime() - tripEnd.getTime()) / (86400000 * 30);
        if (monthsValid < 6) {
          warnings.push({
            type: 'warning',
            message: `${p.firstName} ${p.lastName}'s passport has only ${Math.round(monthsValid)} months validity after trip end. Some countries require 6 months.`,
          });
        }
      }
    }
  }

  private checkMissingReturnFlight(trip: any, warnings: ValidationWarning[]) {
    if (!trip.startDate || !trip.endDate) return;
    const flights = trip.services.filter((s: any) => s.type === 'FLIGHT');
    if (flights.length === 0) return;

    const sorted = flights.sort(
      (a: any, b: any) => new Date(b.flight?.departureTime || 0).getTime() - new Date(a.flight?.departureTime || 0).getTime(),
    );
    const lastFlight = sorted[0];
    if (lastFlight.flight?.arrivalTime) {
      const arrival = new Date(lastFlight.flight.arrivalTime);
      const end = new Date(trip.endDate);
      if (arrival > end) {
        warnings.push({
          type: 'warning',
          message: `Last flight arrives after trip end date. Trip ends ${end.toLocaleDateString()} but flight arrives ${arrival.toLocaleDateString()}.`,
        });
      }
    }
  }

  private checkMissingAccommodation(trip: any, warnings: ValidationWarning[]) {
    if (!trip.startDate || !trip.endDate) return;
    const flights = trip.services.filter((s: any) => s.type === 'FLIGHT' && s.flight);
    const hotels = trip.services.filter((s: any) => s.type === 'HOTEL');

    if (flights.length > 0 && hotels.length === 0) {
      warnings.push({
        type: 'suggestion',
        message: 'No accommodation booked. Consider adding hotels for overnight stays.',
      });
    }
  }

  private checkMissingVisa(trip: any, warnings: ValidationWarning[]) {
    const visas = trip.services.filter((s: any) => s.type === 'VISA');
    const destinations = trip.destinationCountries?.length || (trip.destination ? 1 : 0);

    if (destinations > 0 && visas.length === 0) {
      warnings.push({
        type: 'suggestion',
        message: 'No visa services found. Check if visas are required for destinations.',
      });
    }
  }
}
