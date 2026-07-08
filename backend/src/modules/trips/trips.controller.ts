import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { TripsService } from './trips.service';
import { CreateTripDto, UpdateTripDto, ChangeStatusDto } from './dto/create-trip.dto';
import { CurrentUser } from '../../common/decorators/current-user.decorator';

@Controller('trips')
export class TripsController {
  constructor(private trips: TripsService) {}

  @Post()
  create(@Body() dto: CreateTripDto, @CurrentUser('id') userId: string) {
    return this.trips.create(dto, userId);
  }

  @Get()
  findAll() {
    return this.trips.findAll();
  }

  @Get(':id')
  findById(@Param('id') id: string) {
    return this.trips.findById(id);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateTripDto) {
    return this.trips.update(id, dto);
  }

  @Patch(':id/status')
  changeStatus(
    @Param('id') id: string,
    @Body() dto: ChangeStatusDto,
    @CurrentUser('id') userId: string,
  ) {
    return this.trips.changeStatus(id, dto, userId);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.trips.remove(id);
  }

  @Post(':id/customers/:customerId')
  addCustomer(
    @Param('id') tripId: string,
    @Param('customerId') customerId: string,
    @Body('isPrimary') isPrimary?: boolean,
    @Body('role') role?: string,
  ) {
    return this.trips.addCustomer(tripId, customerId, isPrimary, role);
  }

  @Get(':id/profit')
  getProfitSummary(@Param('id') id: string) {
    return this.trips.getProfitSummary(id);
  }
}
