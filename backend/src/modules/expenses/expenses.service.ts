import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateExpenseDto } from './dto/create-expense.dto';
import { UpdateExpenseDto } from './dto/update-expense.dto';

@Injectable()
export class ExpensesService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateExpenseDto, userId: string) {
    return this.prisma.expense.create({
      data: { ...dto, createdById: userId } as any,
    });
  }

  async findAll() {
    return this.prisma.expense.findMany({
      orderBy: { createdAt: 'desc' },
      include: { createdBy: { select: { id: true, firstName: true, lastName: true } } },
    });
  }

  async findOne(id: string) {
    const expense = await this.prisma.expense.findUnique({
      where: { id },
      include: { createdBy: { select: { id: true, firstName: true, lastName: true } } },
    });
    if (!expense) throw new NotFoundException('Expense not found');
    return expense;
  }

  async update(id: string, dto: UpdateExpenseDto) {
    await this.findOne(id);
    return this.prisma.expense.update({ where: { id }, data: dto as any });
  }

  async remove(id: string) {
    await this.findOne(id);
    await this.prisma.expense.delete({ where: { id } });
    return { deleted: true };
  }
}
