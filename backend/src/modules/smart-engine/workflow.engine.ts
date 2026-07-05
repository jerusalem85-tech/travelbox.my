import { Injectable, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

export const WORKFLOW_ORDER: Record<string, number> = {
  LEAD: 0,
  QUOTATION: 1,
  NEGOTIATION: 2,
  CONFIRMED: 3,
  DEPOSIT_PAID: 4,
  BOOKED: 5,
  TICKETED: 6,
  DOCUMENTS_SENT: 7,
  TRAVELING: 8,
  COMPLETED: 9,
  ARCHIVED: 10,
};

export const WORKFLOW_STAGES = Object.keys(WORKFLOW_ORDER);

interface TransitionRule {
  from: string;
  to: string;
  requires: string[];
}

const TRANSITION_RULES: TransitionRule[] = [
  { from: 'LEAD', to: 'QUOTATION', requires: ['customerId'] },
  { from: 'QUOTATION', to: 'NEGOTIATION', requires: [] },
  { from: 'NEGOTIATION', to: 'CONFIRMED', requires: ['startDate', 'endDate'] },
  { from: 'CONFIRMED', to: 'DEPOSIT_PAID', requires: [] },
  { from: 'DEPOSIT_PAID', to: 'BOOKED', requires: [] },
  { from: 'BOOKED', to: 'TICKETED', requires: [] },
  { from: 'TICKETED', to: 'DOCUMENTS_SENT', requires: [] },
  { from: 'DOCUMENTS_SENT', to: 'TRAVELING', requires: [] },
  { from: 'TRAVELING', to: 'COMPLETED', requires: [] },
  { from: 'COMPLETED', to: 'ARCHIVED', requires: [] },
];

@Injectable()
export class WorkflowEngine {
  constructor(private prisma: PrismaService) {}

  async canTransition(tripId: string, toStage: string): Promise<{ allowed: boolean; reason?: string }> {
    const trip = await this.prisma.trip.findUnique({ where: { id: tripId } });
    if (!trip) return { allowed: false, reason: 'Trip not found' };

    const currentOrder = WORKFLOW_ORDER[trip.status];
    const targetOrder = WORKFLOW_ORDER[toStage];

    if (targetOrder === undefined) {
      return { allowed: false, reason: `Invalid stage: ${toStage}` };
    }

    if (Math.abs(targetOrder - currentOrder) !== 1) {
      return { allowed: false, reason: `Cannot jump from ${trip.status} to ${toStage}. Must move one step at a time.` };
    }

    const rule = TRANSITION_RULES.find(r => r.from === trip.status && r.to === toStage);
    if (rule) {
      for (const field of rule.requires) {
        if ((trip as any)[field] === null || (trip as any)[field] === undefined) {
          return { allowed: false, reason: `Cannot transition to ${toStage}: ${field} is required.` };
        }
      }
    }

    return { allowed: true };
  }

  async transition(tripId: string, toStage: string): Promise<any> {
    const check = await this.canTransition(tripId, toStage);
    if (!check.allowed) {
      throw new BadRequestException(check.reason);
    }

    const trip = await this.prisma.trip.update({
      where: { id: tripId },
      data: { status: toStage as any },
    });

    await this.prisma.auditLog.create({
      data: {
        action: 'WORKFLOW_TRANSITION',
        module: 'trips',
        entityId: tripId,
        entityType: 'Trip',
        newValues: { status: toStage },
      },
    });

    return trip;
  }

  async getAvailableTransitions(tripId: string): Promise<string[]> {
    const trip = await this.prisma.trip.findUnique({ where: { id: tripId } });
    if (!trip) return [];

    const currentOrder = WORKFLOW_ORDER[trip.status];
    const available: string[] = [];

    for (const stage of WORKFLOW_STAGES) {
      const stageOrder = WORKFLOW_ORDER[stage];
      if (Math.abs(stageOrder - currentOrder) === 1) {
        available.push(stage);
      }
    }

    return available;
  }
}
