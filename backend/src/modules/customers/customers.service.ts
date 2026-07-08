import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateCustomerDto, UpdateCustomerDto } from './dto/create-customer.dto';

@Injectable()
export class CustomersService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateCustomerDto) {
    return this.prisma.customer.create({
      data: {
        ...dto,
        passportExpiry: dto.passportExpiry ? new Date(dto.passportExpiry) : undefined,
        dob: dto.dob ? new Date(dto.dob) : undefined,
        tenantId: 'default',
      },
    });
  }

  async findAll() {
    return this.prisma.customer.findMany({
      where: { deletedAt: null },
      orderBy: { createdAt: 'desc' },
    });
  }

  async findById(id: string) {
    const customer = await this.prisma.customer.findUnique({
      where: { id },
      include: { trips: { include: { trip: true } }, passengers: true },
    });
    if (!customer || customer.deletedAt) throw new NotFoundException('Customer not found');
    return customer;
  }

  async update(id: string, dto: UpdateCustomerDto) {
    await this.findById(id);
    return this.prisma.customer.update({
      where: { id },
      data: {
        ...dto,
        passportExpiry: dto.passportExpiry ? new Date(dto.passportExpiry) : undefined,
        dob: dto.dob ? new Date(dto.dob) : undefined,
      },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.customer.update({
      where: { id },
      data: { deletedAt: new Date() },
    });
    return { deleted: true };
  }

  async search(query: string) {
    return this.prisma.customer.findMany({
      where: {
        deletedAt: null,
        OR: [
          { firstName: { contains: query, mode: 'insensitive' } },
          { lastName: { contains: query, mode: 'insensitive' } },
          { email: { contains: query, mode: 'insensitive' } },
          { phone: { contains: query, mode: 'insensitive' } },
        ],
      },
      take: 20,
      orderBy: { createdAt: 'desc' },
    });
  }
}
