import { Controller, Get, Post, Put, Delete, Body, Param, UseGuards } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { PassengersService } from './passengers.service';

@ApiTags('Passengers')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'))
@Controller('passengers')
export class PassengersController {
  constructor(private service: PassengersService) {}

  @Post()
  create(@Body() data: any) { return this.service.create(data.tripId, data); }

  @Post('bulk')
  bulkCreate(@Body() body: { tripId: string; passengers: any[] }) { return this.service.bulkCreate(body.tripId, body.passengers); }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) { return this.service.findByTrip(tripId); }

  @Get(':id')
  findById(@Param('id') id: string) { return this.service.findById(id); }

  @Put(':id')
  update(@Param('id') id: string, @Body() data: any) { return this.service.update(id, data); }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.service.remove(id); }
}
