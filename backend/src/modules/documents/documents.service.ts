import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateDocumentDto } from './dto/create-document.dto';
import { DocumentStatus, DocumentType } from '@prisma/client';

@Injectable()
export class DocumentsService {
  constructor(private prisma: PrismaService) {}

  async generate(tripId: string, dto: CreateDocumentDto, userId: string) {
    const trip = await this.prisma.trip.findUnique({
      where: { id: tripId },
      include: {
        createdBy: { select: { id: true, firstName: true, lastName: true, email: true } },
        assignedTo: { select: { id: true, firstName: true, lastName: true, email: true } },
        customers: { include: { customer: true } },
        passengers: true,
        flights: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
        hotels: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
        transfers: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
        visas: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
        insurances: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
        activities: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
      },
    });
    if (!trip || trip.deletedAt) throw new NotFoundException('Trip not found');

    const documentNo = await this.generateDocumentNo(dto.documentType);

    const doc = await this.prisma.document.create({
      data: {
        tenantId: 'default',
        tripId,
        documentType: dto.documentType,
        documentNo,
        title: this.generateTitle(dto.documentType, trip),
        status: 'DRAFT',
        generatedById: userId,
      },
    });

    return { ...doc, trip };
  }

  async findAll(tripId?: string, documentType?: string, status?: string) {
    return this.prisma.document.findMany({
      where: {
        deletedAt: null,
        ...(tripId ? { tripId } : {}),
        ...(documentType ? { documentType: documentType as DocumentType } : {}),
        ...(status ? { status: status as DocumentStatus } : {}),
      },
      include: {
        trip: { select: { id: true, referenceNo: true, name: true } },
        generatedBy: { select: { id: true, firstName: true, lastName: true } },
      },
      orderBy: { createdAt: 'desc' },
    });
  }

  async findById(id: string) {
    const doc = await this.prisma.document.findUnique({
      where: { id },
      include: {
        trip: {
          include: {
            createdBy: { select: { id: true, firstName: true, lastName: true, email: true } },
            assignedTo: { select: { id: true, firstName: true, lastName: true, email: true } },
            customers: { include: { customer: true } },
            passengers: true,
            flights: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
            hotels: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
            transfers: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
            visas: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
            insurances: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
            activities: { include: { supplier: { select: { id: true, name: true, phone: true, email: true } } } },
          },
        },
        generatedBy: { select: { id: true, firstName: true, lastName: true } },
      },
    });
    if (!doc || doc.deletedAt) throw new NotFoundException('Document not found');
    return doc;
  }

  async updateStatus(id: string, status: DocumentStatus) {
    const doc = await this.prisma.document.findUnique({ where: { id } });
    if (!doc || doc.deletedAt) throw new NotFoundException('Document not found');
    return this.prisma.document.update({
      where: { id },
      data: { status, sentAt: status === 'SENT' ? new Date() : undefined },
    });
  }

  async remove(id: string) {
    const doc = await this.prisma.document.findUnique({ where: { id } });
    if (!doc || doc.deletedAt) throw new NotFoundException('Document not found');
    await this.prisma.document.update({ where: { id }, data: { deletedAt: new Date() } });
    return { deleted: true };
  }

  private async generateDocumentNo(type: DocumentType): Promise<string> {
    const prefix = type === 'INVOICE' ? 'INV' : type === 'QUOTATION' ? 'QT' : type === 'ITINERARY' ? 'IT' : 'DOC';
    const year = new Date().getFullYear();
    const last = await this.prisma.document.findFirst({
      where: { documentNo: { startsWith: `${prefix}-${year}-` } },
      orderBy: { documentNo: 'desc' },
    });
    let seq = 1;
    if (last) {
      const parts = last.documentNo.split('-');
      seq = parseInt(parts[2], 10) + 1;
    }
    return `${prefix}-${year}-${String(seq).padStart(4, '0')}`;
  }

  private generateTitle(type: DocumentType, trip: any): string {
    const typeMap: Record<DocumentType, string> = {
      INVOICE: 'فاتورة',
      QUOTATION: 'عرض سعر',
      PROPOSAL: 'اقتراح',
      RECEIPT: 'سند قبض',
      VOUCHER: 'قسيمة',
      ITINERARY: 'برنامج الرحلة',
      TICKET: 'تذكرة',
      CERTIFICATE: 'شهادة',
      CONTRACT: 'عقد',
    };
    return `${typeMap[type]} - ${trip.referenceNo}`;
  }
}
