import { Module } from '@nestjs/common';
import { APP_FILTER, APP_GUARD } from '@nestjs/core';
import { CoreModule } from './core/core.module';
import { AuthModule } from './modules/auth/auth.module';
import { UsersModule } from './modules/users/users.module';
import { CustomersModule } from './modules/customers/customers.module';
import { SuppliersModule } from './modules/suppliers/suppliers.module';
import { PaymentsModule } from './modules/payments/payments.module';
import { TripsModule } from './modules/trips/trips.module';
import { ServicesModule } from './modules/services/services.module';
import { AccountingModule } from './modules/accounting/accounting.module';
import { InvoicesModule } from './modules/invoices/invoices.module';
import { NotesModule } from './modules/notes/notes.module';
import { TasksModule } from './modules/tasks/tasks.module';
import { TimelineModule } from './modules/timeline/timeline.module';
import { DocumentsModule } from './modules/documents/documents.module';
import { DashboardModule } from './modules/dashboard/dashboard.module';
import { JwtAuthGuard } from './core/guards/jwt-auth.guard';
import { HttpExceptionFilter } from './common/filters/http-exception.filter';
import { PassportModule } from '@nestjs/passport';

@Module({
  imports: [
    PassportModule.register({ defaultStrategy: 'jwt' }),
    CoreModule,
    AuthModule,
    UsersModule,
    CustomersModule,
    SuppliersModule,
    PaymentsModule,
    AccountingModule,
    InvoicesModule,
    NotesModule,
    TasksModule,
    TimelineModule,
    DocumentsModule,
    TripsModule,
    ServicesModule,
    DashboardModule,
  ],
  providers: [
    { provide: APP_GUARD, useClass: JwtAuthGuard },
    { provide: APP_FILTER, useClass: HttpExceptionFilter },
  ],
})
export class AppModule {}
