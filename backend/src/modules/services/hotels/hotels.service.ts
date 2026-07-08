import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../core/database/prisma.service';
import { CreateHotelDto, UpdateHotelDto } from './dto/create-hotel.dto';

@Injectable()
export class HotelsService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateHotelDto) {
    return this.prisma.hotel.create({
      data: { ...dto, checkIn: new Date(dto.checkIn), checkOut: new Date(dto.checkOut), costPrice: dto.costPrice || 0, sellPrice: dto.sellPrice || 0 },
    });
  }

  async findByTrip(tripId: string) {
    return this.prisma.hotel.findMany({ where: { tripId }, include: { supplier: { select: { id: true, name: true } } }, orderBy: { checkIn: 'asc' } });
  }

  async findById(id: string) {
    const hotel = await this.prisma.hotel.findUnique({ where: { id } });
    if (!hotel) throw new NotFoundException('Hotel not found');
    return hotel;
  }

  async update(id: string, dto: UpdateHotelDto) {
    await this.findById(id);
    return this.prisma.hotel.update({
      where: { id },
      data: { ...dto, checkIn: dto.checkIn ? new Date(dto.checkIn) : undefined, checkOut: dto.checkOut ? new Date(dto.checkOut) : undefined },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.hotel.delete({ where: { id } });
    return { deleted: true };
  }
}
