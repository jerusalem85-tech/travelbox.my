import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { VisaService } from './visa.service';
import { CreateVisaDto, UpdateVisaDto } from './dto/create-visa.dto';

@Controller('services/visa')
export class VisaController {
  constructor(private visa: VisaService) {}

  @Post()
  create(@Body() dto: CreateVisaDto) { return this.visa.create(dto); }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) { return this.visa.findByTrip(tripId); }

  @Get(':id')
  findById(@Param('id') id: string) { return this.visa.findById(id); }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateVisaDto) { return this.visa.update(id, dto); }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.visa.remove(id); }
}
