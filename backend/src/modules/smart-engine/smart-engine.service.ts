import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import type { TripValidationResult } from '../../shared/interfaces';

@Injectable()
export class SmartEngineService {
  private readonly logger = new Logger(SmartEngineService.name);

  constructor(private prisma: PrismaService) {}

  async validateTrip(tripId: string): Promise<TripValidationResult> {
    const trip = await this.prisma.trip.findUnique({
      where: { id: tripId },
      include: {
        passengers: true,
        services: {
          include: {
            flight: {
              include: { passengers: true },
            },
            hotel: {
              include: { guests: true },
            },
            transfer: true,
            tour: true,
            visa: {
              include: { passengers: true },
            },
            insurance: true,
            carRental: true,
          },
          orderBy: { sortOrder: 'asc' },
        },
      },
    });

    if (!trip) throw new Error('Trip not found');

    const warnings: string[] = [];
    const errors: string[] = [];
    const suggestions: string[] = [];

    this.checkMissingHotelNights(trip, warnings, errors, suggestions);
    this.checkFlightScheduleConflicts(trip, warnings, errors);
    this.checkTransferConflicts(trip, warnings, errors);
    this.checkPassportExpiry(trip, warnings, errors);
    this.checkVisaIssues(trip, warnings, errors, suggestions);
    this.checkDuplicatePassengers(trip, warnings, errors);
    this.checkDuplicateTicketNumbers(trip, warnings, errors);
    this.checkOverlappingHotels(trip, warnings, errors);
    this.checkMissingServices(trip, suggestions);
    this.calculateTripMetrics(trip, warnings);

    return {
      isValid: errors.length === 0,
      warnings,
      errors,
      suggestions,
    };
  }

  private checkMissingHotelNights(trip: any, warnings: string[], errors: string[], suggestions: string[]) {
    const flights = trip.services.filter((s: any) => s.type === 'FLIGHT' && s.flight);
    const hotels = trip.services.filter((s: any) => s.type === 'HOTEL' && s.hotel);

    if (flights.length > 0 && hotels.length === 0) {
      suggestions.push('No hotels booked for this trip. Consider adding accommodations.');
    }

    const flightDates = flights
      .map((f: any) => f.flight?.departureTime)
      .filter(Boolean)
      .sort();

    const hotelDates = hotels.map((h: any) => ({ in: h.hotel?.checkIn, out: h.hotel?.checkOut })).filter((h: any) => h.in);

    if (flightDates.length > 1 && hotelDates.length > 0) {
      const firstFlight = new Date(flightDates[0]);
      const lastFlight = new Date(flightDates[flightDates.length - 1]);
      const tripDuration = Math.ceil((lastFlight.getTime() - firstFlight.getTime()) / (1000 * 3600 * 24));

      let totalHotelNights = 0;
      for (const h of hotelDates) {
        if (h.in && h.out) {
          totalHotelNights += Math.ceil((new Date(h.out).getTime() - new Date(h.in).getTime()) / (1000 * 3600 * 24));
        }
      }

      if (totalHotelNights < tripDuration - 1 && tripDuration > 1) {
        warnings.push(`Trip has ${tripDuration} days but only ${totalHotelNights} hotel nights booked. Missing ~${tripDuration - 1 - totalHotelNights} night(s).`);
      }
    }
  }

  private checkFlightScheduleConflicts(trip: any, warnings: string[], errors: string[]) {
    const flights = trip.services
      .filter((s: any) => s.type === 'FLIGHT' && s.flight)
      .map((s: any) => s.flight)
      .filter((f: any) => f.departureTime && f.arrivalTime)
      .sort((a: any, b: any) => new Date(a.departureTime).getTime() - new Date(b.departureTime).getTime());

    for (let i = 0; i < flights.length - 1; i++) {
      const current = flights[i];
      const next = flights[i + 1];
      const arrivalTime = new Date(current.arrivalTime).getTime();
      const nextDeparture = new Date(next.departureTime).getTime();
      const gapMinutes = (nextDeparture - arrivalTime) / (1000 * 60);

      if (gapMinutes < 0) {
        errors.push(`Flight ${next.flightNumber || next.id} departs before previous flight arrives. Impossible schedule.`);
      } else if (gapMinutes < 60) {
        warnings.push(`Only ${gapMinutes} minutes between flight ${current.flightNumber || ''} arrival and flight ${next.flightNumber || ''} departure. May be tight for connections.`);
      }
    }
  }

