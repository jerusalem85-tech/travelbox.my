import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class PassengersService {
  constructor(private prisma: PrismaService) {}

  async create(tripId: string, data: any) {
    return this.prisma.passenger.create({ data: { tripId, ...data } });
  }

  async bulkCreate(tripId: string, passengers: any[]) {
    return this.prisma.passenger.createMany({ data: passengers.map((p) => ({ tripId, ...p })) });
  }

  async findByTrip(tripId: string) {
    return this.prisma.passenger.findMany({ where: { tripId }, orderBy: { createdAt: 'asc' } });
  }

  async findById(id: string) {
    const passenger = await this.prisma.passenger.findUnique({ where: { id }, include: { trip: true, flightSegments: { include: { flight: true } }, hotelGuests: { include: { hotel: true } }, documents: true } });
    if (!passenger) throw new NotFoundException('Passenger not found');
    return passenger;
  }

  async update(id: string, data: any) {
    return this.prisma.passenger.update({ where: { id }, data });
  }

  async remove(id: string) {
    await this.prisma.passenger.delete({ where: { id } });
    return { deleted: true };
  }
}
