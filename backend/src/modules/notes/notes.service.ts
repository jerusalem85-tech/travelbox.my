import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateNoteDto, UpdateNoteDto } from './dto/create-note.dto';

@Injectable()
export class NotesService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateNoteDto, userId: string) {
    return this.prisma.note.create({
      data: { tripId: dto.tripId, authorId: userId, content: dto.content, isPinned: dto.isPinned },
      include: { author: { select: { id: true, firstName: true, lastName: true } } },
    });
  }

  async findByTrip(tripId: string) {
    return this.prisma.note.findMany({
      where: { tripId },
      include: { author: { select: { id: true, firstName: true, lastName: true } } },
      orderBy: [{ isPinned: 'desc' }, { createdAt: 'desc' }],
    });
  }

  async update(id: string, dto: UpdateNoteDto) {
    const note = await this.prisma.note.findUnique({ where: { id } });
    if (!note) throw new NotFoundException('Note not found');
    return this.prisma.note.update({ where: { id }, data: dto });
  }

  async remove(id: string) {
    const note = await this.prisma.note.findUnique({ where: { id } });
    if (!note) throw new NotFoundException('Note not found');
    await this.prisma.note.delete({ where: { id } });
    return { deleted: true };
  }
}