  private checkTransferConflicts(trip: any, warnings: string[], errors: string[]) {
    const flights = trip.services.filter((s: any) => s.type === 'FLIGHT' && s.flight).map((s: any) => s.flight);
    const transfers = trip.services.filter((s: any) => s.type === 'TRANSFER' && s.transfer).map((s: any) => s.transfer);

    for (const transfer of transfers) {
      if (!transfer.pickupTime) continue;
      const pickupTime = new Date(transfer.pickupTime).getTime();

      for (const flight of flights) {
        if (!flight.arrivalTime) continue;
        const arrivalTime = new Date(flight.arrivalTime).getTime();
        const gapMinutes = (pickupTime - arrivalTime) / (1000 * 60);

        if (gapMinutes < 0 && gapMinutes > -120) {
          continue;
        }
        if (gapMinutes > 0 && gapMinutes < 30) {
          warnings.push(`Transfer pickup is only ${gapMinutes} minutes after flight arrival. May not be enough time for baggage claim.`);
        }
      }
    }
  }

  private checkPassportExpiry(trip: any, warnings: string[], errors: string[]) {
    const now = new Date();
    for (const passenger of trip.passengers) {
      if (!passenger.passportExpiry) {
        warnings.push(`${passenger.firstName} ${passenger.lastName} has no passport expiry date.`);
        continue;
      }
      const expiry = new Date(passenger.passportExpiry);
      const monthsUntilExpiry = (expiry.getTime() - now.getTime()) / (1000 * 3600 * 24 * 30);
      if (monthsUntilExpiry < 6 && monthsUntilExpiry > 0) {
        warnings.push(`${passenger.firstName} ${passenger.lastName}'s passport expires in ${Math.round(monthsUntilExpiry)} months. Some countries require 6 months validity.`);
      }
      if (monthsUntilExpiry <= 0) {
        errors.push(`${passenger.firstName} ${passenger.lastName}'s passport has expired!`);
      }

      if (trip.startDate && passenger.passportExpiry) {
        const tripStart = new Date(trip.startDate);
        const passportValidUntil = new Date(passenger.passportExpiry);
        const validAfterTrip = (passportValidUntil.getTime() - tripStart.getTime()) / (1000 * 3600 * 24 * 30);
        if (validAfterTrip < 6 && validAfterTrip > 0) {
          warnings.push(`${passenger.firstName} ${passenger.lastName}'s passport has only ${Math.round(validAfterTrip)} months validity remaining at trip start.`);
        }
      }
    }
  }

  private checkVisaIssues(trip: any, warnings: string[], errors: string[], suggestions: string[]) {
    const visas = trip.services.filter((s: any) => s.type === 'VISA' && s.visa);
    const destinations = (trip.destination || '').split(',').map((d: string) => d.trim());

    if (destinations.length > 0 && visas.length === 0) {
      for (const dest of destinations) {
        if (['USA', 'UK', 'SCHENGEN', 'AUSTRALIA', 'CANADA', 'CHINA', 'INDIA', 'RUSSIA', 'BRAZIL', 'TURKEY'].includes(dest.toUpperCase())) {
          suggestions.push(`Destination "${dest}" typically requires a visa for most nationalities. Consider adding a visa service.`);
        }
      }
    }

    for (const visa of visas) {
      if (visa.visa?.expiryDate && trip.startDate) {
        const visaExpiry = new Date(visa.visa.expiryDate);
        if (visaExpiry < new Date(trip.startDate)) {
          errors.push(`Visa for ${visa.visa.country || 'unknown'} expires before trip starts.`);
        }
      }
    }
  }

  private checkDuplicatePassengers(trip: any, warnings: string[], errors: string[]) {
    const passportMap = new Map<string, any[]>();
    const nameMap = new Map<string, any[]>();

    for (const p of trip.passengers) {
      const key = `${p.firstName.toLowerCase()}_${p.lastName.toLowerCase()}`;
      if (!nameMap.has(key)) nameMap.set(key, []);
      nameMap.get(key)!.push(p);

      if (p.passportNumber) {
        if (!passportMap.has(p.passportNumber)) passportMap.set(p.passportNumber, []);
        passportMap.get(p.passportNumber)!.push(p);
      }
    }

    for (const [passport, passengers] of passportMap) {
      if (passengers.length > 1) {
        warnings.push(`Duplicate passport number ${passport} for ${passengers.map((p: any) => p.firstName + ' ' + p.lastName).join(', ')}.`);
      }
    }

    for (const [name, passengers] of nameMap) {
      if (passengers.length > 1 && !passengers.some((p: any) => p.passportNumber)) {
        warnings.push(`Possible duplicate: ${passengers[0].firstName} ${passengers[0].lastName} appears ${passengers.length} times without passport numbers.`);
      }
    }
  }

