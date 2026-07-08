import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../core/database/prisma.service';
import { CreateFlightDto, UpdateFlightDto } from './dto/create-flight.dto';

@Injectable()
export class FlightsService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateFlightDto) {
    return this.prisma.flight.create({
      data: {
        ...dto,
        departureDate: new Date(dto.departureDate),
        arrivalDate: new Date(dto.arrivalDate),
        costPrice: dto.costPrice || 0,
        sellPrice: dto.sellPrice || 0,
      },
    });
  }

  async findByTrip(tripId: string) {
    return this.prisma.flight.findMany({
      where: { tripId },
      include: { supplier: { select: { id: true, name: true } } },
      orderBy: { departureDate: 'asc' },
    });
  }

  async findById(id: string) {
    const flight = await this.prisma.flight.findUnique({ where: { id } });
    if (!flight) throw new NotFoundException('Flight not found');
    return flight;
  }

  async update(id: string, dto: UpdateFlightDto) {
    await this.findById(id);
    return this.prisma.flight.update({
      where: { id },
      data: {
        ...dto,
        departureDate: dto.departureDate ? new Date(dto.departureDate) : undefined,
        arrivalDate: dto.arrivalDate ? new Date(dto.arrivalDate) : undefined,
      },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.flight.delete({ where: { id } });
    return { deleted: true };
  }
}
