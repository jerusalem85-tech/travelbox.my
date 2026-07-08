import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { PassengersService } from './passengers.service';
import { CreatePassengerDto, UpdatePassengerDto } from './dto/create-passenger.dto';

@Controller('passengers')
export class PassengersController {
  constructor(private passengers: PassengersService) {}

  @Post()
  create(@Body() dto: CreatePassengerDto) {
    return this.passengers.create(dto);
  }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) {
    return this.passengers.findByTrip(tripId);
  }

  @Get(':id')
  findById(@Param('id') id: string) {
    return this.passengers.findById(id);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdatePassengerDto) {
    return this.passengers.update(id, dto);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.passengers.remove(id);
  }
}
