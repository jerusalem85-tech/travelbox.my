import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

export interface LocationInfo {
  currentCity: string;
  currentCountry: string;
  nextMove: string;
  warnings: string[];
}

@Injectable()
export class LocationEngine {
  constructor(private prisma: PrismaService) {}

  async getCurrentLocation(tripId: string): Promise<LocationInfo> {
    const services = await this.prisma.service.findMany({
      where: { tripId },
      include: { flight: true, transfer: true, cruise: true, train: true },
      orderBy: [{ startDate: 'asc' }, { sortOrder: 'asc' }],
    });

    const now = new Date();
    const warnings: string[] = [];
    let currentCity = 'Unknown';
    let currentCountry = 'Unknown';
    let nextMove = '';

    for (const s of services) {
      if (s.flight?.arrivalTime && new Date(s.flight.arrivalTime) <= now) {
        currentCity = s.flight.arrivalAirport || currentCity;
      }
      if (s.flight?.departureTime && new Date(s.flight.departureTime) <= now && s.flight.arrivalTime && new Date(s.flight.arrivalTime) > now) {
        currentCity = `In flight to ${s.flight.arrivalAirport || 'destination'}`;
      }
      if (s.transfer?.dropoffLocation && s.transfer.pickupTime && new Date(s.transfer.pickupTime) <= now) {
        currentCity = s.transfer.dropoffLocation;
      }
    }

    const upcoming = services.find(s => {
      const date = s.startDate || (s.flight?.departureTime) || (s as any).hotel?.checkIn;
      return date && new Date(date) > now;
    });

    if (upcoming) {
      if (upcoming.flight) {
        nextMove = `Flight to ${upcoming.flight.arrivalAirport || 'destination'}`;
      } else if (upcoming.transfer) {
        nextMove = `Transfer to ${upcoming.transfer.dropoffLocation || 'destination'}`;
      } else {
        nextMove = upcoming.description || upcoming.type;
      }
    }

    const flightServices = services.filter(s => s.type === 'FLIGHT' && s.flight);
    for (const s of services) {
      if (s.type === 'HOTEL') {
        const hotel = s as any;
        if (hotel.hotel?.city && hotel.hotel.city !== currentCity && !services.some(
          ts => ts.type === 'TRANSFER' && ts.transfer?.dropoffLocation === hotel.hotel.city
        )) {
          warnings.push(`Hotel is in ${hotel.hotel.city} but traveler is in ${currentCity}. No transfer found.`);
        }
      }
    }

    return { currentCity, currentCountry, nextMove, warnings };
  }
}
