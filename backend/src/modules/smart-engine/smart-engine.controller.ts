import { Controller, Post, Get, Param, Query, UseGuards, Body } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { SmartEngineService } from './smart-engine.service';
import { TimelineEngine } from './timeline.engine';
import { LocationEngine } from './location.engine';
import { DateValidationEngine } from './date-validation.engine';
import { CityEngine } from './city.engine';
import { FinancialEngine } from './financial.engine';
import { WorkflowEngine } from './workflow.engine';

@ApiTags('Smart Engine')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'))
@Controller('smart-engine')
export class SmartEngineController {
  constructor(
    private service: SmartEngineService,
    private timeline: TimelineEngine,
    private location: LocationEngine,
    private dateValidation: DateValidationEngine,
    private city: CityEngine,
    private financial: FinancialEngine,
    private workflow: WorkflowEngine,
  ) {}

  @Post('validate/:tripId')
  @ApiOperation({ summary: 'Validate a trip and detect issues' })
  validate(@Param('tripId') tripId: string) {
    return this.service.validateTrip(tripId);
  }

  @Get('analyze/:tripId')
  @ApiOperation({ summary: 'Full trip analysis with summary' })
  analyze(@Param('tripId') tripId: string) {
    return this.service.analyze(tripId);
  }

  @Get('timeline/:tripId')
  @ApiOperation({ summary: 'Build chronological timeline for a trip' })
  getTimeline(@Param('tripId') tripId: string) {
    return this.timeline.buildTimeline(tripId);
  }

  @Get('location/:tripId')
  @ApiOperation({ summary: 'Get current traveler location and next move' })
  getLocation(@Param('tripId') tripId: string) {
    return this.location.getCurrentLocation(tripId);
  }

  @Get('validations/:tripId')
  @ApiOperation({ summary: 'Get all date/location validation warnings' })
  getValidations(@Param('tripId') tripId: string) {
    return this.dateValidation.validate(tripId);
  }

  @Get('cities/:tripId')
  @ApiOperation({ summary: 'Get city/country analysis for a trip' })
  getCities(@Param('tripId') tripId: string) {
    return this.city.analyze(tripId);
  }

  @Get('financial/:tripId')
  @ApiOperation({ summary: 'Get financial summary for a trip' })
  getFinancial(@Param('tripId') tripId: string) {
    return this.financial.calculate(tripId);
  }

  @Post('workflow/:tripId/transition')
  @ApiOperation({ summary: 'Transition trip to next workflow stage' })
  transition(@Param('tripId') tripId: string, @Body('toStage') toStage: string) {
    return this.workflow.transition(tripId, toStage);
  }

  @Get('workflow/:tripId/available')
  @ApiOperation({ summary: 'Get available workflow transitions' })
  availableTransitions(@Param('tripId') tripId: string) {
    return this.workflow.getAvailableTransitions(tripId);
  }
}
