import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateInvoiceDto } from './dto/create-invoice.dto';
import { UpdateInvoiceDto } from './dto/update-invoice.dto';

@Injectable()
export class InvoicesService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateInvoiceDto, userId: string) {
    return this.prisma.invoice.create({
      data: { ...dto, createdById: userId } as any,
      include: { client: true, booking: true },
    });
  }

  async findAll() {
    return this.prisma.invoice.findMany({
      orderBy: { createdAt: 'desc' },
      include: { client: true, createdBy: { select: { id: true, firstName: true, lastName: true } } },
    });
  }

  async findOne(id: string) {
    const invoice = await this.prisma.invoice.findUnique({
      where: { id },
      include: { client: true, createdBy: { select: { id: true, firstName: true, lastName: true } } },
    });
    if (!invoice) throw new NotFoundException('Invoice not found');
    return invoice;
  }

  async update(id: string, dto: UpdateInvoiceDto) {
    await this.findOne(id);
    return this.prisma.invoice.update({
      where: { id },
      data: dto as any,
      include: { client: true },
    });
  }

  async remove(id: string) {
    await this.findOne(id);
    await this.prisma.invoice.delete({ where: { id } });
    return { deleted: true };
  }
}
