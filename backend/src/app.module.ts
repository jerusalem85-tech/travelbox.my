import { Module } from '@nestjs/common';
import { ThrottlerModule } from '@nestjs/throttler';
import { PrismaModule } from './core/database/prisma.module';
import { AuthModule } from './modules/auth/auth.module';
import { TripsModule } from './modules/trips/trips.module';
import { PassengersModule } from './modules/passengers/passengers.module';
import { SuppliersModule } from './modules/suppliers/suppliers.module';
import { CrmModule } from './modules/crm/crm.module';
import { PaymentsModule } from './modules/payments/payments.module';
import { AccountingModule } from './modules/accounting/accounting.module';
import { DocumentsModule } from './modules/documents/documents.module';
import { TasksModule } from './modules/tasks/tasks.module';
import { NotificationsModule } from './modules/notifications/notifications.module';
import { ReportsModule } from './modules/reports/reports.module';
import { DashboardModule } from './modules/dashboard/dashboard.module';
import { SearchModule } from './modules/search/search.module';
import { AdminModule } from './modules/admin/admin.module';
import { SmartEngineModule } from './modules/smart-engine/smart-engine.module';

@Module({
  imports: [
    ThrottlerModule.forRoot([{ ttl: 60000, limit: 100 }]),
    PrismaModule,
    AuthModule,
    TripsModule,
    PassengersModule,
    SuppliersModule,
    CrmModule,
    PaymentsModule,
    AccountingModule,
    DocumentsModule,
    TasksModule,
    NotificationsModule,
    ReportsModule,
    DashboardModule,
    SearchModule,
    AdminModule,
    SmartEngineModule,
  ],
})
export class AppModule {}
