import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

export interface TimelineEntry {
  id: string;
  date: string;
  time: string;
  type: string;
  title: string;
  subtitle: string;
  icon: string;
  serviceId: string;
  sortOrder: number;
}

@Injectable()
export class TimelineEngine {
  constructor(private prisma: PrismaService) {}

  async buildTimeline(tripId: string): Promise<TimelineEntry[]> {
    const services = await this.prisma.service.findMany({
      where: { tripId },
      include: {
        flight: true,
        hotel: true,
        transfer: true,
        tour: true,
        cruise: true,
        visa: true,
        insurance: true,
        carRental: true,
        train: true,
      },
      orderBy: [{ startDate: 'asc' }, { sortOrder: 'asc' }],
    });

    const entries: TimelineEntry[] = [];

    for (const s of services) {
      const base = {
        id: s.id,
        serviceId: s.id,
        sortOrder: s.sortOrder,
      };

      switch (s.type) {
        case 'FLIGHT': {
          if (s.flight?.departureTime) {
            entries.push({
              ...base,
              date: s.flight.departureTime.toISOString().split('T')[0],
              time: s.flight.departureTime.toTimeString().slice(0, 5),
              type: 'FLIGHT_DEPARTURE',
              title: `Flight ${s.flight.flightNumber || ''}`,
              subtitle: `${s.flight.departureAirport || ''} → ${s.flight.arrivalAirport || ''}`,
              icon: 'plane',
            });
          }
          if (s.flight?.arrivalTime) {
            entries.push({
              ...base,
              date: s.flight.arrivalTime.toISOString().split('T')[0],
              time: s.flight.arrivalTime.toTimeString().slice(0, 5),
              type: 'FLIGHT_ARRIVAL',
              title: `Arrival ${s.flight.arrivalAirport || ''}`,
              subtitle: s.flight.arrivalTerminal ? `Terminal ${s.flight.arrivalTerminal}` : '',
              icon: 'arrival',
            });
          }
          break;
        }
        case 'HOTEL': {
          if (s.hotel?.checkIn) {
            entries.push({
              ...base,
              date: s.hotel.checkIn.toISOString().split('T')[0],
              time: s.hotel.checkIn.toTimeString().slice(0, 5),
              type: 'HOTEL_CHECKIN',
              title: `Check-in ${s.hotel.hotelName || 'Hotel'}`,
              subtitle: s.hotel.roomType || '',
              icon: 'hotel',
            });
          }
          if (s.hotel?.checkOut) {
            entries.push({
              ...base,
              date: s.hotel.checkOut.toISOString().split('T')[0],
              time: s.hotel.checkOut.toTimeString().slice(0, 5),
              type: 'HOTEL_CHECKOUT',
              title: `Check-out ${s.hotel.hotelName || 'Hotel'}`,
              subtitle: '',
              icon: 'hotel-out',
            });
          }
          break;
        }
        case 'TRANSFER': {
          if (s.transfer?.pickupTime) {
            entries.push({
              ...base,
              date: s.transfer.pickupTime.toISOString().split('T')[0],
              time: s.transfer.pickupTime.toTimeString().slice(0, 5),
              type: 'TRANSFER',
              title: `Transfer: ${s.transfer.pickupLocation || ''} → ${s.transfer.dropoffLocation || ''}`,
              subtitle: s.transfer.vehicleType || '',
              icon: 'car',
            });
          }
          break;
        }
        case 'TOUR': {
          const tourDate = s.tour?.startTime || s.startDate;
          if (tourDate) {
            entries.push({
              ...base,
              date: new Date(tourDate).toISOString().split('T')[0],
              time: s.tour?.startTime ? new Date(s.tour.startTime).toTimeString().slice(0, 5) : '',
              type: 'TOUR',
              title: s.tour?.tourName || 'Tour',
              subtitle: s.description || '',
              icon: 'map',
            });
          }
          break;
        }
        case 'CRUISE': {
          if (s.cruise?.embarkDate) {
            entries.push({
              ...base,
              date: s.cruise.embarkDate.toISOString().split('T')[0],
              time: s.cruise.embarkDate.toTimeString().slice(0, 5),
              type: 'CRUISE',
              title: `Cruise: ${s.cruise.cruiseName || ''}`,
              subtitle: s.cruise.shipName || '',
              icon: 'ship',
            });
          }
          break;
        }
        case 'VISA': {
          if (s.visa?.issueDate) {
            entries.push({
              ...base,
              date: s.visa.issueDate.toISOString().split('T')[0],
              time: '',
              type: 'VISA',
              title: `Visa: ${s.visa.country || ''}`,
              subtitle: `${s.visa.visaType || ''} ${s.visa.entryType === 'MULTIPLE' ? '(Multiple entry)' : '(Single entry)'}`,
              icon: 'visa',
            });
          }
          break;
        }
        case 'INSURANCE': {
          if (s.insurance?.startDate) {
            entries.push({
              ...base,
              date: s.insurance.startDate.toISOString().split('T')[0],
              time: '',
              type: 'INSURANCE',
              title: `Insurance: ${s.insurance.company || ''}`,
              subtitle: `Policy ${s.insurance.policyNumber || ''}`,
              icon: 'shield',
            });
          }
          break;
        }
        case 'CAR_RENTAL': {
          if (s.carRental?.pickupTime) {
            entries.push({
              ...base,
              date: s.carRental.pickupTime.toISOString().split('T')[0],
              time: s.carRental.pickupTime.toTimeString().slice(0, 5),
              type: 'CAR_RENTAL',
              title: `Car Rental: ${s.carRental.company || ''}`,
              subtitle: s.carRental.carType || '',
              icon: 'car',
            });
          }
          break;
        }
        case 'TRAIN': {
          if (s.train?.departureTime) {
            entries.push({
              ...base,
              date: s.train.departureTime.toISOString().split('T')[0],
              time: s.train.departureTime.toTimeString().slice(0, 5),
              type: 'TRAIN',
              title: `Train ${s.train.trainNumber || ''}`,
              subtitle: `${s.train.departureStation || ''} → ${s.train.arrivalStation || ''}`,
              icon: 'train',
            });
          }
          break;
        }
      }
    }

    entries.sort((a, b) => {
      if (a.date !== b.date) return a.date.localeCompare(b.date);
      if (a.time !== b.time) return a.time.localeCompare(b.time);
      return a.sortOrder - b.sortOrder;
    });

    return entries;
  }
}