  private checkDuplicateTicketNumbers(trip: any, warnings: string[], errors: string[]) {
    const ticketNumbers = new Map<string, string>();

    for (const service of trip.services) {
      if (service.type === 'FLIGHT' && service.flight) {
        if (service.flight.ticketNumber) {
          if (ticketNumbers.has(service.flight.ticketNumber)) {
            warnings.push(`Duplicate ticket number ${service.flight.ticketNumber} found on flight ${service.flight.flightNumber || ''}.`);
          } else {
            ticketNumbers.set(service.flight.ticketNumber, service.flight.flightNumber || '');
          }
        }
        for (const fp of service.flight.passengers || []) {
          if (fp.ticketNumber) {
            if (ticketNumbers.has(fp.ticketNumber)) {
              warnings.push(`Duplicate ticket number ${fp.ticketNumber} for passenger on flight ${service.flight.flightNumber || ''}.`);
            } else {
              ticketNumbers.set(fp.ticketNumber, `${service.flight.flightNumber || ''} - ${fp.passengerId}`);
            }
          }
        }
      }
    }
  }

  private checkOverlappingHotels(trip: any, warnings: string[], errors: string[]) {
    const hotels = trip.services
      .filter((s: any) => s.type === 'HOTEL' && s.hotel)
      .map((s: any) => s.hotel)
      .filter((h: any) => h.checkIn && h.checkOut);

    for (let i = 0; i < hotels.length; i++) {
      for (let j = i + 1; j < hotels.length; j++) {
        const a = hotels[i];
        const b = hotels[j];
        const aIn = new Date(a.checkIn).getTime();
        const aOut = new Date(a.checkOut).getTime();
        const bIn = new Date(b.checkIn).getTime();
        const bOut = new Date(b.checkOut).getTime();

        if (aIn < bOut && bIn < aOut) {
          warnings.push(`Overlapping hotel stays: ${a.hotelName || 'Hotel'} (${a.checkIn.toLocaleDateString()} - ${a.checkOut.toLocaleDateString()}) overlaps with ${b.hotelName || 'Hotel'} (${b.checkIn.toLocaleDateString()} - ${b.checkOut.toLocaleDateString()}).`);
        }
      }
    }
  }

  private checkMissingServices(trip: any, suggestions: string[]) {
    const types = new Set(trip.services.map((s: any) => s.type));

    if (trip.destination && !types.has('HOTEL') && trip.services.length > 0) {
      suggestions.push('No hotel booked. Consider adding accommodation.');
    }

    if (types.has('FLIGHT') && !types.has('TRANSFER')) {
      const flights = trip.services.filter((s: any) => s.type === 'FLIGHT');
      if (flights.length > 1) {
        suggestions.push('Multiple flights detected. Consider adding airport transfers.');
      }
    }

    if (trip.destination && !types.has('INSURANCE')) {
      suggestions.push('Travel insurance not included. Consider adding for passenger protection.');
    }

    const totalPassengers = trip.passengers?.length || 0;
    for (const service of trip.services) {
      if (service.type === 'HOTEL' && service.hotel) {
        const guestCount = service.hotel.guests?.length || 0;
        if (guestCount < totalPassengers && totalPassengers > 1) {
          suggestions.push(`Hotel ${service.hotel.hotelName || ''} has ${guestCount} guest(s) but trip has ${totalPassengers} passenger(s).`);
        }
      }
    }
  }

  calculateTripMetrics(trip: any, warnings: string[]) {
    const totalCost = trip.services.reduce((sum: number, s: any) => sum + (s.costPrice || 0), 0);
    const totalSelling = trip.services.reduce((sum: number, s: any) => sum + (s.sellingPrice || 0), 0);
    const totalProfit = trip.services.reduce((sum: number, s: any) => sum + (s.profit || 0), 0);

    if (totalProfit < 0) {
      warnings.push(`Trip is at a loss of $${Math.abs(totalProfit).toLocaleString()}. Review pricing.`);
    }

    if (totalProfit > 0 && totalSelling > 0) {
      const margin = (totalProfit / totalSelling) * 100;
      if (margin < 10) {
        warnings.push(`Profit margin is only ${margin.toFixed(1)}%. Consider increasing selling prices or reducing costs.`);
      }
    }
  }

  async analyze(tripId: string) {
    const validation = await this.validateTrip(tripId);
    return {
      tripId,
      ...validation,
      summary: {
        status: validation.isValid ? 'OK' : validation.errors.length > 0 ? 'ERRORS' : 'WARNINGS',
        totalIssues: validation.errors.length + validation.warnings.length,
        suggestions: validation.suggestions.length,
      },
    };
  }
}
