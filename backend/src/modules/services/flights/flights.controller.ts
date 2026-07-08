import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { FlightsService } from './flights.service';
import { CreateFlightDto, UpdateFlightDto } from './dto/create-flight.dto';

@Controller('services/flights')
export class FlightsController {
  constructor(private flights: FlightsService) {}

  @Post()
  create(@Body() dto: CreateFlightDto) {
    return this.flights.create(dto);
  }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) {
    return this.flights.findByTrip(tripId);
  }

  @Get(':id')
  findById(@Param('id') id: string) {
    return this.flights.findById(id);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateFlightDto) {
    return this.flights.update(id, dto);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.flights.remove(id);
  }
}
