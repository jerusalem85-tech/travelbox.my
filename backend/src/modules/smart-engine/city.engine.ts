import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

export interface CitySummary {
  cities: string[];
  countries: string[];
  flightCount: number;
  hotelCount: number;
  transferCount: number;
  tourCount: number;
  nightCount: number;
}

@Injectable()
export class CityEngine {
  constructor(private prisma: PrismaService) {}

  async analyze(tripId: string): Promise<CitySummary> {
    const services = await this.prisma.service.findMany({
      where: { tripId },
      include: {
        flight: true,
        hotel: true,
        transfer: true,
        tour: true,
      },
    });

    const cities = new Set<string>();
    const countries = new Set<string>();

    for (const s of services) {
      if (s.flight?.departureAirport) {
        cities.add(this.airportToCity(s.flight.departureAirport));
      }
      if (s.flight?.arrivalAirport) {
        cities.add(this.airportToCity(s.flight.arrivalAirport));
      }
      if (s.hotel?.hotelName) {
        cities.add(s.hotel.hotelName);
      }
      if (s.transfer?.pickupLocation) {
        cities.add(s.transfer.pickupLocation);
      }
      if (s.transfer?.dropoffLocation) {
        cities.add(s.transfer.dropoffLocation);
      }
      if (s.tour?.meetingPoint) {
        cities.add(s.tour.meetingPoint);
      }
    }

    let nightCount = 0;
    for (const s of services) {
      if (s.hotel?.checkIn && s.hotel?.checkOut) {
        nightCount += Math.ceil(
          (s.hotel.checkOut.getTime() - s.hotel.checkIn.getTime()) / 86400000,
        );
      }
    }

    return {
      cities: Array.from(cities),
      countries: Array.from(countries),
      flightCount: services.filter(s => s.type === 'FLIGHT').length,
      hotelCount: services.filter(s => s.type === 'HOTEL').length,
      transferCount: services.filter(s => s.type === 'TRANSFER').length,
      tourCount: services.filter(s => s.type === 'TOUR').length,
      nightCount,
    };
  }

  async updateTripCities(tripId: string): Promise<void> {
    const summary = await this.analyze(tripId);
    await this.prisma.trip.update({
      where: { id: tripId },
      data: {
        destinationCities: JSON.stringify(summary.cities),
        destinationCountries: JSON.stringify(summary.countries),
        duration: summary.nightCount || undefined,
      },
    });
  }

  private airportToCity(code: string): string {
    const map: Record<string, string> = {
      DXB: 'Dubai', AUH: 'Abu Dhabi', NYC: 'New York', LON: 'London',
      CAI: 'Cairo', RUH: 'Riyadh', IST: 'Istanbul', BKK: 'Bangkok',
      PAR: 'Paris', MLE: 'Malé', TLV: 'Tel Aviv', HKT: 'Phuket',
    };
    return map[code] || code;
  }
}
