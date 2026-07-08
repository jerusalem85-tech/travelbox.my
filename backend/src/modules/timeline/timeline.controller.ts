import { Controller, Get, Param } from '@nestjs/common';
import { TimelineService } from './timeline.service';

@Controller('timeline')
export class TimelineController {
  constructor(private timeline: TimelineService) {}

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) {
    return this.timeline.findByTrip(tripId);
  }
}
