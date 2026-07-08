import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreatePassengerDto, UpdatePassengerDto } from './dto/create-passenger.dto';

@Injectable()
export class PassengersService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreatePassengerDto) {
    return this.prisma.passenger.create({
      data: {
        ...dto,
        dob: dto.dob ? new Date(dto.dob) : undefined,
        passportExpiry: dto.passportExpiry ? new Date(dto.passportExpiry) : undefined,
      },
    });
  }

  async findByTrip(tripId: string) {
    return this.prisma.passenger.findMany({
      where: { tripId },
      orderBy: { createdAt: 'asc' },
    });
  }

  async findById(id: string) {
    const passenger = await this.prisma.passenger.findUnique({ where: { id } });
    if (!passenger) throw new NotFoundException('Passenger not found');
    return passenger;
  }

  async update(id: string, dto: UpdatePassengerDto) {
    await this.findById(id);
    return this.prisma.passenger.update({
      where: { id },
      data: {
        ...dto,
        dob: dto.dob ? new Date(dto.dob) : undefined,
        passportExpiry: dto.passportExpiry ? new Date(dto.passportExpiry) : undefined,
      },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.passenger.delete({ where: { id } });
    return { deleted: true };
  }
}
