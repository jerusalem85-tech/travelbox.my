import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateTripDto } from './dto/create-trip.dto';
import { UpdateTripDto } from './dto/update-trip.dto';

@Injectable()
export class TripsService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateTripDto) {
    return this.prisma.trip.create({ data: dto as any });
  }

  async findAll() {
    return this.prisma.trip.findMany({ orderBy: { createdAt: 'desc' } });
  }

  async findOne(id: string) {
    const trip = await this.prisma.trip.findUnique({ where: { id } });
    if (!trip) throw new NotFoundException('Trip not found');
    return trip;
  }

  async update(id: string, dto: UpdateTripDto) {
    await this.findOne(id);
    return this.prisma.trip.update({ where: { id }, data: dto as any });
  }

  async remove(id: string) {
    await this.findOne(id);
    await this.prisma.trip.delete({ where: { id } });
    return { deleted: true };
  }
}
