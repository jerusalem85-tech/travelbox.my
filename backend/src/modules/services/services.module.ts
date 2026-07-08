import { Module } from '@nestjs/common';
import { FlightsController } from './flights/flights.controller';
import { FlightsService } from './flights/flights.service';
import { HotelsController } from './hotels/hotels.controller';
import { HotelsService } from './hotels/hotels.service';
import { TransfersController } from './transfers/transfers.controller';
import { TransfersService } from './transfers/transfers.service';
import { VisaController } from './visa/visa.controller';
import { VisaService } from './visa/visa.service';
import { InsuranceController } from './insurance/insurance.controller';
import { InsuranceService } from './insurance/insurance.service';
import { ActivitiesController } from './activities/activities.controller';
import { ActivitiesService } from './activities/activities.service';

@Module({
  controllers: [
    FlightsController,
    HotelsController,
    TransfersController,
    VisaController,
    InsuranceController,
    ActivitiesController,
  ],
  providers: [
    FlightsService,
    HotelsService,
    TransfersService,
    VisaService,
    InsuranceService,
    ActivitiesService,
  ],
  exports: [
    FlightsService,
    HotelsService,
    TransfersService,
    VisaService,
    InsuranceService,
    ActivitiesService,
  ],
})
export class ServicesModule {}
