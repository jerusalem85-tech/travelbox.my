import { Controller, Get, Post, Put, Delete, Body, Param, Query, UseGuards } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { TripsService } from './trips.service';
import { CreateTripDto, UpdateTripDto, QueryTripDto, CreateServiceDto, UpdateServiceDto } from './trip.dto';
import { CurrentUser } from '../../core/decorators/current-user.decorator';
import { SmartEngineService } from '../smart-engine/smart-engine.service';

@ApiTags('Trips')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'))
@Controller('trips')
export class TripsController {
  constructor(
    private tripsService: TripsService,
    private smartEngine: SmartEngineService,
  ) {}

  @Post()
  @ApiOperation({ summary: 'Create a new trip' })
  create(@Body() dto: CreateTripDto, @CurrentUser('id') userId: string) {
    return this.tripsService.create(dto, userId);
  }

  @Get()
  @ApiOperation({ summary: 'List trips with filtering and pagination' })
  findAll(@Query() query: QueryTripDto) {
    return this.tripsService.findAll(query);
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get trip details with all relations' })
  findById(@Param('id') id: string) {
    return this.tripsService.findById(id);
  }

  @Put(':id')
  @ApiOperation({ summary: 'Update trip' })
  update(@Param('id') id: string, @Body() dto: UpdateTripDto) {
    return this.tripsService.update(id, dto);
  }

  @Delete(':id')
  @ApiOperation({ summary: 'Delete trip' })
  remove(@Param('id') id: string) {
    return this.tripsService.remove(id);
  }

  @Post(':id/validate')
  @ApiOperation({ summary: 'Validate trip with Smart Engine' })
  validate(@Param('id') id: string) {
    return this.smartEngine.validateTrip(id);
  }

  @Post(':id/generate-timeline')
  @ApiOperation({ summary: 'Auto-generate trip timeline from services' })
  generateTimeline(@Param('id') id: string) {
    return this.tripsService.updateTimeline(id);
  }

  @Post(':id/recalculate')
  @ApiOperation({ summary: 'Recalculate trip finances' })
  recalculate(@Param('id') id: string) {
    return this.tripsService.recalculateFinances(id);
  }

  // --- Service Management (unified, replacing standalone flight/hotel/transfer/etc modules) ---

  @Get(':id/services')
  @ApiOperation({ summary: 'List all services for a trip' })
  listServices(@Param('id') id: string) {
    return this.tripsService.findServicesByTrip(id);
  }

  @Post(':id/services')
  @ApiOperation({ summary: 'Add a service to a trip' })
  addService(@Param('id') id: string, @Body() dto: CreateServiceDto) {
    return this.tripsService.createService(id, dto);
  }

  @Put(':id/services/:serviceId')
  @ApiOperation({ summary: 'Update a service' })
  updateService(@Param('serviceId') serviceId: string, @Body() dto: UpdateServiceDto) {
    return this.tripsService.updateService(serviceId, dto);
  }

  @Delete(':id/services/:serviceId')
  @ApiOperation({ summary: 'Remove a service from a trip' })
  removeService(@Param('id') id: string, @Param('serviceId') serviceId: string) {
    return this.tripsService.removeService(id, serviceId);
  }

  @Put(':id/services/reorder')
  @ApiOperation({ summary: 'Reorder services and rebuild chain' })
  reorderServices(@Param('id') id: string, @Body('serviceIds') serviceIds: string[]) {
    return this.tripsService.reorderServices(id, serviceIds);
  }
}
