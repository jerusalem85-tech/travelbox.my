import { Controller, Get, Query, UseGuards } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { DashboardService } from './dashboard.service';

@ApiTags('Dashboard')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'))
@Controller('dashboard')
export class DashboardController {
  constructor(private service: DashboardService) {}

  @Get('stats') getStats() { return this.service.getStats(); }
  @Get('upcoming-trips') getUpcomingTrips(@Query('limit') limit?: string) { return this.service.getUpcomingTrips(limit ? parseInt(limit) : 5); }
  @Get('recent-activity') getRecentActivity(@Query('limit') limit?: string) { return this.service.getRecentActivity(limit ? parseInt(limit) : 10); }
  @Get('monthly-stats') getMonthlyStats(@Query('year') year: string) { return this.service.getMonthlyStats(parseInt(year)); }
}
