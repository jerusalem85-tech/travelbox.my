import { Controller, Get, Query, UseGuards } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { ReportsService } from './reports.service';

@ApiTags('Reports')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'))
@Controller('reports')
export class ReportsController {
  constructor(private service: ReportsService) {}

  @Get('sales') getSalesReport(@Query('startDate') startDate: string, @Query('endDate') endDate: string) { return this.service.getSalesReport(startDate, endDate); }
  @Get('services') getServiceBreakdown(@Query('startDate') startDate: string, @Query('endDate') endDate: string) { return this.service.getServiceBreakdown(startDate, endDate); }
  @Get('monthly-revenue') getMonthlyRevenue(@Query('year') year: string) { return this.service.getMonthlyRevenue(parseInt(year)); }
  @Get('top-customers') getTopCustomers(@Query('limit') limit?: string) { return this.service.getTopCustomers(limit ? parseInt(limit) : 10); }
  @Get('supplier-performance') getSupplierPerformance(@Query('startDate') startDate: string, @Query('endDate') endDate: string) { return this.service.getSupplierPerformance(startDate, endDate); }
}
