import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { InsuranceService } from './insurance.service';
import { CreateInsuranceDto, UpdateInsuranceDto } from './dto/create-insurance.dto';

@Controller('services/insurance')
export class InsuranceController {
  constructor(private insurance: InsuranceService) {}

  @Post()
  create(@Body() dto: CreateInsuranceDto) { return this.insurance.create(dto); }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) { return this.insurance.findByTrip(tripId); }

  @Get(':id')
  findById(@Param('id') id: string) { return this.insurance.findById(id); }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateInsuranceDto) { return this.insurance.update(id, dto); }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.insurance.remove(id); }
}
