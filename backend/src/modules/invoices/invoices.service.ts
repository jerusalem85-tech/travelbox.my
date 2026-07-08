import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateInvoiceDto, UpdateInvoiceDto } from './dto/create-invoice.dto';

@Injectable()
export class InvoicesService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateInvoiceDto) {
    const invoiceNo = await this.generateInvoiceNo(dto.documentType || 'INVOICE');
    const taxRate = dto.taxRate || 0;
    const discountPct = dto.discountPct || 0;
    const subtotal = dto.subtotal;
    const discountAmount = (subtotal * discountPct) / 100;
    const taxAmount = ((subtotal - discountAmount) * taxRate) / 100;
    const totalAmount = subtotal - discountAmount + taxAmount;

    return this.prisma.invoice.create({
      data: {
        tripId: dto.tripId,
        invoiceNo,
        documentType: dto.documentType || 'INVOICE',
        customerId: dto.customerId,
        issueDate: dto.issueDate ? new Date(dto.issueDate) : new Date(),
        dueDate: dto.dueDate ? new Date(dto.dueDate) : undefined,
        subtotal,
        taxRate,
        taxAmount,
        discountPct,
        discountAmount,
        totalAmount,
        balanceDue: totalAmount,
        currency: dto.currency || 'USD',
        notes: dto.notes,
        terms: dto.terms,
      },
      include: {
        trip: { select: { id: true, referenceNo: true } },
        customer: { select: { id: true, firstName: true, lastName: true } },
      },
    });
  }

  async findAll(tripId?: string, status?: string) {
    return this.prisma.invoice.findMany({
      where: {
        deletedAt: null,
        ...(tripId ? { tripId } : {}),
        ...(status ? { status: status as any } : {}),
      },
      include: {
        trip: { select: { id: true, referenceNo: true } },
        customer: { select: { id: true, firstName: true, lastName: true } },
      },
      orderBy: { issueDate: 'desc' },
    });
  }

  async findById(id: string) {
    const invoice = await this.prisma.invoice.findUnique({
      where: { id },
      include: {
        trip: {
          select: {
            id: true, referenceNo: true, name: true,
            flights: true, hotels: true, transfers: true,
            visas: true, insurances: true, activities: true,
          },
        },
        customer: { select: { id: true, firstName: true, lastName: true, email: true, phone: true } },
      },
    });
    if (!invoice || invoice.deletedAt) throw new NotFoundException('Invoice not found');
    return invoice;
  }

  async update(id: string, dto: UpdateInvoiceDto) {
    await this.findById(id);
    const data: any = { ...dto };
    if (dto.dueDate) data.dueDate = new Date(dto.dueDate);
    if (dto.paidDate) data.paidDate = new Date(dto.paidDate);

    if (dto.amountPaid !== undefined) {
      const invoice = await this.prisma.invoice.findUnique({ where: { id } });
      data.amountPaid = dto.amountPaid;
      data.balanceDue = Number(invoice!.totalAmount) - dto.amountPaid;
    }

    return this.prisma.invoice.update({ where: { id }, data });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.invoice.update({ where: { id }, data: { deletedAt: new Date(), status: 'CANCELLED' } });
    return { deleted: true };
  }

  private async generateInvoiceNo(prefix: string): Promise<string> {
    const year = new Date().getFullYear();
    const abbr = prefix === 'INVOICE' ? 'INV' : prefix === 'QUOTATION' ? 'QT' : 'DOC';
    const last = await this.prisma.invoice.findFirst({
      where: { invoiceNo: { startsWith: `${abbr}-${year}-` } },
      orderBy: { invoiceNo: 'desc' },
    });
    let seq = 1;
    if (last) {
      const parts = last.invoiceNo.split('-');
      seq = parseInt(parts[2], 10) + 1;
    }
    return `${abbr}-${year}-${String(seq).padStart(4, '0')}`;
  }
}
