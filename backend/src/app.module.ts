import { Module } from '@nestjs/common';
import { PrismaModule } from './core/database/prisma.module';
import { AuthModule } from './modules/auth/auth.module';
import { UsersModule } from './modules/users/users.module';
import { DashboardModule } from './modules/dashboard/dashboard.module';
import { ClientsModule } from './modules/clients/clients.module';
import { TripsModule } from './modules/trips/trips.module';
import { BookingsModule } from './modules/bookings/bookings.module';
import { InvoicesModule } from './modules/invoices/invoices.module';
import { ExpensesModule } from './modules/expenses/expenses.module';
import { SuppliersModule } from './modules/suppliers/suppliers.module';

@Module({
  imports: [PrismaModule, AuthModule, UsersModule, DashboardModule, ClientsModule, TripsModule, BookingsModule, InvoicesModule, ExpensesModule, SuppliersModule],
})
export class AppModule {}
